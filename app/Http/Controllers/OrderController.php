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
    public function buyProduct(Request $request, $product_id)
{
    DB::beginTransaction();

    try {
        // Lấy danh sách biến thể có cùng product_id
        $variants = ProductVariant::where('product_id', $product_id)
            ->where('status', 1) // Chỉ lấy biến thể đang hoạt động
            ->where('quantity', '>', 0) // Chỉ lấy biến thể còn hàng
            ->get();

        if ($variants->isEmpty()) {
            return response()->json(['message' => 'Không có biến thể nào khả dụng'], 404);
        }

        $quantityToBuy = $request->input('quantity', 1);

        // Chọn biến thể đầu tiên có đủ hàng
        $variant = $variants->where('quantity', '>=', $quantityToBuy)->first();

        if (!$variant) {
            return response()->json(['message' => 'Không có biến thể nào đủ số lượng yêu cầu'], 400);
        }

        $price = $variant->promotional_price ?? $variant->price;
        $totalPrice = $price * $quantityToBuy;

        // Tạo đơn hàng trong bảng 'orders'
        $order = Oder::create([
            'user_id' => Auth::id(),
            'recipient_name' => $request->input('recipient_name'),
            'recipient_phone' => $request->input('recipient_phone'),
            'recipient_address' => $request->input('recipient_address'),
            'total_price' => $totalPrice,
            'shipping_fee' => $request->input('shipping_fee', 0), 
            'payment_method' => $request->input('payment_method', 'cod'), 
            'payment_status' => 'chua_thanh_toan',
            'status' => 'cho_xac_nhan'
        ]);

        // Lưu chi tiết đơn hàng vào bảng 'order_details'
        DB::table('oder_details')->insert([
            'product_variant_id' => $variant->id,
            'oder_id' => $order->id,
            'price' => $price,
            'quantity' => $quantityToBuy,
            'total_price' => $totalPrice,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::commit();

        return response()->json([
            'message' => 'Đơn hàng đã được đặt, chờ xác nhận!',
            'oder' => $order,
            'product_variant' => [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'product_id' => $variant->product_id,
                'image' => $variant->product->image,
                'price' => $variant->price,
                'promotional_price' => $variant->promotional_price,
                'quantity' => $quantityToBuy
            ]
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
        $order = Oder::find($order_id); 

        if (!$order) {
            return response()->json(['message' => 'Đơn hàng không tồn tại'], 404);
        }

        if ($order->status !== 'cho_xac_nhan') {
            return response()->json(['message' => 'Đơn hàng đã được xử lý trước đó'], 400);
        }

        // Lấy danh sách sản phẩm trong đơn hàng
        $orderDetails = $order->orderDetails;

        foreach ($orderDetails as $detail) {
            $variant = ProductVariant::find($detail->product_variant_id);

            if (!$variant) {
                return response()->json(['message' => 'Sản phẩm không tồn tại'], 404);
            }

            if ($variant->quantity < $detail->quantity) {
                return response()->json(['message' => 'Kho không đủ hàng để xác nhận đơn'], 400);
            }

            // Cập nhật số lượng sản phẩm
            $variant->quantity -= $detail->quantity;
            $variant->save();
        }

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
