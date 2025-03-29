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
    // Thêm sản phẩm vào giỏ hàng
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:product_variants,product_id',
            'product_size_id' => 'required|exists:product_variants,product_size_id',
            'quantity' => 'required|integer|min:1'
        ]);

        $user = Auth::user();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        $productVariant = ProductVariant::where('product_id', $request->product_id)
            ->where('product_size_id', $request->product_size_id)
            ->first();

        if (!$productVariant) {
            return response()->json(['message' => 'Sản phẩm không tồn tại'], 404);
        }

        if ($request->quantity > $productVariant->quantity) {
            return response()->json(['message' => 'Số lượng yêu cầu vượt quá kho'], 400);
        }
        
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('products_id', $request->product_id)
            ->where('product_size_id', $request->product_size_id)
            ->first();
        
        if ($cartItem) {
            return response()->json(['message' => 'Sản phẩm đã có trong giỏ hàng'], 400);
        }

        $product = Product::find($request->product_id);
        $productSize = ProductSize::find($request->product_size_id);

        $newCartItem = CartItem::create([
            'cart_id' => $cart->id,
            'products_id' => $request->product_id,
            'product_size_id' => $request->product_size_id,
            'quantity' => $request->quantity,
        ]);

        return response()->json([
            'message' => 'Thêm vào giỏ hàng thành công',
            'cart_item' => [
                'cart_id' => $newCartItem->cart_id,
                'products_id' => $newCartItem->products_id,
                'product_size_id' => $newCartItem->product_size_id,
                'quantity' => $newCartItem->quantity,
                'product_name' => $product->product_name,
                'product_size' => $productSize->name,
            ]
        ], 200);
    }
}
