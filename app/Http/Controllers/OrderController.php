<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Models\Order; 
use App\Models\OrderDetail;
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
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_code' => 'OD' . time(), // Sinh mã đơn hàng tự động
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
            DB::table('order_details')->insert([
                'product_variant_id' => $variant->id,
                'order_id' => $order->id,
                'price' => $price,
                'quantity' => $quantityToBuy,
                'total_price' => $totalPrice,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Đơn hàng đã được đặt, chờ xác nhận!',
                'order' => $order,
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
    public function confirmOrder($id)
    {
        DB::beginTransaction();
    
        try {
            $order = Order::with('orderDetails')->find($id); // Lấy đơn hàng kèm chi tiết đơn hàng
    
            if (!$order) {
                return response()->json(['message' => 'Đơn hàng không tồn tại'], 404);
            }
    
            if ($order->status !== 'cho_xac_nhan') {
                return response()->json(['message' => 'Đơn hàng đã được xử lý trước đó'], 400);
            }
    
            if ($order->orderDetails->isEmpty()) {
                return response()->json(['message' => 'Đơn hàng không có sản phẩm'], 400);
            }
    
            foreach ($order->orderDetails as $detail) {
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
    
            // Xóa tất cả chi tiết đơn hàng trước
            OrderDetail::where('order_id', $id)->delete();
            // Xóa đơn hàng
            $order->delete();
            DB::commit();
    
            return response()->json([
                'message' => 'Đơn hàng đã được xác nhận và xóa thành công!',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Lỗi khi xác nhận đơn hàng', 'error' => $e->getMessage()], 500);
        }
    }
}
