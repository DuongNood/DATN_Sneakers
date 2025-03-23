<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function getUserCart()
{
    $user = auth()->user();
    $cart = Cart::where('user_id', $user->id)->with('cartItems.product')->first();

    if (!$cart) {
        return response()->json(['message' => 'Giỏ hàng trống'], 200);
    }

    return response()->json([
        'message' => 'Giỏ hàng của bạn',
        'cart' => $cart
    ]);
}
public function getAllCarts()
{
    $carts = Cart::with('user', 'cartItems.product')->get();

    return response()->json([
        'message' => 'Danh sách giỏ hàng của tất cả users',
        'carts' => $carts
    ]);
}
public function addToCart(Request $request)
    {
        $request->validate([
            'products_id' => 'required|exists:products,id',
            // 'quantity' => 'required|integer|min:1',
        ]);

        $user = auth()->user();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        // Kiểm tra sản phẩm đã có trong giỏ hàng chưa
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('products_id', $request->products_id)
            ->first();

        if ($cartItem) {
            // Nếu có, cập nhật số lượng
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            // Nếu chưa, tạo mới
            CartItem::create([
                'cart_id' => $cart->id,
                'products_id' => $request->products_id,
                
            ]);
        }

        return response()->json(['message' => 'Thêm sản phẩm vào giỏ hàng thành công']);
    }


public function updateCartItem(Request $request, $id)
{
    $request->validate(['quantity' => 'required|integer|min:1']);

    $user = auth()->user();
    $cartItem = CartItem::whereHas('cart', function ($query) use ($user) {
        $query->where('user_id', $user->id);
    })->find($id);

    if (!$cartItem) {
        return response()->json(['message' => 'Sản phẩm không tồn tại trong giỏ hàng'], 404);
    }

    $cartItem->update(['quantity' => $request->quantity]);

    return response()->json(['message' => 'Cập nhật số lượng thành công']);
}
public function removeCartItem($id)
{
    $user = auth()->user();
    $cartItem = CartItem::whereHas('cart', function ($query) use ($user) {
        $query->where('user_id', $user->id);
    })->find($id);

    if (!$cartItem) {
        return response()->json(['message' => 'Sản phẩm không tồn tại trong giỏ hàng'], 404);
    }

    $cartItem->delete();

    return response()->json(['message' => 'Xóa sản phẩm khỏi giỏ hàng thành công']);
}
public function clearCart()
{
    $user = auth()->user();
    $cart = Cart::where('user_id', $user->id)->first();

    if (!$cart) {
        return response()->json(['message' => 'Giỏ hàng trống'], 200);
    }

    CartItem::where('cart_id', $cart->id)->delete();
    $cart->delete();

    return response()->json(['message' => 'Đã xóa toàn bộ giỏ hàng']);
}

}
