<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // Lấy danh sách giỏ hàng của user
    public function index()
    {
        $cart = Cart::where('user_id', Auth::id())->with('items.product')->first();
        return response()->json($cart);
    }

    // Thêm sản phẩm vào giỏ hàng
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:product_variants,product_id',
            'product_size_id' => 'required|exists:product_variants,product_size_id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        
        $variant = ProductVariant::where('product_id', $request->product_id)
            ->where('product_size_id', $request->product_size_id)
            ->first();

        if (!$variant || $variant->quantity < $request->quantity) {
            return response()->json(['message' => 'Không đủ hàng trong kho'], 400);
        }

        $cartItem = CartItem::updateOrCreate(
            ['cart_id' => $cart->id, 'product_id' => $request->product_id, 'product_size_id' => $request->product_size_id],
            ['quantity' => $request->quantity]
        );

        return response()->json(['message' => 'Đã thêm vào giỏ hàng', 'cart' => $cart]);
    }

    // Cập nhật số lượng sản phẩm trong giỏ hàng
    public function updateCartItem(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        
        $cartItem = CartItem::findOrFail($id);
        $variant = ProductVariant::where('product_id', $cartItem->product_id)
            ->where('product_size_id', $cartItem->product_size_id)
            ->first();

        if ($variant->quantity < $request->quantity) {
            return response()->json(['message' => 'Không đủ hàng trong kho'], 400);
        }

        $cartItem->update(['quantity' => $request->quantity]);
        return response()->json(['message' => 'Cập nhật thành công', 'cartItem' => $cartItem]);
    }

    // Xóa sản phẩm khỏi giỏ hàng
    public function removeCartItem($id)
    {
        $cartItem = CartItem::findOrFail($id);
        $cartItem->delete();
        return response()->json(['message' => 'Xóa sản phẩm khỏi giỏ hàng']);
    }
}



