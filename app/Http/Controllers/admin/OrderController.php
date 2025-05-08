<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        if ($request->has('payment_method') && in_array($request->payment_method, ['cod', 'momo' , 'vnpay'])) {
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

        // Không cho cập nhật đơn hàng đã bị hủy
        if ($order->status === 'huy_don_hang') {
            return redirect()->back()->with('error', 'Không thể cập nhật đơn hàng đã bị hủy!');
        }

        // Validate dữ liệu đầu vào
        $validated = $request->validate([
            'recipient_name'    => 'required|string|max:255',
            'recipient_phone'   => 'required|string|max:20',
            'recipient_address' => 'required|string|max:500',
            'status'            => 'nullable|string',
            'payment_status'    => 'nullable|string',
        ]);

        // Ghi log request để debug
        // Log::info("Dữ liệu cập nhật đơn hàng nhận được:", $validated);

        DB::beginTransaction();

        try {
            // Cập nhật thông tin người nhận
            $order->recipient_name = $validated['recipient_name'];
            $order->recipient_phone = $validated['recipient_phone'];
            $order->recipient_address = $validated['recipient_address'];

            // Nếu trạng thái đơn hàng thay đổi hợp lệ thì cập nhật
            if (!empty($validated['status']) && $this->canUpdateShippingStatus($order->status, $validated['status'])) {
                if ($validated['status'] === 'huy_don_hang') {
                    $this->restoreStock($order);
                }
                $order->status = $validated['status'];
            }

            // Cập nhật trạng thái thanh toán nếu chưa thanh toán
            if (!empty($validated['payment_status']) && $order->payment_status !== 'da_thanh_toan') {
                $order->payment_status = $validated['payment_status'];
            }

            // (Tùy chọn) Cập nhật chi tiết sản phẩm trong đơn nếu có logic thêm ở đây
            // ...

            // Tính toán lại tổng tiền
            $order->total_price = $this->calculateTotalPrice($order);

            $order->save();
            DB::commit();

            return redirect()->back()->with('success', 'Đơn hàng đã được cập nhật!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Lỗi cập nhật đơn hàng #{$order->id}: " . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi cập nhật đơn hàng!');
        }
    }


    private function calculateTotalPrice(Order $order)
    {
        $total = $order->orderDetails->sum(function ($detail) {
            return $detail->price;
        });
        return $total - $order->promotion + $order->shipping_fee;
    }

    private function canUpdateShippingStatus($currentStatus, $newStatus)
    {
        $validTransitions = [
            'cho_xac_nhan'    => ['dang_chuan_bi', 'dang_van_chuyen', 'da_giao_hang'],
            'dang_chuan_bi'   => ['dang_van_chuyen', 'da_giao_hang'],
            'dang_van_chuyen' => ['da_giao_hang'],
            'da_giao_hang'    => [], // Không thể cập nhật
            'huy_don_hang'    => []  // Không thể cập nhật
        ];

        return isset($validTransitions[$currentStatus]) && in_array($newStatus, $validTransitions[$currentStatus]);
    }

    // Hiển thị danh sách đơn hàng chờ xác nhận hủy
    public function indexPendingCancellations()
    {
        $data = Order::with('user')
            ->where('status', Order::CHO_XAC_NHAN_HUY)
            ->orderBy('updated_at', 'asc')
            ->paginate(15);

        return view('admin.orders.pending_cancellation', compact('data'));
    }

    public function indexOrderCancellations()
    {
        $data = Order::with('user')
            ->where('status', Order::HUY_DON_HANG)
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('admin.orders.order_cancellation', compact('data'));
    }

    // Admin xác nhận yêu cầu hủy của khách
    public function confirmCancellation(Order $order)
    {
        if (!$order->canProcessCancellation()) {
            return redirect()->route('admin.orders.pending_cancellation')->with('error', 'Trạng thái đơn hàng không hợp lệ.');
        }

        try {
            DB::beginTransaction();

            $order->status = Order::HUY_DON_HANG;
            $order->previous_status = null;

            $order->save();

            $this->restoreStock($order);

            DB::commit();

            // (Tùy chọn) Gửi thông báo cho khách hàng
            // $order->user->notify(new OrderCancelledByAdmin($order, $request->input('cancellation_reason')));

            return redirect()->route('admin.orders.pending_cancellation')->with('success', "Đã xác nhận hủy đơn hàng #{$order->order_code}.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to confirm cancellation for order {$order->order_code}: " . $e->getMessage());
            return redirect()->route('admin.orders.pending_cancellation')->with('error', 'Xác nhận hủy thất bại, vui lòng thử lại.');
        }
    }

    // Admin từ chối yêu cầu hủy của khách
    public function rejectCancellation(Request $request, Order $order)
    {
        if (!$order->canProcessCancellation()) {
            return redirect()->route('admin.orders.pending_cancellation')->with('error', 'Trạng thái đơn hàng không hợp lệ.');
        }

        $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:500'], // Lý do từ chối của admin (tùy chọn)
        ]);

        try {
            DB::beginTransaction();

            // Quay lại trạng thái trước đó đã lưu
            $order->status = $order->previous_status ?? Order::CHO_XAC_NHAN;

            // Xử lý lý do
            $adminReason = $request->input('rejection_reason');
            $newReason = "Yêu cầu hủy bị từ chối.";
            if ($adminReason) {
                $newReason .= " Lý do từ Admin: " . $adminReason;
            }

            $order->cancellation_reason = $newReason; // Ghi đè lý do từ chối của admin
            $order->previous_status = null; // Xóa trạng thái trước đó đi
            $order->save();

            DB::commit();

            // (Tùy chọn) Gửi thông báo cho khách hàng
            // $order->user->notify(new CancellationRejected($order, $adminReason));

            return redirect()->route('admin.orders.pending_cancellation')->with('success', "Đã từ chối yêu cầu hủy đơn hàng #{$order->id}.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to reject cancellation for order {$order->id}: " . $e->getMessage());
            return redirect()->route('admin.orders.pending_cancellation')->with('error', 'Từ chối hủy thất bại, vui lòng thử lại.');
        }
    }

    // Admin hủy đơn hàng trực tiếp
    public function cancelOrderDirectly(Request $request, Order $order)
    {
        // Kiểm tra xem đơn hàng có đang ở trạng thái có thể bị hủy không (tránh hủy đơn đã hoàn thành/đã hủy)
        if (in_array($order->status, [Order::HUY_DON_HANG, Order::DA_GIAO_HANG])) {
            return back()->with('error', 'Không thể hủy đơn hàng ở trạng thái này.');
        }

        $request->validate([
            'cancellation_reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        try {
            DB::beginTransaction();

            $order->status = Order::HUY_DON_HANG;
            $order->cancellation_reason = $request->input('cancellation_reason') . ' (Hủy bởi Admin)';
            $order->previous_status = null;
            $order->save();

            $this->restoreStock($order);

            DB::commit();

            // (Tùy chọn) Gửi thông báo cho khách hàng
            // $order->user->notify(new OrderCancelledByAdmin($order, $request->input('cancellation_reason')));

            return redirect()->route('admin.orders.edit', $order)->with('success', "Đã hủy đơn hàng #{$order->order_code} bởi Admin.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Admin không thể hủy đơn hàng {$order->order_code}:" . $e->getMessage());
            return back()->with('error', 'Hủy đơn hàng thất bại, vui lòng thử lại.');
        }
    }

    // public function destroy(Order $order)
    // {
    //     // Logic xóa đơn hàng vĩnh viễn hiện tại của bạn...
    //     // Cẩn thận: Nên kiểm tra trạng thái trước khi cho xóa hẳn?
    //     try {
    //         // Xóa các chi tiết đơn hàng trước (nếu có ràng buộc khóa ngoại)
    //         // $order->orderDetails()->delete();
    //         // $order->delete();
    //         // Hoặc nếu dùng SoftDeletes thì $order->forceDelete();
    //         // return redirect()->route('admin.orders.index')->with('success', 'Xóa đơn hàng thành công.');

    //         // !! Quan trọng: Nếu destroy() đang được dùng để thay đổi status thành CANCELLED thì bạn cần sửa lại
    //         // Hãy dùng hàm cancelOrderDirectly() mới cho việc đó.

    //     } catch (\Exception $e) {
    //         Log::error("Failed to delete order {$order->id}: " . $e->getMessage());
    //         return back()->with('error', 'Xóa đơn hàng thất bại.');
    //     }
    // }

    protected function restoreStock(Order $order)
    {
        // Lấy tất cả chi tiết đơn hàng
        $orderDetails = OrderDetail::where('order_id', $order->id)->get();

        foreach ($orderDetails as $detail) {
            $variant = ProductVariant::find($detail->product_variant_id);
            if ($variant) {
                // Cộng lại số lượng đã trừ khi đặt hàng
                $variant->quantity += $detail->quantity;
                $variant->save();
                Log::info("Đã khôi phục kho cho ID biến thể {$variant->id}. Đã thêm: {$detail->quantity}. Số lượng mới: {$variant->quantity}");
            } else {
                Log::warning("Không tìm thấy ProductVariant cho OrderDetail ID {$detail->id} khi khôi phục kho cho đơn hàng đã hủy {$order->id}.");
            }
        }
    }
}
