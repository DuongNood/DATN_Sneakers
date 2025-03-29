<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductPromotion;
use App\Models\ProductVariant;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    const PATH_VIEW = 'admin.orders.';

    public function index(Request $request)
    {
        $query = Order::query();

        // 🔍 Xử lý tìm kiếm
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('order_code', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('recipient_name', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('recipient_phone', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('recipient_address', 'LIKE', '%' . $request->search . '%');
            });
        }

        // Lọc theo phương thức thanh toán (COD hoặc Online)
        if ($request->has('payment_method') && in_array($request->payment_method, ['COD', 'Online'])) {
            $query->where('payment_method', $request->payment_method);
        }

        // Lọc theo trạng thái thanh toán (chưa thanh toán hoặc đã thanh toán)
        if ($request->has('payment_status') && in_array($request->payment_status, ['chua_thanh_toan', 'da_thanh_toan'])) {
            $query->where('payment_status', $request->payment_status);
        }

        // Lọc theo trạng thái vận chuyển
        $validStatuses = ['cho_xac_nhan', 'dang_chuan_bi', 'dang_van_chuyen', 'da_giao_hang', 'huy_don_hang'];
        if ($request->has('status') && in_array($request->status, $validStatuses)) {
            $query->where('status', $request->status);
        }

        // Lấy danh sách đơn hàng và phân trang
        $orders = $query->latest('id')->paginate(10);

        return view(self::PATH_VIEW . __FUNCTION__, compact('orders'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $order = Order::with('orderDetails.product')->findOrFail($id);
        $statusOptions = ['cho_xac_nhan', 'dang_chuan_bi', 'dang_van_chuyen', 'da_giao_hang', 'huy_don_hang'];
        return view(self::PATH_VIEW . __FUNCTION__, compact('order', 'statusOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        // Nếu đơn hàng đã bị hủy, không cho cập nhật bất cứ trạng thái nào
        if ($order->status === 'huy_don_hang') {
            return redirect()->back()->with('error', 'Không thể cập nhật đơn hàng đã bị hủy!');
        }

        DB::beginTransaction();
        try {
            // Nếu trạng thái đơn hàng thay đổi hợp lệ thì cập nhật
            if ($request->status && $this->canUpdateShippingStatus($order->status, $request->status)) {
                // Nếu chuyển sang trạng thái hủy đơn hàng, hoàn lại số lượng sản phẩm

                if ($request->status === 'huy_don_hang') {
                    foreach ($order->orderDetails as $detail) {
                        $variant = ProductVariant::findOrFail($detail->product_variant_id);
                        $variant->increment('quantity', $detail->quantity);
                    }
                }
                $order->status = $request->status;
            }

            // Trạng thái thanh toán có thể cập nhật độc lập với trạng thái đơn hàng
            if ($request->has('payment_status') && $order->payment_status !== 'da_thanh_toan') {
                $order->payment_status = $request->payment_status;
            }

            $order->save();
            DB::commit();

            return redirect()->back()->with('success', 'Đơn hàng đã được cập nhật!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi cập nhật đơn hàng!');
        }
    }

    private function canUpdateShippingStatus($currentStatus, $newStatus)
    {
        $validTransitions = [
            'cho_xac_nhan'    => ['dang_chuan_bi', 'dang_van_chuyen', 'da_giao_hang', 'huy_don_hang'],
            'dang_chuan_bi'   => ['dang_van_chuyen', 'da_giao_hang'],
            'dang_van_chuyen' => ['da_giao_hang'],
            'da_giao_hang'    => [], // Không thể cập nhật
            'huy_don_hang'    => []  // Không thể cập nhật
        ];

        return isset($validTransitions[$currentStatus]) && in_array($newStatus, $validTransitions[$currentStatus]);
    }
}
