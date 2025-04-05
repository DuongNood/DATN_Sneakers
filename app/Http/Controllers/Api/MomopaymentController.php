<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MomopaymentController extends Controller
{
    public function createPayment(Request $request)
    {
        // Validate dữ liệu đầu vào
        $validated = $request->validate([
            'orderId' => 'required|string',
            'amount' => 'required|numeric|min:1000',
            'orderInfo' => 'required|string',
            'redirectUrl' => 'required|url',
            'ipnUrl' => 'required|url',
            'requestId' => 'required|string',
            'extraData' => 'nullable|string',
        ]);

        // Thông tin MoMo (lấy từ .env)
        $partnerCode = env('MOMO_PARTNER_CODE');
        $accessKey = env('MOMO_ACCESS_KEY');
        $secretKey = env('MOMO_SECRET_KEY');
        $endpoint = env('MOMO_ENDPOINT', 'https://test-payment.momo.vn/v2/gateway/api/create');

        // Dữ liệu để tạo chữ ký
        $rawSignature = "accessKey=$accessKey&amount={$validated['amount']}&extraData={$validated['extraData']}&ipnUrl={$validated['ipnUrl']}&orderId={$validated['orderId']}&orderInfo={$validated['orderInfo']}&partnerCode=$partnerCode&redirectUrl={$validated['redirectUrl']}&requestId={$validated['requestId']}&requestType=captureWallet";

        // Tạo chữ ký
        $signature = hash_hmac('sha256', $rawSignature, $secretKey);

        // Dữ liệu gửi lên MoMo
        $requestBody = [
            'partnerCode' => $partnerCode,
            'accessKey' => $accessKey,
            'requestId' => $validated['requestId'],
            'amount' => (string) $validated['amount'],
            'orderId' => $validated['orderId'],
            'orderInfo' => $validated['orderInfo'],
            'redirectUrl' => $validated['redirectUrl'],
            'ipnUrl' => $validated['ipnUrl'],
            'extraData' => $validated['extraData'] ?? '',
            'requestType' => 'captureWallet',
            'signature' => $signature,
            'lang' => 'vi',
        ];

        // Debug yêu cầu gửi đi
        Log::info('MoMo request', [
            'rawSignature' => $rawSignature,
            'signature' => $signature,
            'requestBody' => $requestBody,
        ]);

        try {
            // Gửi yêu cầu đến MoMo
            $response = Http::post($endpoint, $requestBody);
            $responseData = $response->json();

            // Debug phản hồi từ MoMo
            Log::info('MoMo response', ['response' => $responseData]);

            if ($response->successful() && $responseData['resultCode'] === 0) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Payment link created successfully',
                    'payUrl' => $responseData['payUrl'],
                ], 200);
            } else {
                Log::error('MoMo create payment failed', ['response' => $responseData]);
                return response()->json([
                    'status' => 'error',
                    'message' => $responseData['message'] ?? 'Failed to create MoMo payment link',
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error creating MoMo payment link', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create MoMo payment link: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function momoCallback(Request $request)
    {
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

        Log::info('MoMo callback', [
            'receivedData' => $data,
            'rawSignature' => $rawSignature,
            'signature' => $signature,
        ]);

        if ($signature !== $data['signature']) {
            Log::error('Invalid MoMo callback signature', ['data' => $data]);
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
        }

        if ($data['resultCode'] === 0) {
            \DB::table('momo_transactions')->updateOrInsert(
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
                    'status' => 'success',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            \DB::table('orders')
                ->where('order_id', $data['orderId'])
                ->update(['status' => 'paid']);

            $extraData = json_decode(base64_decode($data['extraData']), true);
            $orderCode = $extraData['orderCode'] ?? $data['orderId'];
            return redirect("http://localhost:3000/order-success?orderCode={$orderCode}&status=success");
        } else {
            \DB::table('momo_transactions')->updateOrInsert(
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
                    'status' => 'failed',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            return redirect("http://localhost:3000/order-success?status=failed");
        }
    }

    public function getTransactions(Request $request)
    {
        $transactions = \DB::table('momo_transactions')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $transactions,
        ]);
    }
}