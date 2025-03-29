<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:product_variants,product_id',
            'product_size_id' => 'required|exists:product_variants,product_size_id',
            'quantity' => 'required|integer|min:1'
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Bạn chưa đăng nhập'], 401);
        }

        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        // Tìm biến thể sản phẩm dựa vào product_id và product_size_id
        $variant = ProductVariant::where('product_id', $request->product_id)
            ->where('product_size_id', $request->product_size_id)
            ->first();

        if (!$variant) {
            return response()->json(['message' => 'Biến thể sản phẩm không tồn tại'], 404);
        }

        // Kiểm tra số lượng tồn kho
        if ($request->quantity > $variant->quantity) {
            return response()->json(['message' => 'Số lượng không đủ trong kho'], 400);
        }

        // Kiểm tra sản phẩm đã có trong giỏ hàng chưa (chỉ so sánh product_id)
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cartItem) {
            // Nếu cùng product_id nhưng khác size, thêm mới
            if ($cartItem->product_size_id !== $request->product_size_id) {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity
                ]);
            } else {
                // Nếu cùng product_id và product_size_id, cập nhật số lượng
                $newQuantity = $cartItem->quantity + $request->quantity;

                if ($newQuantity > $variant->quantity) {
                    return response()->json(['message' => 'Số lượng không đủ trong kho'], 400);
                }

                $cartItem->update(['quantity' => $newQuantity]);
            }
        } else {
            // Thêm sản phẩm mới vào giỏ hàng
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity
            ]);
        }

        // Lấy thông tin tên sản phẩm và kích thước
        $product = Product::find($request->product_id);
        $size = ProductSize::find($request->product_size_id);
        $quantity = CartItem::find($request->quantity);
        return response()->json([
            'message' => 'Đã thêm vào giỏ hàng',
            'cart' => $cart,
            'product_name' => $product->product_name ?? 'Không xác định',
            'name' => $size->name ?? 'Không xác định',
            'quantity' => $quantity->quantity ?? 'Không xác định'
        ]);
    }
}
