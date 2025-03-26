<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductSize;
use Str;

class OrderController extends Controller
{
    public function buyProductByName(Request $request, $product_name)
{
    $request->validate([
        'product_size_id' => 'required|integer', // Đây là ID của size
        'quantity' => 'required|integer|min:1'
    ]);

    $user = Auth::user();
    
    // Tìm sản phẩm theo tên
    $product = Product::where('product_name', $product_name)->firstOrFail();

    // Lấy thông tin kích cỡ sản phẩm
    $productSize = ProductSize::find($request->product_size_id);
    if (!$productSize) {
        return response()->json(['message' => 'Không tìm thấy kích cỡ sản phẩm!'], 404);
    }

    // Lấy giá từ products (ưu tiên discounted_price nếu có, ngược lại dùng original_price)
    $price = $product->discounted_price ?? $product->original_price;

    if (!$price) {
        return response()->json(['message' => 'Sản phẩm chưa có giá!'], 400);
    }

    $totalPrice = $price * $request->quantity;
    $orderCode = 'ORD' . strtoupper(Str::random(10));

    $order = Order::create([
        'user_id' => $user->id,
        'order_code' => $orderCode,
        'recipient_name' => $user->name,
        'recipient_phone' => $user->phone ?? 'N/A',
        'recipient_address' => $user->address ?? 'N/A',
        'total_price' => $totalPrice,
        'shipping_fee' => 0,
        'payment_method' => 'COD',
        'payment_status' => 'chua_thanh_toan',
        'status' => 'cho_xac_nhan',
    ]);

    OrderDetail::create([
        'order_id' => $order->id,
        'product_id' => $product->id, 
        'product_size_id' => $productSize->id, // Lưu lại ID của size
        'size_name' => $productSize->name, // Lưu thêm tên size
        'quantity' => $request->quantity,
        'price' => $price,
        'discount' => $product->discounted_price ? ($product->original_price - $product->discounted_price) : 0,
        'total_price' => $totalPrice,
    ]);

    return response()->json([
        'message' => 'Đặt hàng thành công!',
        'order' => $order,
        'size' => $productSize->name // Trả về luôn thông tin size
    ], 201);
}



public function confirmOrder($order_id)
{
    DB::beginTransaction();
    try {
        // Tìm đơn hàng theo order_id
        $order = Order::with('orderDetails')->where('id', $order_id)->first();

        if (!$order) {
            return response()->json(['message' => 'Đơn hàng không tồn tại'], 404);
        }

        if ($order->status !== 'cho_xac_nhan') {
            return response()->json(['message' => 'Đơn hàng đã được xử lý trước đó'], 400);
        }

        if ($order->orderDetails->isEmpty()) {
            return response()->json(['message' => 'Đơn hàng không có sản phẩm'], 400);
        }

        foreach ($order->orderDetails as $item) {
            if (!$item->product_size_id) {
                return response()->json(['message' => 'Thiếu product_size_id trong order_details'], 400);
            }

            // Tìm sản phẩm theo kích cỡ
            $productSize = ProductSize::find($item->product_size_id);

            if (!$productSize) {
                return response()->json(['message' => 'Kích cỡ sản phẩm không tồn tại'], 404);
            }

            if ($productSize->quantity < $item->quantity) {
                return response()->json(['message' => 'Kho không đủ hàng để xác nhận đơn'], 400);
            }

            // Trừ số lượng sản phẩm trong kho
            $productSize->quantity -= $item->quantity;
            $productSize->save();
        }

        // ✅ Cập nhật trạng thái đơn hàng trong bảng orders
        $order->status = 'da_xac_nhan';
        $order->save();

        DB::commit();

        return response()->json(['message' => 'Đơn hàng đã được xác nhận thành công!'], 200);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'Lỗi khi xác nhận đơn hàng', 'error' => $e->getMessage()], 500);
    }
}





}
