<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductVariant;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\VnpayTransaction;
use Illuminate\Support\Facades\Log;

class VnpayController extends Controller
{
    /**
     * Tạo URL thanh toán VNPay
     */
    public function createPaymentUrl(Request $request, $orderId): JsonResponse
    {
        $order = Order::findOrFail($orderId);

        if ($order->payment_method !== 'vnpay') {
            Log::warning('VNPay Create Payment: Invalid payment method', [
                'order_id' => $orderId,
                'payment_method' => $order->payment_method
            ]);
            return response()->json(['message' => 'Phương thức thanh toán không hợp lệ!'], 400);
        }

        $vnp_TmnCode = env('VNPAY_TMN_CODE', 'DD51VBJS');
        $vnp_HashSecret = env('VNPAY_HASH_SECRET', '888PJ1QWYHX192436FTH1MV7S571ULQH');
        $vnp_Url = env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
        $vnp_ReturnUrl = env('VNPAY_RETURN_URL', 'https://polesneakers.loca.lt/api/vnpay/return');

        $vnp_TxnRef = $order->order_code;
        $vnp_Amount = $order->total_price * 100;
        $vnp_OrderInfo = 'Thanh toan don hang ' . $vnp_TxnRef;
        $vnp_OrderType = 'billpayment';
        $vnp_IpAddr = $request->ip();
        $vnp_Locale = 'vn';
        $vnp_CreateDate = now()->format('YmdHis');
        $vnp_ExpireDate = now()->addMinutes(15)->format('YmdHis');

        $inputData = [
            'vnp_Version' => '2.1.0',
            'vnp_TmnCode' => $vnp_TmnCode,
            'vnp_Amount' => $vnp_Amount,
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => $vnp_CreateDate,
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => $vnp_IpAddr,
            'vnp_Locale' => $vnp_Locale,
            'vnp_OrderInfo' => $vnp_OrderInfo,
            'vnp_OrderType' => $vnp_OrderType,
            'vnp_ReturnUrl' => $vnp_ReturnUrl,
            'vnp_TxnRef' => $vnp_TxnRef,
            'vnp_ExpireDate' => $vnp_ExpireDate,
        ];

        ksort($inputData);
        $query = [];
        foreach ($inputData as $key => $value) {
            $query[] = $key . '=' . urlencode($value);
        }
        $hashData = implode('&', $query);
        $vnp_SecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        $vnp_Url .= '?' . $hashData . '&vnp_SecureHash=' . $vnp_SecureHash;

        Log::info('VNPay Create Payment URL', [
            'order_id' => $orderId,
            'vnp_TxnRef' => $vnp_TxnRef,
            'payment_url' => $vnp_Url
        ]);

        return response()->json([
            'message' => 'Tạo URL thanh toán VNPay thành công!',
            'payment_url' => $vnp_Url,
        ], 200);
    }

    /**
     * Xử lý phản hồi từ VNPay (vnp_ReturnUrl)
     */
    public function handleReturn(Request $request): JsonResponse
    {
        $vnp_HashSecret = env('VNPAY_HASH_SECRET', '888PJ1QWYHX192436FTH1MV7S571ULQH');
        $inputData = $request->all();

        Log::info('VNPay Return: Raw Input', ['inputData' => $inputData]);

        if (!isset($inputData['vnp_SecureHash'])) {
            Log::error('VNPay Return: Missing vnp_SecureHash', ['inputData' => $inputData]);
            return response()->json(['message' => 'Thiếu chữ ký VNPay!'], 400);
        }

        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);

        ksort($inputData);
        $query = [];
        foreach ($inputData as $key => $value) {
            $query[] = $key . '=' . urlencode(str_replace(' ', '%20', $value));
        }
        $hashData = implode('&', $query);
        $calculatedHash = hash_hmac('sha512', urldecode($hashData), $vnp_HashSecret);

        if ($vnp_SecureHash !== $calculatedHash) {
            Log::error('VNPay Return: Invalid signature', ['hashData' => $hashData]);
            return response()->json(['message' => 'Chữ ký không hợp lệ!'], 400);
        }

