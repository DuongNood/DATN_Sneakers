<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductSize;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function buyProductByName(Request $request, $product_name)
    {
        $request->validate([
            'product_size_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'promotion_name' => 'nullable|string'
        ]);

        $user = Auth::user();

        // Kiểm tra nếu user chưa có thông tin phone hoặc address
        if (!$user->phone || !$user->address) {
            return response()->json(['message' => 'Vui lòng cập nhật số điện thoại và địa chỉ trước khi mua hàng!'], 400);
        }

        // Tìm sản phẩm theo tên
        $product = Product::firstWhere('product_name', $product_name);
        if (!$product) {
            return response()->json(['message' => 'Sản phẩm không tồn tại!'], 404);
        }

        return DB::transaction(function () use ($request, $product, $user) {
            // Khóa sản phẩm tránh trường hợp đặt hàng cùng lúc
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

            // Lấy giá sản phẩm (ưu tiên discounted_price nếu có)
            $price = $product->discounted_price ?? $product->original_price;
            $totalPriceBeforeDiscount = $price * $request->quantity;

            // Mặc định phí vận chuyển
            $shippingFee = 30000;
            $promotionAmount = 0;

            // Kiểm tra mã khuyến mãi nếu có
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
                    return response()->json(["message" => "Mã giảm giá không hợp lệ hoặc đã hết hạn!"], 400);
                }
            }

            // Tính tổng tiền cuối cùng (đã trừ giảm giá & cộng phí ship)
            $finalTotalPrice = max(($totalPriceBeforeDiscount - $promotionAmount) + $shippingFee, 0);

            // Tạo mã đơn hàng
            $orderCode = 'ORD' . strtoupper(Str::random(10));

            // Tạo đơn hàng
            $order = Order::create([
                'user_id' => $user->id,
                'order_code' => $orderCode,
                'recipient_name' => $user->name,
                'recipient_phone' => $user->phone,
                'recipient_address' => $user->address,
                'total_price' => $finalTotalPrice,
                'promotion' => $promotionAmount,
                'shipping_fee' => $shippingFee,
                'payment_method' => 'COD',
                'payment_status' => 'chua_thanh_toan',
                'status' => 'cho_xac_nhan',
            ]);

            // Lấy thông tin size sản phẩm
            $productSize = ProductSize::find($request->product_size_id);

            // Tạo chi tiết đơn hàng
            OrderDetail::create([
                'order_id' => $order->id,
                'product_variant_id' => $productVariant->id,
                'quantity' => $request->quantity,
                'price' => $price,
            ]);

            // Trừ kho
            $productVariant->decrement('quantity', $request->quantity);

            return response()->json([
                'message' => 'Đặt hàng thành công!',
                'order' => [
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
     * Display a listing of the resource for the authenticated user.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();

        $ordersPaginator = Order::with([
            'user',
            'orderDetails.productVariant.product',
            'orderDetails.productVariant.product_size'
        ])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

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
     * Display the specified resource.
     */
    public function show(Order $order): JsonResponse
    {
        // Authorization
        Gate::authorize('view', $order);

        $order->load([
            'user',
            'orderDetails.productVariant.product',
            'orderDetails.productVariant.productSize'
        ]);

        return response()->json($this->formatOrderData($order));
    }

    /**
     * Handle customer's request to cancel an order.
     */
    public function requestCancellation(Request $request, Order $order): JsonResponse
    {
        // Authorization đã được xử lý bởi middleware hoặc Gate trong route

        $request->validate([
            'cho_xac_nhan_huy' => ['required', 'string', 'min:10', 'max:500'],
        ]);

        if (!$order->canBeCancelledByUser()) {
            return response()->json([
                'message' => 'Đơn hàng không thể bị hủy ở giai đoạn này!',
                'current_status' => $order->status
            ], 400);
        }

        try {
            DB::beginTransaction();

            $order->previous_status = $order->status;
            $order->status = Order::CHO_XAC_NHAN_HUY;
            $order->cho_xac_nhan_huy = $request->input('cho_xac_nhan_huy');
            $order->save();

            DB::commit();

            $order->load([
                'user',
                'orderDetails.productVariant.product',
                'orderDetails.productVariant.productSize'
            ]);

            return response()->json([
                'message' => 'Yêu cầu hủy đã được gửi thành công.',
                'order' => $this->formatOrderData($order)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Yêu cầu hủy đơn hàng không thành công đối với đơn hàng ' . $order->order_code . ': ' . $e->getMessage());
            return response()->json(['message' => 'Không thể gửi yêu cầu hủy!'], 500);
        }
    }


    /**
     * Helper function to format order data consistently for JSON responses.
     * Hàm này giúp tái sử dụng logic định dạng, bạn có thể tùy chỉnh thêm
     * @param Order $order The Order model instance (pre-loaded with necessary relations)
     * @return array
     */
    protected function formatOrderData(Order $order): array
    {
        return [
            'id' => $order->id,
            'order_code' => $order->order_code ?? null,
            'recipient_name' => $order->user->name,
            'recipient_phone' => $order->user->phone,
            'recipient_address' => $order->user->address,
            'promotion' => (float) ($order->promotion ?? 0),
            'shipping_fee' => (float) ($order->shipping_fee ?? 0),
            'total_price' => (float) $order->total_price,
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'status' => $order->status,
            'cho_xac_nhan_huy' => $order->cho_xac_nhan_huy, // Lý do hủy (sẽ là null nếu chưa hủy)
            'previous_status' => $order->status === Order::CHO_XAC_NHAN_HUY ? $order->previous_status : null, // Chỉ hiện khi chờ hủy
            'created_at' => $order->created_at ? $order->created_at->toISOString() : null,
            'updated_at' => $order->updated_at ? $order->updated_at->toISOString() : null,

            // Định dạng chi tiết đơn hàng
            'order_details' => $order->orderDetails->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'product_id' => $detail->productVariant->product_id,
                    'product_name' => $detail->productVariant->product->product_name,
                    'variant_id' => $detail->product_variant_id,
                    'size' => $detail->productVariant->productSize->name,
                    'quantity' => $detail->quantity,
                    'price' => (float) $detail->price, // Giá tại thời điểm mua
                    'image_url' => $detail->productVariant->product->image
                ];
            }),
        ];
    }

    /**
     * Helper function to get status text.
     * @param string|null $status
     * @return string
     */
    protected function getStatusText(?string $status): string
    {
        switch ($status) {
            case Order::CHO_XAC_NHAN:
                return 'Chờ xác nhận';
            case Order::DANG_CHUAN_BI:
                return 'Đang chuẩn bị';
            case Order::DANG_VAN_CHUYEN:
                return 'Đang vận chuyển';
            case Order::DA_GIAO_HANG:
                return 'Đã giao hàng';
            case Order::CHO_XAC_NHAN_HUY:
                return 'Chờ xác nhận hủy';
            case Order::HUY_DON_HANG:
                return 'Hủy đơn hàng';
            default:
                return $status ?? 'Không xác định';
        }
    }
}
