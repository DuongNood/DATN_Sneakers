<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // 🛒 Thêm sản phẩm vào giỏ hàng (KHÔNG vượt quá số lượng trong kho)
    public function addToCart(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|exists:carts,id',
            'products_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1'
        ]);

        // Lấy thông tin sản phẩm từ kho
        $productVariant = ProductVariant::findOrFail($request->products_id);

        // Kiểm tra số lượng tồn kho
        if ($request->quantity > $productVariant->quantity) {
            return response()->json(['message' => 'Số lượng sản phẩm trong kho không đủ'], 400);
        }

        // Kiểm tra nếu sản phẩm đã có trong giỏ -> cập nhật số lượng
        $cartItem = CartItem::where([
            'cart_id' => $request->cart_id,
            'products_id' => $request->products_id
        ])->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $request->quantity;
            if ($newQuantity > $productVariant->quantity) {
                return response()->json(['message' => 'Số lượng sản phẩm trong kho không đủ'], 400);
            }
            $cartItem->quantity = $newQuantity;
            $cartItem->save();
        } else {
            // Nếu chưa có thì thêm mới
            $cartItem = CartItem::create([
                'cart_id' => $request->cart_id,
                'products_id' => $request->products_id,
                'quantity' => $request->quantity
            ]);
        }

        return response()->json(['message' => 'Đã thêm vào giỏ hàng', 'cartItem' => $cartItem]);
    }

    // 📜 Lấy danh sách sản phẩm trong giỏ
    public function getCart()
    {
        $carts = Cart::with('cartItems.product')->get();
        return response()->json(['carts' => $carts]);
    }

    // ✏️ Cập nhật số lượng sản phẩm trong giỏ (KHÔNG vượt quá số lượng trong kho)
    public function updateCart(Request $request)
    {
        $request->validate([
            'cart_item_id' => 'required|exists:cart_items,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = CartItem::findOrFail($request->cart_item_id);
        $productVariant = ProductVariant::findOrFail($cartItem->products_id);

        // Kiểm tra số lượng tồn kho
        if ($request->quantity > $productVariant->quantity) {
            return response()->json(['message' => 'Số lượng sản phẩm trong kho không đủ'], 400);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json(['message' => 'Đã cập nhật giỏ hàng', 'cartItem' => $cartItem]);
    }

    // ❌ Xóa sản phẩm khỏi giỏ hàng
    public function removeFromCart($cart_item_id)
    {
        $cartItem = CartItem::findOrFail($cart_item_id);
        $cartItem->delete();

        return response()->json(['message' => 'Đã xóa sản phẩm khỏi giỏ hàng']);
    }
}

