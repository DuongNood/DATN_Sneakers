<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Momo_transactions;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MomopaymentController extends Controller
{


    public function getTransactions()
    {

        $momoTransactions = Momo_transactions::all();

        return response()->json([
            'status' => true,
            'message' => 'lấy ra thành công các giao dịch',
            'data' => $momoTransactions
        ]);
    }

    public function createPayment(Request $request)
    {
        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";

        $partnerCode = $request->input('partnerCode', 'MOMOBKUN20180529');
        $accessKey = $request->input('accessKey', 'klm05TvNBzhg7h7j');
        $secretKey = $request->input('secretKey', 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa');
        $orderInfo = $request->input('orderInfo', "Thanh toán qua MoMo");
        $amount = $request->input('amount', "10000");
        $orderId = time() . "";
        $redirectUrl = $request->input('redirectUrl', "https://webhook.site/b3088a6a-2d17-4f8d-a383-71389a6c600b");
        $ipnUrl = $request->input('ipnUrl', "https://webhook.site/b3088a6a-2d17-4f8d-a383-71389a6c600b");
        $extraData = $request->input('extraData', "");

        $requestId = time() . "";
        $requestType = "payWithATM";

        // Tạo chuỗi để ký HMAC SHA256
        $rawHash = "accessKey={$accessKey}&amount={$amount}&extraData={$extraData}&ipnUrl={$ipnUrl}&orderId={$orderId}&orderInfo={$orderInfo}&partnerCode={$partnerCode}&redirectUrl={$redirectUrl}&requestId={$requestId}&requestType={$requestType}";
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
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature,
        ];

        // Gửi yêu cầu POST
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($endpoint, $data);

        $jsonResult = $response->json();

        // Chuyển hướng đến payUrl
        return response()->json(["payUrl" => $jsonResult['payUrl'] ?? '']);
    }

    public function momoCallback(Request $request)
    {
        $data = $request->all();
        
        $partnerCode = $data["partnerCode"] ?? '';
        $accessKey = $data["accessKey"] ?? '';
        $orderId = $data["orderId"] ?? '';
        $localMessage = $data["localMessage"] ?? '';
        $message = $data["message"] ?? '';
        $transId = $data["transId"] ?? '';
        $orderInfo = $data["orderInfo"] ?? '';
        $amount = $data["amount"] ?? '';
        $errorCode = $data["errorCode"] ?? '';
        $responseTime = $data["responseTime"] ?? '';
        $requestId = $data["requestId"] ?? '';
        $extraData = $data["extraData"] ?? '';
        $payType = $data["payType"] ?? '';
        $orderType = $data["orderType"] ?? '';
        $m2signature = $data["signature"] ?? '';

        $serectKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';

        $rawHash = "partnerCode=$partnerCode&accessKey=$accessKey&requestId=$requestId&amount=$amount&orderId=$orderId&orderInfo=$orderInfo" .
            "&orderType=$orderType&transId=$transId&message=$message&localMessage=$localMessage&responseTime=$responseTime&errorCode=$errorCode" .
            "&payType=$payType&extraData=$extraData";
        
        $partnerSignature = hash_hmac("sha256", $rawHash, $serectKey);

        // Kiểm tra tính toàn vẹn của dữ liệu
        if ($m2signature === $partnerSignature) {
            if ($errorCode == '0') {
                return response()->json([
                    'message' => 'Capture Payment Success',
                    'status' => 'success'
                ], 200);
            } else {
                return response()->json([
                    'message' => $message,
                    'status' => 'failed'
                ], 400);
            }
        } else {
            return response()->json([
                'message' => 'This transaction could be hacked, please check your signature and returned signature',
                'status' => 'error'
            ], 400);
        }
    }

    public function queryPaymentStatus(Request $request)
    {
        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/query";
        
        $partnerCode = 'MOMOBKUN20180529';
        $accessKey = 'klm05TvNBzhg7h7j';
        $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
        $requestId = time() . "";
        $orderId = $request->input('orderId');

        $rawHash = "accessKey=$accessKey&orderId=$orderId&partnerCode=$partnerCode&requestId=$requestId";
        $signature = hash_hmac("sha256", $rawHash, $secretKey);
        
        $data = [
            'partnerCode' => $partnerCode,
            'requestId' => $requestId,
            'orderId' => $orderId,
            'requestType' => 'transactionStatus',
            'signature' => $signature,
            'lang' => 'vi'
        ];
        
        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post($endpoint, $data);
        
        return response()->json($response->json());
    }


    /**
     * Lưu đơn hàng và lịch sử giao dịch
     */
    public function saveOrder(Request $request)
    {
        DB::beginTransaction();
        try {
            $order = new Order();
            $order->user_id = $request->user_id;
            $order->total_price = $request->amount;
            $order->status = 'pending';
            $order->payment_method = 'MoMo';
            $order->save();
            
            foreach ($request->cart_items as $item) {
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $item['id'];
                $orderItem->quantity = $item['quantity'];
                $orderItem->price = $item['price'];
                $orderItem->save();
            }
            
            $transaction = new Momo_transactions();
            $transaction->user_id = $request->user_id;
            $transaction->order_id = $order->id;
            $transaction->amount = $request->amount;
            $transaction->status = 'pending';
            $transaction->payment_method = 'MoMo';
            $transaction->save();
            
            DB::commit();
            return response()->json(['message' => 'Đơn hàng và giao dịch đã được lưu thành công', 'order_id' => $order->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Có lỗi xảy ra khi lưu đơn hàng', 'message' => $e->getMessage()], 500);
        }
    }

}


