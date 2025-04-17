<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use VNPay;

class PaymentController extends Controller
{
    public function createPayment(Request $request)
    {
        $amount = $request->amount; // Số tiền từ FE
        $orderId = time() . ""; // Mã đơn hàng unique
        $orderDesc = "Thanh toán đơn hàng #" . $orderId;

        $data = [
            'vnp_TxnRef' => $orderId,
            'vnp_OrderInfo' => $orderDesc,
            'vnp_Amount' => $amount * 100, // VNPay yêu cầu nhân 100
            'vnp_IpAddr' => $request->ip(),
            'vnp_CreateDate' => now()->format('YmdHis'),
            'vnp_Locale' => 'vn',
            'vnp_OrderType' => '250000', // Loại hàng hóa
        ];

        $paymentUrl = VNPay::createPayment($data);

        return response()->json(['url' => $paymentUrl]);
    }

    public function paymentReturn(Request $request)
    {
        $vnpayData = $request->all();
        $result = VNPay::verifyPayment($vnpayData);

        if ($result['success']) {
            // Lưu thông tin thanh toán vào database
            $payment = new \App\Models\Payment();
            $payment->order_id = $vnpayData['vnp_TxnRef'];
            $payment->amount = $vnpayData['vnp_Amount'] / 100;
            $payment->user_id = auth()->id() ?? null; // Nếu có user đăng nhập
            $payment->status = 'success';
            $payment->vnpay_response = json_encode($vnpayData);
            $payment->save();

            return redirect('https:/localhost/3000/success?order_id=' . $vnpayData['vnp_TxnRef']);
        } else {
            return redirect('https:/localhost/3000/failed');
        }
    }
}