<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Momo_transactions;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MomopaymentController extends Controller
{

    public function createPayment(Request $request, $order_id)
    {
        $order = Order::find($order_id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => "Không tìm thấy đơn hàng có id= $order_id",
            ], 404);
        }
        // dd($dt);

        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
        $partnerCode = env('MOMO_PARTNER_CODE');
        $accessKey = env('MOMO_ACCESS_KEY');
        $secretKey = env('MOMO_SECRET_KEY');

        $orderId = time() . '_' . $order->id;
        $orderInfo = "Thanh toán đơn hàng " . $order->order_code;
        $amount = (int) $order->total_price;
        $redirectUrl = route('momo.callback');
        $ipnUrl = route('momo.callback');
        $requestId = Str::uuid()->toString();
        $extraData = "";
        $requestType = "payWithATM";


        $rawHash = "accessKey=$accessKey&amount=$amount&extraData=$extraData&ipnUrl=$ipnUrl&orderId=$orderId&orderInfo=$orderInfo&partnerCode=$partnerCode&redirectUrl=$redirectUrl&requestId=$requestId&requestType=$requestType";
        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        $data = [
            "partnerCode"  => $partnerCode,
            "requestId"    => $requestId,
            "amount"       => $amount,
            "orderId"      => $orderId,
            "orderInfo"    => $orderInfo,
            "redirectUrl"  => $redirectUrl,
            "ipnUrl"       => $ipnUrl,
            "requestType"  => $requestType,
            "extraData"    => $extraData,
            "signature"    => $signature,
            // "responseTime" => $responseTime
        ];

        $response = Http::post($endpoint, $data)->json();

        if (!isset($response['payUrl']) || $response['resultCode'] !== 0) {
            return response()->json([
                'status' => false,
                'message' => 'Thanh toán thất bại!',
                'error' => $response
            ], 400);
        }

        return response()->json(["payUrl" => $response['payUrl']]);

    }

       public function callback(Request $request)
    {
        $data = $request->all(); // Lấy toàn bộ dữ liệu trả về từ MoMo
        // dd($data);

        Log::info('MoMo Callback Data:', $data); // Ghi log để kiểm tra dữ liệu

        if (isset($data['partnerCode']) && isset($data['orderId'])) {

            // Tìm order theo orderId
            $order = Order::where('id', explode('_', $data['orderId'])[1])->first();

            if ($order) {
                // Cập nhật trạng thái thanh toán 
                $order->update([
                    'payment_status' => 'da_thanh_toan',
                ]);

                // Lưu giao dịch vào bảng momo_transactions
                Momo_transactions::query()->create([
                    'partnerCode' => $data['partnerCode'] ?? 'UNKNOWN',
                    'amount' => $data['amount'],
                    'requestId' => $data['requestId'],
                    'orderId' => $data['orderId'],
                    'orderInfo' => $data['orderInfo'],
                    'signature' => $data['signature'],
                    'orderType' => $data['orderType'],
                    'transId' => $data['transId'],
                    'payType' => $data['payType'],
                    'responseTime' => $data['responseTime'],
                ]);
            }


        }
        // return redirect('/orders')->with('message', 'Thanh toán hoàn tất!');
    }

}


