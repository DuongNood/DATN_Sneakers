<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductSize;


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
        $product = Product::where('product_name', $product_name)->firstOrFail();

        // Kiểm tra biến thể sản phẩm (size)
        $productVariant = ProductVariant::where([
            ['product_id', $product->id],
            ['product_size_id', $request->product_size_id]
        ])->first();

        if (!$productVariant) {
            return response()->json(['message' => 'Không tìm thấy biến thể sản phẩm!'], 404);
        }

        if ($productVariant->quantity < $request->quantity) {
            return response()->json(['message' => 'Kho không đủ hàng!'], 400);
        }

        // Lấy giá sản phẩm (ưu tiên discounted_price nếu có)
        $price = $product->discounted_price ?? $product->original_price;
        $totalPriceBeforeDiscount = $price * $request->quantity; // Giá trước khi áp dụng giảm giá

        // Mặc định phí vận chuyển
        $shippingFee = 30000;

        // Mặc định giảm giá = 0
        $promotionAmount = 0;

        // Kiểm tra mã khuyến mãi nếu có
        if (!empty($request->promotion_name)) {
            $promotion = DB::table('promotions')
                ->where('promotion_name', $request->promotion_name)
                ->where('status', 1) // Chỉ lấy khuyến mãi còn hiệu lực
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->first();

            if ($promotion) {
                if ($promotion->discount_type === 'Giảm theo %') {
                    // Giảm theo phần trăm
                    $promotionAmount = ($totalPriceBeforeDiscount * $promotion->discount_value) / 100;

                    // Giới hạn mức giảm tối đa nếu có
                    if (!empty($promotion->max_discount_value) && $promotionAmount > $promotion->max_discount_value) {
                        $promotionAmount = $promotion->max_discount_value;
                    }
                } elseif ($promotion->discount_type === 'Giảm số tiền') {
                    // Giảm số tiền cố định
                    $promotionAmount = $promotion->discount_value;
                }

                // Giảm giá không được lớn hơn tổng giá trị sản phẩm
                $promotionAmount = min($promotionAmount, $totalPriceBeforeDiscount);
            } else {
                return response()->json(["message" => "Mã giảm giá không hợp lệ hoặc đã hết hạn!"], 400);
            }
        }

        // Tổng tiền đơn hàng cuối cùng (đã trừ giảm giá & cộng phí ship)
        $finalTotalPrice = max(($totalPriceBeforeDiscount - $promotionAmount) + $shippingFee, 0);

        // Tạo mã đơn hàng
        $orderCode = 'ORD' . strtoupper(Str::random(10));

        // Tạo đơn hàng
        $order = Order::create([
            'user_id' => $user->id,
            'order_code' => $orderCode,
            'recipient_name' => $user->name,
            'recipient_phone' => $user->phone ?? 'N/A',
            'recipient_address' => $user->address ?? 'N/A',
            'total_price' => $finalTotalPrice, // ✅ Tổng tiền sau khi giảm giá và cộng phí ship
            'promotion' => $promotionAmount, // Số tiền giảm giá
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
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'price' => $price,
        ]);

        // Trả về thông tin đơn hàng đầy đủ
        return response()->json([
            'message' => 'Đặt hàng thành công!',
            'order' => [
                'order_code' => $order->order_code,
                'recipient_name' => $order->recipient_name,
                'recipient_phone' => $order->recipient_phone,
                'recipient_address' => $order->recipient_address,
                'total_price' => $order->total_price, // ✅ Đã bao gồm phí ship & trừ giảm giá
                'promotion' => $order->promotion, // Số tiền giảm giá
                'shipping_fee' => $order->shipping_fee,
                'final_total' => $order->total_price, // Tổng tiền cuối cùng (giống total_price)
                'payment_method' => $order->payment_method,
                'payment_status' => $order->payment_status,
                'status' => $order->status,
                'products' => [
                    'product_name' => $product->product_name,
                    'product_code' => $product->product_code,
                    'image' => $product->image,
                    'size' => $productSize->name,
                    'quantity' => $request->quantity,
                    'unit_price' => $price,
                    'total_price' => $totalPriceBeforeDiscount // Giá trước giảm giá
                ]
            ],
        ], 201);
    }



    public function confirmOrder($order_code)
{
    DB::beginTransaction();
    try {
        //  Tìm đơn hàng theo order_code
        $order = Order::with('orderDetails')->where('order_code', $order_code)->first();

        //  Kiểm tra nếu không tìm thấy đơn hàng
        if (!$order) {
            return response()->json(['message' => 'Không tìm thấy đơn hàng!'], 404);
        }

        //  Kiểm tra trạng thái đơn hàng
        if ($order->status !== 'cho_xac_nhan') {
            return response()->json(['message' => 'Đơn hàng đã được xử lý trước đó'], 400);
        }

        $errors = [];

        

        //  Cập nhật trạng thái đơn hàng
        $order->status = 'da_xac_nhan';
        $order->save();

        DB::commit();
        return response()->json(['message' => 'Đơn hàng đã được xác nhận!'], 200);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Lỗi khi xác nhận đơn hàng',
            'error' => $e->getMessage()
        ], 500);
    }
}
}

