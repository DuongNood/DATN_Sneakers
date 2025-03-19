<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Models\Oder; // Đảm bảo đúng tên Model với DB của bạn

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Mua hàng (Tạo đơn hàng, chưa trừ kho)
     */
    public function buyProduct(Request $request, $variant_id)
    {
        DB::beginTransaction();

        try {
            $variant = ProductVariant::find($variant_id);

            if (!$variant || !$variant->status) {  
                return response()->json(['message' => 'Sản phẩm không tồn tại hoặc đã ngừng bán'], 404);
            }

            $quantityToBuy = $request->input('quantity', 1);

            if ($variant->quantity < $quantityToBuy) {
                return response()->json(['message' => 'Số lượng sản phẩm không đủ'], 400);
            }

            $price = $variant->promotional_price ?? $variant->price;
            $totalPrice = $price * $quantityToBuy;

            $order = Oder::create([
                'user_id' => Auth::id(),
                'product_variant_id' => $variant->id, // Fix lỗi thiếu field này
                'recipient_name' => $request->input('recipient_name'),
                'recipient_phone' => $request->input('recipient_phone'),
                'recipient_address' => $request->input('recipient_address'),
                'total_price' => $totalPrice,
                'shipping_fee' => $request->input('shipping_fee', 0), 
                'payment_method' => $request->input('payment_method', 'cod'), 
                'payment_status' => 'chua_thanh_toan',
                'status' => 'cho_xac_nhan'
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Đơn hàng đã được đặt, chờ xác nhận!',
                'order' => $order
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Đã có lỗi xảy ra, vui lòng thử lại!',
                'error' => $e->getMessage() 
            ], 500);
        }
    }

    /**
     * Xác nhận đơn hàng (Trừ kho ngay sau khi xác nhận, cập nhật tổng tiền)
     */
    public function confirmOrder($order_id)
{
    DB::beginTransaction();

    try {
        $order = Oder::find($order_id); // Kiểm tra đơn hàng

        if (!$order) {
            return response()->json(['message' => 'Đơn hàng không tồn tại'], 404);
        }

        if ($order->status !== 'cho_xac_nhan') {
            return response()->json(['message' => 'Đơn hàng đã được xử lý trước đó'], 400);
        }

        // Kiểm tra sản phẩm
        $variant = ProductVariant::find($order->product_variant_id);

        if (!$variant) {
            return response()->json(['message' => 'Sản phẩm không tồn tại'], 404);
        }

        if ($variant->quantity < $order->quantity) {
            return response()->json(['message' => 'Kho không đủ hàng để xác nhận đơn'], 400);
        }

        // Cập nhật số lượng sản phẩm
        $variant->quantity -= $order->quantity;
        $variant->save();

        // Cập nhật trạng thái đơn hàng
        $order->status = 'da_xac_nhan';
        $order->payment_status = 'da_thanh_toan';
        $order->save();

        DB::commit();

        return response()->json([
            'message' => 'Đơn hàng đã được xác nhận!',
            'order' => $order
        ], 200);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'Lỗi khi xác nhận đơn hàng', 'error' => $e->getMessage()], 500);
    }
}

}