        $order = Order::where('order_code', $inputData['vnp_TxnRef'])->first();
        if (!$order) {
            Log::error('VNPay Return: Order not found', ['vnp_TxnRef' => $inputData['vnp_TxnRef']]);
            return response()->json(['message' => 'Đơn hàng không tồn tại!'], 404);
        }

        if ($order->total_price * 100 != $inputData['vnp_Amount']) {
            Log::error('VNPay Return: Invalid amount', [
                'order_id' => $order->id,
                'vnp_Amount' => $inputData['vnp_Amount'],
                'order_amount' => $order->total_price * 100
            ]);
            return response()->json(['message' => 'Số tiền không hợp lệ!'], 400);
        }

        $responseCode = $inputData['vnp_ResponseCode'];
        $transactionStatus = $responseCode === '00' ? '00' : $responseCode;

        try {
            return DB::transaction(function () use ($inputData, $order, $responseCode, $transactionStatus) {
                VnpayTransaction::create([
                    'order_id' => $order->id,
                    'vnp_transaction_no' => $inputData['vnp_TransactionNo'] ?? null,
                    'vnp_amount' => $inputData['vnp_Amount'] / 100,
                    'vnp_bank_code' => $inputData['vnp_BankCode'] ?? null,
                    'vnp_bank_tran_no' => $inputData['vnp_BankTranNo'] ?? null,
                    'vnp_card_type' => $inputData['vnp_CardType'] ?? null,
                    'vnp_pay_date' => !empty($inputData['vnp_PayDate']) ? date('Y-m-d H:i:s', strtotime($inputData['vnp_PayDate'])) : null,
                    'vnp_response_code' => $responseCode,
                    'vnp_transaction_status' => $transactionStatus,
                    'vnp_secure_hash' => $inputData['vnp_SecureHash'],
                ]);

                if ($responseCode === '00') {
                    // Thanh toán thành công, giữ payment_status = 'da_thanh_toan'
                    $order->status = 'dang_chuan_bi';
                    $order->save();

                    Log::info('VNPay Return: Payment successful', ['order_id' => $order->id]);
                    return response()->json([
                        'message' => 'Thanh toán thành công!',
                        'order_id' => $order->id,
                        'order_code' => $order->order_code,
                        'transaction_status' => 'success',
                    ], 200);
                } else {
                    // Thanh toán thất bại, hoàn tác payment_status và kho
                    $order->payment_status = 'chua_thanh_toan';
                    $order->status = 'huy_don_hang';
                    $order->save();

                    // Hoàn kho
                    $orderDetails = OrderDetail::where('order_id', $order->id)->get();
                    foreach ($orderDetails as $detail) {
                        $productVariant = ProductVariant::where('id', $detail->product_variant_id)
                            ->lockForUpdate()
                            ->first();
                        if ($productVariant) {
                            $productVariant->increment('quantity', $detail->quantity);
                        }
                    }

                    Log::warning('VNPay Return: Payment failed', [
                        'order_id' => $order->id,
                        'response_code' => $responseCode
                    ]);
                    return response()->json([
                        'message' => 'Thanh toán thất bại! Mã lỗi: ' . $responseCode,
                        'order_id' => $order->id,
                        'order_code' => $order->order_code,
                        'transaction_status' => 'failed',
                    ], 400);
                }
            });
        } catch (\Exception $e) {
            Log::error('VNPay Return Transaction Error', [
                'order_id' => $order->id,
                'error_message' => $e->getMessage()
            ]);
            return response()->json(['message' => 'Lỗi xử lý giao dịch: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Xử lý IPN từ VNPay
     */
    public function handleIpn(Request $request): JsonResponse
    {
        $vnp_HashSecret = env('VNPAY_HASH_SECRET', '888PJ1QWYHX192436FTH1MV7S571ULQH');
        $inputData = $request->all();

        Log::info('VNPay IPN: Raw Input', ['inputData' => $inputData]);

        if (!isset($inputData['vnp_SecureHash'])) {
            Log::error('VNPay IPN: Missing vnp_SecureHash', ['inputData' => $inputData]);
            return response()->json(['RspCode' => '97', 'Message' => 'Thiếu chữ ký VNPay'], 400);
        }

        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);

        ksort($inputData);
        $query = [];
        foreach ($inputData as $key => $value) {
            $query[] = $key . '=' . urlencode(str_replace(' ', '%20', $value));
        }
        $hashData = implode('&', $query);
        $calculatedHash = hash_hmac('sha512', urldecode($hashData), $vnp_HashSecret);

        if ($vnp_SecureHash !== $calculatedHash) {
            Log::error('VNPay IPN: Invalid signature', ['hashData' => $hashData]);
            return response()->json(['RspCode' => '97', 'Message' => 'Chữ ký không hợp lệ'], 400);
        }

        $order = Order::where('order_code', $inputData['vnp_TxnRef'])->first();
        if (!$order) {
            Log::error('VNPay IPN: Order not found', ['vnp_TxnRef' => $inputData['vnp_TxnRef']]);
            return response()->json(['RspCode' => '01', 'Message' => 'Đơn hàng không tồn tại'], 404);
        }

        if ($order->total_price * 100 != $inputData['vnp_Amount']) {
            Log::error('VNPay IPN: Invalid amount', [
                'order_id' => $order->id,
                'vnp_Amount' => $inputData['vnp_Amount'],
                'order_amount' => $order->total_price * 100
            ]);
            return response()->json(['RspCode' => '04', 'Message' => 'Số tiền không hợp lệ'], 400);
        }

        $responseCode = $inputData['vnp_ResponseCode'];
        $transactionStatus = $responseCode === '00' ? '00' : $responseCode;

        try {
            return DB::transaction(function () use ($inputData, $order, $responseCode, $transactionStatus) {
                VnpayTransaction::create([
                    'order_id' => $order->id,
                    'vnp_transaction_no' => $inputData['vnp_TransactionNo'] ?? null,
                    'vnp_amount' => $inputData['vnp_Amount'] / 100,
                    'vnp_bank_code' => $inputData['vnp_BankCode'] ?? null,
                    'vnp_bank_tran_no' => $inputData['vnp_BankTranNo'] ?? null,
                    'vnp_card_type' => $inputData['vnp_CardType'] ?? null,
                    'vnp_pay_date' => !empty($inputData['vnp_PayDate']) ? date('Y-m-d H:i:s', strtotime($inputData['vnp_PayDate'])) : null,
                    'vnp_response_code' => $responseCode,
                    'vnp_transaction_status' => $transactionStatus,
                    'vnp_secure_hash' => $inputData['vnp_SecureHash'],
                ]);

                if ($responseCode === '00') {
                    // Thanh toán thành công, giữ payment_status = 'da_thanh_toan'
                    $order->status = 'dang_chuan_bi';
                    $order->save();

                    Log::info('VNPay IPN: Payment successful', ['order_id' => $order->id]);
                    return response()->json(['RspCode' => '00', 'Message' => 'Xác nhận giao dịch thành công'], 200);
                } else {
                    // Thanh toán thất bại, hoàn tác payment_status và kho
                    $order->payment_status = 'chua_thanh_toan';
                    $order->status = 'huy_don_hang';
                    $order->save();

                    // Hoàn kho
                    $orderDetails = OrderDetail::where('order_id', $order->id)->get();
                    foreach ($orderDetails as $detail) {
                        $productVariant = ProductVariant::where('id', $detail->product_variant_id)
                            ->lockForUpdate()
                            ->first();
                        if ($productVariant) {
                            $productVariant->increment('quantity', $detail->quantity);
                        }
                    }

                    Log::warning('VNPay IPN: Payment failed', [
                        'order_id' => $order->id,
                        'response_code' => $responseCode
                    ]);
                    return response()->json(['RspCode' => $responseCode, 'Message' => 'Giao dịch thất bại'], 400);
                }
            });
        } catch (\Exception $e) {
            Log::error('VNPay IPN Transaction Error', [
                'order_id' => $order->id,
                'error_message' => $e->getMessage()
            ]);
            return response()->json(['RspCode' => '99', 'Message' => 'Lỗi xử lý giao dịch'], 500);
        }
    }
}