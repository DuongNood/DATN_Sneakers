<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class MomoController extends Controller
{
    public function createPayment(Request $request)
    {
        $config = [
            'partner_code' => env('MOMO_PARTNER_CODE', 'MOMOBKUN20180529'),
            'access_key' => env('MOMO_ACCESS_KEY', 'klm05TvNBzhg7h7j'),
            'secret_key' => env('MOMO_SECRET_KEY', 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa'),
            'endpoint' => env('MOMO_ENDPOINT', 'https://test-payment.momo.vn/v2/gateway/api/create'),
            'redirect_url' => env('MOMO_REDIRECT_URL', 'https://datn-sneakers.loca.lt/api/momo/callback'),
            'ipn_url' => env('MOMO_IPN_URL', 'https://datn-sneakers.loca.lt/api/momo/ipn'),
        ];

        $orderId = time() . "";
        $orderInfo = "Thanh toán đơn hàng #$orderId";
        $amount = $request->input('amount'); // Tổng tiền từ frontend
        $requestId = time() . "";
        $extraData = base64_encode(json_encode([
            'products' => $request->input('products', []),
            'shipping_info' => $request->input('shipping_info', []),
        ])); // Lưu thông tin đơn hàng vào extraData
        $requestType = "payWithATM"; // Thẻ ATM, đổi thành "payWithCard" nếu dùng thẻ quốc tế

        // Tạo raw signature
        $rawHash = "accessKey=" . $config['access_key'] .
                   "&amount=" . $amount .
                   "&extraData=" . $extraData .
                   "&ipnUrl=" . $config['ipn_url'] .
                   "&orderId=" . $orderId .
                   "&orderInfo=" . $orderInfo .
                   "&partnerCode=" . $config['partner_code'] .
                   "&redirectUrl=" . $config['redirect_url'] .
                   "&requestId=" . $requestId .
                   "&requestType=" . $requestType;

        // Tạo chữ ký SHA256
        $signature = hash_hmac("sha256", $rawHash, $config['secret_key']);

        // Dữ liệu gửi đến MoMo
        $data = [
            'partnerCode' => $config['partner_code'],
            'accessKey' => $config['access_key'],
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $config['redirect_url'],
            'ipnUrl' => $config['ipn_url'],
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature,
            'lang' => 'vi'
        ];

        // Gửi request đến MoMo
        try {
            $client = new Client();
            $response = $client->post($config['endpoint'], [
                'json' => $data,
                'headers' => ['Content-Type' => 'application/json']
            ]);

            $result = json_decode($response->getBody(), true);
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('MoMo Payment Error: ' . $e->getMessage());
            return response()->json(['error' => 'Không thể kết nối đến MoMo'], 500);
        }
    }

    public function callback(Request $request)
    {
        $data = $request->all();
        Log::info('MoMo Callback:', $data);

        // Kiểm tra resultCode
        if (isset($data['resultCode']) && $data['resultCode'] == 0) {
            // Thanh toán thành công, giải mã extraData
            $extraData = json_decode(base64_decode($data['extraData']), true);
            // TODO: Tạo đơn hàng tại đây
            return response()->json(['message' => 'Thanh toán thành công', 'data' => $data]);
        }

        // Thanh toán thất bại
        Log::error('MoMo Callback Failed:', $data);
        return response()->json(['message' => 'Thanh toán thất bại', 'data' => $data]);
    }

    public function ipn(Request $request)
    {
        $data = $request->all();
        Log::info('MoMo IPN:', $data);

        // Kiểm tra chữ ký
        $config = [
            'secret_key' => env('MOMO_SECRET_KEY', 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa'),
        ];
        $rawHash = "accessKey=" . $data['accessKey'] .
                   "&amount=" . $data['amount'] .
                   "&extraData=" . $data['extraData'] .
                   "&message=" . $data['message'] .
                   "&orderId=" . $data['orderId'] .
                   "&orderInfo=" . $data['orderInfo'] .
                   "&orderType=" . $data['orderType'] .
                   "&partnerCode=" . $data['partnerCode'] .
                   "&payType=" . $data['payType'] .
                   "&requestId=" . $data['requestId'] .
                   "&responseTime=" . $data['responseTime'] .
                   "&resultCode=" . $data['resultCode'] .
                   "&transId=" . $data['transId'];
        $signature = hash_hmac("sha256", $rawHash, $config['secret_key']);

        if ($signature !== $data['signature']) {
            Log::error('MoMo IPN Invalid Signature');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($data['resultCode'] == 0) {
            // Thanh toán thành công, giải mã extraData và tạo đơn hàng
            $extraData = json_decode(base64_decode($data['extraData']), true);
            // TODO: Tạo đơn hàng tại đây
        }

        return response()->json([], 204);
    }
}