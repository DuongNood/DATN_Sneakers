<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
<<<<<<< HEAD
    
    // Thêm sản phẩm vào giỏ hàng
=======
    // 🛒 Thêm sản phẩm vào giỏ hàng (KHÔNG vượt quá số lượng trong kho)
>>>>>>> parent of f0d2918 (Fix merge conflict in ProductController.php and api.php)
    public function addToCart(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|exists:carts,id',
            'products_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1'
        ]);

<<<<<<< HEAD
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
=======
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
>>>>>>> parent of f0d2918 (Fix merge conflict in ProductController.php and api.php)
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

