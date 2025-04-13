<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class MomoController extends Controller
{
    public function createPayment(Request $request)
    {
        
        $endpoint = env('MOMO_ENDPOINT');
        $partnerCode = env('MOMO_PARTNER_CODE');
        $accessKey = env('MOMO_ACCESS_KEY');
        $secretKey = env('MOMO_SECRET_KEY');
        $orderId = time() . "";
        $orderInfo = "Thanh toán qua MoMo";
        $amount = $request->amount;
        $redirectUrl = env('MOMO_REDIRECT_URL');
        $ipnUrl = env('MOMO_IPN_URL');
        $requestId = time() . "";
        $requestType = "captureWallet";

        $rawHash = "accessKey=$accessKey&amount=$amount&extraData=&ipnUrl=$ipnUrl&orderId=$orderId&orderInfo=$orderInfo&partnerCode=$partnerCode&redirectUrl=$redirectUrl&requestId=$requestId&requestType=$requestType";
        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        $data = [
            'partnerCode' => $partnerCode,
            'partnerName' => "Test",
            'storeId' => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => base64_encode(""),
            'requestType' => $requestType,
            'signature' => $signature,
        ];

        $client = new Client();
        $response = $client->post($endpoint, ['json' => $data]);
        $result = json_decode($response->getBody(), true);

        return response()->json($result);
    }

    public function callback(Request $request)
    {
        $data = $request->all();
        \Log::info('MoMo callback data:', $data); 
        if (isset($data['resultCode']) && $data['resultCode'] == 0) {
            // Thanh toán thành công
            return redirect('/order-success');
        }
        // Thanh toán bị hủy hoặc thất bại
        return redirect('/'); 
    }

    public function ipn(Request $request)
    {
        $data = $request->all();
        \Log::info('MoMo IPN data:', $data); 
        return response()->json(['message' => 'IPN received']);
    }
}