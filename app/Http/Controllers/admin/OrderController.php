<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductPromotion;
use App\Models\ProductVariant;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    const PATH_VIEW = 'admin.orders.';

    public function index()
    {
        $orders = Order::with('user')->orderBy('created_at', 'desc')->get();

        return view(self::PATH_VIEW . __FUNCTION__, compact('orders'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $order = Order::with('orderDetails.product')->findOrFail($id);
        $statusOptions = ['Chờ xác nhận', 'Đang chuẩn bị', 'Đang vận chuyển', 'Đã giao hàng', 'Hủy đơn hàng'];
        return view(self::PATH_VIEW . __FUNCTION__, compact('order', 'statusOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        DB::beginTransaction();
        try {
            // Nếu đơn hàng bị hủy, hoàn lại số lượng hàng trong kho
            if ($request->status === 'Hủy đơn hàng' && $order->status !== 'Hủy đơn hàng') {
                foreach ($order->details as $detail) {
                    $variant = ProductVariant::findOrFail($detail->product_variant_id);
                    $variant->increment('quantity', $detail->quantity);
                }
            }

            // Chỉ cập nhật trạng thái nếu hợp lệ
            if ($order->payment_status !== 'Đã thanh toán' && $request->payment_status === 'Đã thanh toán') {
                $order->payment_status = 'Đã thanh toán';
                if ($order->status === 'Chờ xác nhận') {
                    $order->status = 'Đang chuẩn bị';
                }
            }

            if ($request->status && $this->canUpdateShippingStatus($order->status, $request->status)) {
                $order->status = $request->status;
            }

            $order->save();
            DB::commit();
            return redirect()
                ->route(self::PATH_VIEW . 'index')
                ->with('success', 'Đơn hàng đã được cập nhật!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Có lỗi xảy ra khi cập nhật đơn hàng!');
        }
    }

    private function canUpdateShippingStatus($currentStatus, $newStatus)
    {
        $validTransitions = [
            'Chờ xác nhận'    => ['Đang chuẩn bị', 'Đang vận chuyển', 'Hủy đơn hàng'],
            'Đang chuẩn bị'   => ['Đang vận chuyển', 'Hủy đơn hàng'],
            'Đang vận chuyển' => ['Đã giao hàng'],
        ];

        return isset($validTransitions[$currentStatus]) && in_array($newStatus, $validTransitions[$currentStatus]);
    }
}
