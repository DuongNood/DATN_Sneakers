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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MomopaymentController extends Controller
{
    protected function createOrderWithMomo(Request $request)
    {
        $request->validate([
            'shipping_info.fullName' => 'required|string|min:2',
            'shipping_info.email' => 'required|email',
            'shipping_info.phone' => 'required|regex:/^[0-9]{10,11}$/',
            'shipping_info.address' => 'required|string|min:5',
            'coupon_code' => 'nullable|string',
        ]);

        $user = Auth::user();
        $shippingInfo = $request->input('shipping_info');
        $couponCode = $request->input('coupon_code');

        $cart = Cart::where('user_id', $user->id)->first();
        $cartItems = CartItem::where('cart_id', $cart->id)->with('product')->get();
        
        // Tính tổng tiền trước giảm giá
        $totalPriceBeforeDiscount = $cartItems->sum(function ($item) {
            return ($item->discounted_price ?? $item->original_price) * $item->quantity;
        });

        // Phí vận chuyển mặc định
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
        $orderCode = 'MOMO_' . strtoupper(Str::random(10));

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
            'payment_method' => 'momo',
            'payment_status' => 'chua_thanh_toan',
            'status' => 'cho_xac_nhan',
        ]);

        foreach ($cartItems as $item) {
            OrderDetail::create([
                'order_id' => $order->id,
                'product_variant_id' => ProductVariant::where('product_id', $item->product_id)
                    ->where('product_size_id', $item->product_size_id)->first()->id,
                'quantity' => $item->quantity,
                'price' => $item->discounted_price ?? $item->original_price,
            ]);
        }

        // Tạo URL MoMo
        $momoResponse = $this->createPaymentUrl($order);

        if ($momoResponse instanceof JsonResponse) {
            $momoResponse = $momoResponse->getData(true); // Chuyển đổi thành mảng
        }

        if ($momoResponse['status'] === 'success') {
            return response()->json([
                'status' => 'success',
                'payUrl' => $momoResponse['payUrl'],
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => $momoResponse['message'] ?? 'Không thể tạo liên kết thanh toán',
        ], 400);
    }

    public function createPaymentUrl(Order $order): array
    {
        try {
            $orderInfo = "Thanh toán đơn hàng " . $order->order_code;
            $amount = (string) $order->total_price;
            $orderId = $order->order_code;
            $redirectUrl = route('api.momo.payment'); // Đảm bảo route này chính xác
            $ipnUrl = route('momo.callback'); // Đảm bảo route này chính xác
            $extraData = "";

            $requestId = time() . "";
            $requestType = "captureWallet";

            // Thông tin MoMo (lấy từ .env)
            $partnerCode = env('MOMO_PARTNER_CODE');
            $accessKey = env('MOMO_ACCESS_KEY');
            $secretKey = env('MOMO_SECRET_KEY');
            $endpoint = env('MOMO_ENDPOINT', 'https://test-payment.momo.vn/v2/gateway/api/create');

            // Dữ liệu để tạo chữ ký
            $rawSignature = "accessKey=$accessKey&amount=$amount&extraData=$extraData&ipnUrl=$ipnUrl&orderId=$orderId&orderInfo=$orderInfo&partnerCode=$partnerCode&redirectUrl=$redirectUrl&requestId=$requestId&requestType=$requestType";

            // Tạo chữ ký
            $signature = hash_hmac('sha256', $rawSignature, $secretKey);

            // Dữ liệu gửi lên MoMo
            $requestBody = [
                'partnerCode' => $partnerCode,
                'requestId' => $requestId,
                'amount' => $amount,
                'orderId' => $orderId,
                'orderInfo' => $orderInfo,
                'redirectUrl' => $redirectUrl,
                'ipnUrl' => $ipnUrl,
                'lang' => 'vi',
                'extraData' => $extraData,
                'requestType' => $requestType,
                'signature' => $signature
            ];

            // Ghi lại yêu cầu gửi đi
            Log::info('Yêu cầu MoMo', [
                'rawSignature' => $rawSignature,
                'signature' => $signature,
                'requestBody' => $requestBody,
            ]);

            // Lưu trữ giao dịch ban đầu
            DB::table('momo_transactions')->insert([
                'order_id' => $requestBody['orderId'],
                'partner_code' => $partnerCode,
                'request_id' => $requestBody['requestId'],
                'amount' => $requestBody['amount'],
                'order_info' => $requestBody['orderInfo'],
                'extra_data' => $requestBody['extraData'] ?? '',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Gửi yêu cầu đến MoMo
            $response = Http::post($endpoint, $requestBody);
            $responseData = $response->json();

            // Ghi lại phản hồi từ MoMo
            Log::info('Phản hồi MoMo', ['response' => $responseData]);

            if ($response->successful() && $responseData['resultCode'] === 0) {
                // Cập nhật giao dịch với dữ liệu phản hồi
                DB::table('momo_transactions')
                    ->where('order_id', $requestBody['orderId'])
                    ->update([
                        'response_data' => json_encode($responseData),
                        'updated_at' => now(),
                    ]);

                return [
                    'status' => 'success',
                    'message' => 'Tạo link thanh toán thành công',
                    'payUrl' => $responseData['payUrl']
                ];
            }

            return [
                'status' => 'error',
                'message' => $responseData['message'] ?? 'Không thể tạo liên kết thanh toán'
            ];

        } catch (\Exception $e) {
            Log::error('Lỗi tạo link thanh toán MoMo', ['error' => $e->getMessage()]);
            return [
                'status' => 'error',
                'message' => 'Lỗi tạo link thanh toán MoMo: ' . $e->getMessage()
            ];
        }
    }

    public function momoCallback(Request $request)
    {
        try {
            $data = $request->validate([
                'partnerCode' => 'required|string',
                'orderId' => 'required|string',
                'requestId' => 'required|string',
                'amount' => 'required|numeric',
                'orderInfo' => 'required|string',
                'orderType' => 'required|string',
                'transId' => 'required|string',
                'resultCode' => 'required|integer',
                'message' => 'required|string',
                'payType' => 'required|string',
                'responseTime' => 'required|string',
                'extraData' => 'nullable|string',
                'signature' => 'required|string',
            ]);

            $secretKey = env('MOMO_SECRET_KEY');
            $rawSignature = "accessKey=" . env('MOMO_ACCESS_KEY') . "&amount={$data['amount']}&extraData={$data['extraData']}&message={$data['message']}&orderId={$data['orderId']}&orderInfo={$data['orderInfo']}&orderType={$data['orderType']}&partnerCode={$data['partnerCode']}&payType={$data['payType']}&requestId={$data['requestId']}&responseTime={$data['responseTime']}&resultCode={$data['resultCode']}&transId={$data['transId']}";
            $signature = hash_hmac('sha256', $rawSignature, $secretKey);

            Log::info('Callback MoMo', [
                'receivedData' => $data,
                'rawSignature' => $rawSignature,
                'signature' => $signature,
            ]);

            if ($signature !== $data['signature']) {
                Log::error('Chữ ký callback MoMo không hợp lệ', ['data' => $data]);
                return response()->json(['status' => 'error', 'message' => 'Chữ ký không hợp lệ'], 400);
            }

            DB::beginTransaction();
            try {
                // Cập nhật bản ghi giao dịch
                DB::table('momo_transactions')->updateOrInsert(
                    ['order_id' => $data['orderId']],
                    [
                        'partner_code' => $data['partnerCode'],
                        'request_id' => $data['requestId'],
                        'amount' => $data['amount'],
                        'order_info' => $data['orderInfo'],
                        'order_type' => $data['orderType'],
                        'trans_id' => $data['transId'],
                        'result_code' => $data['resultCode'],
                        'message' => $data['message'],
                        'pay_type' => $data['payType'],
                        'response_time' => $data['responseTime'],
                        'extra_data' => $data['extraData'],
                        'signature' => $data['signature'],
                        'status' => $data['resultCode'] === 0 ? 'success' : 'failed',
                        'updated_at' => now(),
                    ]
                );

                if ($data['resultCode'] === 0) {
                    // Thanh toán thành công - cập nhật các đơn hàng liên quan
                    DB::table('orders')
                        ->where('order_code', $data['orderId'])
                        ->update([
                            'status' => 'paid',
                            'updated_at' => now()
                        ]);
                }

                DB::commit();
                return redirect()->to($request->redirectUrl);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Lỗi xử lý callback MoMo', ['error' => $e->getMessage()]);
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Lỗi trong callback MoMo', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function getTransactions(Request $request)
    {
        try {
            $query = DB::table('momo_transactions');

            // Thêm bộ lọc 
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('order_id')) {
                $query->where('order_id', $request->order_id);
            }

            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
            }

            // Phân trang và sắp xếp
            $transactions = $query->orderByDesc('order_id')->paginate(15);

            return response()->json([
                'status' => 'success',
                'data' => $transactions,
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi lấy dữ liệu MoMo', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Lấy dữ liệu MoMo thất bại: ' . $e->getMessage(),
            ], 500);
        }
    }
}