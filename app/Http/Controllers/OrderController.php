<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductSize;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Tạo đơn hàng từ giỏ hàng (tích hợp với Checkout.tsx)
     */
    public function createOrderFromCart(Request $request): JsonResponse
    {
        $request->validate([
            'shipping_info.fullName' => 'required|string|min:2',
            'shipping_info.email' => 'required|email',
            'shipping_info.phone' => 'required|regex:/^[0-9]{10,11}$/',
            'shipping_info.address' => 'required|string|min:5',
            'coupon_code' => 'nullable|string',
            'payment_method' => ['required', Rule::in(['cod', 'momo', 'vnpay'])],
        ]);

        $user = Auth::user();
        $shippingInfo = $request->input('shipping_info');
        $couponCode = $request->input('coupon_code');
        $paymentMethod = $request->input('payment_method');

        // Lấy giỏ hàng của user
        $cart = Cart::where('user_id', $user->id)->first();
        if (!$cart) {
            return response()->json(['message' => 'Giỏ hàng của bạn đang trống!'], 400);
        }

        $cartItems = CartItem::where('cart_id', $cart->id)->with('product')->get();
        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Không có sản phẩm nào trong giỏ hàng!'], 400);
        }

        return DB::transaction(function () use ($request, $user, $cartItems, $shippingInfo, $couponCode, $paymentMethod, $cart) {
            // Tính tổng tiền trước giảm giá
            $totalPriceBeforeDiscount = $cartItems->sum(function ($item) {
                return ($item->discounted_price ?? $item->original_price) * $item->quantity;
            });

            // Mặc định phí vận chuyển
            $shippingFee = 30000;
            $promotionAmount = 0;

            // Kiểm tra mã khuyến mãi nếu có
            if ($couponCode) {
                $promotion = DB::table('promotions')
                    ->where('promotion_name', $couponCode)
                    ->where('status', 1)
                    ->whereDate('start_date', '<=', now())
                    ->whereDate('end_date', '>=', now())
                    ->first();

                if ($promotion) {
                    if ($promotion->discount_type === 'Giảm theo %') {
                        $promotionAmount = ($totalPriceBeforeDiscount * $promotion->discount_value) / 100;
                        if (!empty($promotion->max_discount_value)) {
                            $promotionAmount = min($promotionAmount, $promotion->max_discount_value);
                        }
                    } elseif ($promotion->discount_type === 'Giảm số tiền') {
                        $promotionAmount = min($promotion->discount_value, $totalPriceBeforeDiscount);
                    }
                } else {
                    return response()->json(['message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn!'], 400);
                }
            }

            // Tính tổng tiền cuối cùng
            $finalTotalPrice = max(($totalPriceBeforeDiscount - $promotionAmount) + $shippingFee, 0);

            // Tạo mã đơn hàng
            $orderCode = 'ORD' . strtoupper(Str::random(10));

            // Tạo đơn hàng
            $order = Order::create([
                'user_id' => $user->id,
                'order_code' => $orderCode,
                'recipient_name' => $shippingInfo['fullName'],
                'recipient_phone' => $shippingInfo['phone'],
                'recipient_address' => $shippingInfo['address'],
                'total_price' => $finalTotalPrice,
                'promotion' => $promotionAmount,
                'shipping_fee' => $shippingFee,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentMethod === 'cod' ? 'chua_thanh_toan' : 'da_thanh_toan',
                'status' => $paymentMethod === 'cod' ? 'cho_xac_nhan' : 'dang_chuan_bi',
            ]);

            // Lưu chi tiết đơn hàng và giảm tồn kho
            foreach ($cartItems as $item) {
                $productVariant = ProductVariant::where('product_id', $item->product_id)
                    ->where('product_size_id', $item->product_size_id)
                    ->lockForUpdate()
                    ->first();

                if (!$productVariant || $productVariant->quantity < $item->quantity) {
                    throw new \Exception("Sản phẩm {$item->product->product_name} (size {$item->product_size_id}) không đủ số lượng");
                }

                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $productVariant->id,
                    'quantity' => $item->quantity,
                    'price' => $item->discounted_price ?? $item->original_price,
                ]);

                // Giảm tồn kho ngay lập tức
                $productVariant->decrement('quantity', $item->quantity);
            }

            // Xóa giỏ hàng
            CartItem::where('cart_id', $cart->id)->delete();
            $cart->delete();

            return response()->json([
                'message' => 'Đặt hàng thành công!',
                'order_id' => $order->id,
                'order_code' => $orderCode,
                'payment_method' => $paymentMethod,
            ], 201);
        });
    }

    /**
     * Mua sản phẩm trực tiếp theo tên
     */
    public function buyProductByName(Request $request, $product_name): JsonResponse
    {
        Log::info('buyProductByName Request Data', $request->all());

        $request->validate([
            'product_size_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'promotion_name' => 'nullable|string',
            'payment_method' => ['required', Rule::in(['cod', 'momo', 'vnpay'])],
            'status' => ['required', Rule::in(array_keys(Order::ORDER_STATUS))],
        ]);

        $user = Auth::user();

        if (!$user->phone || !$user->address) {
            return response()->json(['message' => 'Vui lòng cập nhật số điện thoại và địa chỉ trước khi mua hàng!'], 400);
        }

        $product = Product::firstWhere('product_name', $product_name);
        if (!$product) {
            return response()->json(['message' => 'Sản phẩm không tồn tại!'], 404);
        }

        return DB::transaction(function () use ($request, $product, $user) {
            $productVariant = ProductVariant::where('product_id', $product->id)
                ->where('product_size_id', $request->product_size_id)
                ->lockForUpdate()
                ->first();

            if (!$productVariant) {
                return response()->json(['message' => 'Không tìm thấy biến thể sản phẩm!'], 404);
            }

            if ($productVariant->quantity < $request->quantity) {
                return response()->json(['message' => 'Kho không đủ hàng!'], 400);
            }

            $price = $product->discounted_price ?? $product->original_price;
            $totalPriceBeforeDiscount = $price * $request->quantity;

            $shippingFee = 30000;
            $promotionAmount = 0;

            if ($request->filled('promotion_name')) {
                $promotion = DB::table('promotions')
                    ->where('promotion_name', $request->promotion_name)
                    ->where('status', 1)
                    ->whereDate('start_date', '<=', now())
                    ->whereDate('end_date', '>=', now())
                    ->first();

                if ($promotion) {
                    if ($promotion->discount_type === 'Giảm theo %') {
                        $promotionAmount = ($totalPriceBeforeDiscount * $promotion->discount_value) / 100;
                        if (!empty($promotion->max_discount_value)) {
                            $promotionAmount = min($promotionAmount, $promotion->max_discount_value);
                        }
                    } elseif ($promotion->discount_type === 'Giảm số tiền') {
                        $promotionAmount = min($promotion->discount_value, $totalPriceBeforeDiscount);
                    }
                } else {
                    return response()->json(['message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn!'], 400);
                }
            }

            $finalTotalPrice = max(($totalPriceBeforeDiscount - $promotionAmount) + $shippingFee, 0);

            $orderCode = 'ORD' . strtoupper(Str::random(10));

            $order = Order::create([
                'user_id' => $user->id,
                'order_code' => $orderCode,
                'recipient_name' => $user->name,
                'recipient_phone' => $user->phone,
                'recipient_address' => $user->address,
                'total_price' => $finalTotalPrice,
                'promotion' => $promotionAmount,
                'shipping_fee' => $shippingFee,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_method === 'cod' ? 'chua_thanh_toan' : 'da_thanh_toan',
                'status' => $request->status,
            ]);

            $productSize = ProductSize::find($request->product_size_id);

            OrderDetail::create([
                'order_id' => $order->id,
                'product_variant_id' => $productVariant->id,
                'quantity' => $request->quantity,
                'price' => $price,
            ]);

            // Giảm tồn kho ngay lập tức
            $productVariant->decrement('quantity', $request->quantity);

            return response()->json([
                'message' => 'Đặt hàng thành công!',
                'order' => [
                    'order_id' => $order->id,
                    'order_code' => $order->order_code,
                    'recipient_name' => $order->recipient_name,
                    'recipient_phone' => $order->recipient_phone,
                    'recipient_address' => $order->recipient_address,
                    'total_price' => $order->total_price,
                    'promotion' => $order->promotion,
                    'shipping_fee' => $order->shipping_fee,
                    'payment_method' => $order->payment_method,
                    'payment_status' => $order->payment_status,
                    'status' => $order->status,
                    'products' => [
                        'product_name' => $product->product_name,
                        'product_code' => $product->product_code,
                        'image' => $product->image,
                        'size' => optional($productSize)->name ?? 'N/A',
                        'quantity' => $request->quantity,
                        'unit_price' => $price,
                        'total_price' => $totalPriceBeforeDiscount
                    ]
                ],
            ], 201);
        });
    }

    /**
     * Hiển thị danh sách đơn hàng của user
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        $ordersQuery = Order::with([
            'user',
            'orderDetails.productVariant.product',
            'orderDetails.productVariant.productSize'
        ])
            ->where('user_id', $user->id);

        // Thêm lọc theo status nếu có
        if ($request->has('status') && $request->status !== '') {
            $ordersQuery->where('status', $request->status);
        }

        $ordersPaginator = $ordersQuery->orderBy('created_at', 'desc')->paginate(10);

        $responseData = [
            'data' => $ordersPaginator->getCollection()->map(function ($order) {
                return $this->formatOrderData($order);
            }),
            'links' => [
                'first' => $ordersPaginator->url(1),
                'last' => $ordersPaginator->url($ordersPaginator->lastPage()),
                'prev' => $ordersPaginator->previousPageUrl(),
                'next' => $ordersPaginator->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $ordersPaginator->currentPage(),
                'from' => $ordersPaginator->firstItem(),
                'last_page' => $ordersPaginator->lastPage(),
                'path' => $ordersPaginator->path(),
                'per_page' => $ordersPaginator->perPage(),
                'to' => $ordersPaginator->lastItem(),
                'total' => $ordersPaginator->total(),
            ],
        ];

        return response()->json($responseData);
    }

    /**
     * Hiển thị chi tiết đơn hàng
     */
    public function show(Order $order): JsonResponse
    {
        $order->load([
            'user',
            'orderDetails.productVariant.product',
            'orderDetails.productVariant.productSize'
        ]);

        return response()->json($this->formatOrderData($order));
    }

    /**
     * Xử lý yêu cầu hủy đơn hàng từ khách hàng
     */
    public function requestCancellation(Request $request, Order $order): JsonResponse
    {
        $request->validate([
            'cancellation_reason' => ['required', 'string', 'min:10', 'max:500'],
        ]);

        if (!$order->canBeCancelledByUser()) {
            Log::warning('Cancel order: Order cannot be cancelled', [
                'order_id' => $order->id,
                'status' => $order->status,
                'user_id' => Auth::id(),
            ]);
            return response()->json([
                'message' => 'Đơn hàng không thể hủy tại thời điểm này!',
                'current_status' => $order->status
            ], 400);
        }

        try {
            DB::beginTransaction();

            $order->previous_status = $order->status;
            $order->status = Order::CHO_XAC_NHAN_HUY;
            $order->cancellation_reason = $request->input('cancellation_reason');
            $order->save();

            DB::commit();

            $order->load([
                'user',
                'orderDetails.productVariant.product',
                'orderDetails.productVariant.productSize'
            ]);

            Log::info('Cancel order request submitted', [
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'reason' => $request->input('cancellation_reason'),
            ]);

            return response()->json([
                'message' => 'Yêu cầu hủy đã được gửi thành công.',
                'order' => $this->formatOrderData($order)
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cancel order server error', [
                'error' => $e->getMessage(),
                'order_id' => $order->id,
                'user_id' => Auth::id(),
            ]);
            return response()->json(['message' => 'Đã xảy ra lỗi server khi gửi yêu cầu hủy đơn hàng!'], 500);
        }
    }

    /**
     * Hàm hỗ trợ định dạng dữ liệu đơn hàng
     */
    protected function formatOrderData(Order $order): array
    {
        return [
            'id' => $order->id,
            'order_code' => $order->order_code ?? null,
            'recipient_name' => $order->recipient_name,
            'recipient_phone' => $order->recipient_phone,
            'recipient_address' => $order->recipient_address,
            'promotion' => (float) ($order->promotion ?? 0),
            'shipping_fee' => (float) ($order->shipping_fee ?? 0),
            'total_price' => (float) $order->total_price,
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'status' => $order->status,
            'cancellation_reason' => $order->cancellation_reason,
            'previous_status' => $order->status === Order::CHO_XAC_NHAN_HUY ? $order->previous_status : null,
            'created_at' => $order->created_at ? $order->created_at->toISOString() : null,
            'updated_at' => $order->updated_at ? $order->updated_at->toISOString() : null,
            'order_details' => $order->orderDetails->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'product_id' => $detail->productVariant->product_id,
                    'product_name' => $detail->productVariant->product->product_name,
                    'variant_id' => $detail->product_variant_id,
                    'size' => $detail->productVariant->productSize->name ?? 'N/A',
                    'quantity' => $detail->quantity,
                    'price' => (float) $detail->price,
                    'image_url' => $detail->productVariant->product->image
                ];
            })->all(),
        ];
    }
}