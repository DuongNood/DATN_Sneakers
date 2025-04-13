<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductVariant;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\VnpayTransaction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class VnpaypaymentController extends Controller
{
    public function buyProductWithVNPAY(Request $request, $product_name): JsonResponse
{
    $request->validate([
        'product_size_id' => 'required|integer',
        'quantity' => 'required|integer|min:1',
        'promotion_name' => 'nullable|string'
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

        if (!$productVariant || $productVariant->quantity < $request->quantity) {
            return response()->json(['message' => 'Kho không đủ hàng hoặc biến thể không tồn tại!'], 400);
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
                return response()->json(["message" => "Mã giảm giá không hợp lệ hoặc đã hết hạn!"], 400);
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
            'payment_method' => 'vnpay',
            'payment_status' => 'chua_thanh_toan',
            'status' => 'cho_xac_nhan',
        ]);

        OrderDetail::create([
            'order_id' => $order->id,
            'product_variant_id' => $productVariant->id,
            'quantity' => $request->quantity,
            'price' => $price,
        ]);

        $productVariant->decrement('quantity', $request->quantity);

        // Tạo URL thanh toán VNPAY
        $vnp_Url = env('VNPAY_URL');
        $vnp_Returnurl = env('VNPAY_RETURN_URL');
        $vnp_TmnCode = env('VNPAY_TMN_CODE');
        $vnp_HashSecret = env('VNPAY_HASH_SECRET');

        $vnp_TxnRef = $order->id;
        $vnp_OrderInfo = "Thanh toán đơn hàng #" . $order->order_code;
        $vnp_Amount = $finalTotalPrice * 100;
        $vnp_IpAddr = request()->ip();

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => now()->format('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => "vn",
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => "other",
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        ];

        ksort($inputData);
        $hashdata = urldecode(http_build_query($inputData));
        $query = http_build_query($inputData);

        $vnp_SecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $paymentUrl = $vnp_Url . "?" . $query . '&vnp_SecureHash=' . $vnp_SecureHash;

        // Lưu vào bảng giao dịch
        VnpayTransaction::create([
            'order_id' => $order->id,
            'amount' => $finalTotalPrice,
            'status' => 'pending',
            'payment_method' => 'vnpay'
        ]);

        $qrCode = \QrCode::format('png')->size(300)->generate($paymentUrl);
        $qrImageBase64 = base64_encode($qrCode);

        return response()->json([
            'message' => 'Đặt hàng thành công! Vui lòng thanh toán qua VNPAY',
            'payment_url' => $paymentUrl,
            'qr_code_base64' => 'data:image/png;base64,' . $qrImageBase64,
        ], 201);
    });
}
}