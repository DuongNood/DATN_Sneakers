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
    $product = Product::firstWhere('product_name', $product_name);
    if (!$product) {
        return response()->json(['message' => 'Sản phẩm không tồn tại!'], 404);
    }

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

    //  TRỪ KHO
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
}




}