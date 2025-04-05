<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'product_size_id' => 'nullable|exists:product_sizes,id'
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Vui lòng đăng nhập để thêm vào giỏ hàng'], 401);
        }

        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $product = Product::find($request->product_id);
        $productSize = $request->product_size_id ? ProductSize::find($request->product_size_id) : null;

        if (!$product) {
            return response()->json(['message' => 'Sản phẩm không tồn tại'], 404);
        }

        if ($request->product_size_id && !$productSize) {
            return response()->json(['message' => 'Kích thước sản phẩm không tồn tại'], 404);
        }

        $productVariant = $productSize 
            ? ProductVariant::where('product_id', $product->id)
                            ->where('product_size_id', $productSize->id)
                            ->first()
            : null;

        $originalPrice = $product->original_price;
        $discountedPrice = $product->discounted_price ?? $originalPrice;

        $existingCartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->where('product_size_id', $request->product_size_id)
            ->first();

        if ($existingCartItem) {
            $newQuantity = $existingCartItem->quantity + $request->quantity;
            if ($productVariant && $newQuantity > $productVariant->quantity) {
                return response()->json(['message' => 'Số lượng vượt quá tồn kho'], 400);
            }

            $existingCartItem->update([
                'quantity' => $newQuantity,
                'total_price' => $newQuantity * $discountedPrice
            ]);

            return response()->json([
                'message' => 'Đã cập nhật số lượng sản phẩm trong giỏ hàng',
                'product_name' => $product->product_name,
                'size' => $productSize ? $productSize->name : 'Không có kích thước',
                'quantity' => $existingCartItem->quantity,
                'original_price' => $originalPrice,
                'discounted_price' => $discountedPrice,
                'total_price' => $existingCartItem->total_price
            ], 200);
        }

        if ($productVariant && $productVariant->quantity < $request->quantity) {
            return response()->json(['message' => 'Sản phẩm không đủ số lượng trong kho'], 400);
        }

        $totalPrice = $discountedPrice * $request->quantity;
        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'product_size_id' => $request->product_size_id,
            'original_price' => $originalPrice,
            'discounted_price' => $discountedPrice,
            'total_price' => $totalPrice,
        ]);

        Log::info('Sản phẩm đã được thêm vào giỏ hàng', [
            'user_id' => $user->id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
        ]);

        return response()->json([
            'message' => 'Sản phẩm đã được thêm vào giỏ hàng',
            'product_name' => $product->product_name,
            'size' => $productSize ? $productSize->name : 'Không có kích thước',
            'quantity' => $cartItem->quantity,
            'original_price' => $originalPrice,
            'discounted_price' => $discountedPrice,
            'total_price' => $totalPrice
        ], 200);
    }

    public function getCart()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Vui lòng đăng nhập để xem giỏ hàng'], 401);
        }

        $cart = Cart::where('user_id', $user->id)->first();
        if (!$cart) {
            return response()->json(['message' => 'Giỏ hàng trống', 'items' => [], 'total_cart_price' => 0], 200);
        }

        $cartItems = CartItem::where('cart_id', $cart->id)->get();
        $totalCartPrice = $cartItems->sum('total_price');

        $response = $cartItems->map(function ($item) {
            $product = Product::find($item->product_id);
            $size = ProductSize::find($item->product_size_id);

            if (!$product) {
                return null;
            }

            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $product->product_name,
                'image' => $product->image ?? 'https://via.placeholder.com/150',
                'quantity' => $item->quantity,
                'product_size_id' => $item->product_size_id,
                'size_name' => $size ? $size->name : 'Không có kích thước',
                'original_price' => $item->original_price, // Thêm original_price
                'discounted_price' => $item->discounted_price,
                'total_price' => $item->total_price,
            ];
        })->filter();

        return response()->json([
            'items' => $response->values(),
            'total_cart_price' => $totalCartPrice
        ], 200);
    }

    public function updateCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_size_id' => 'nullable|exists:product_sizes,id',
            'action' => 'required|in:increase,decrease',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Vui lòng đăng nhập để cập nhật giỏ hàng'], 401);
        }

        $cart = Cart::where('user_id', $user->id)->first();
        if (!$cart) {
            return response()->json(['message' => 'Giỏ hàng trống'], 400);
        }

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->where('product_size_id', $request->product_size_id)
            ->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Sản phẩm không có trong giỏ hàng'], 400);
        }

        $product = Product::find($request->product_id);
        $productVariant = $request->product_size_id 
            ? ProductVariant::where('product_id', $product->id)
                            ->where('product_size_id', $request->product_size_id)
                            ->first()
            : null;

        if ($request->action === 'increase') {
            if ($productVariant && $cartItem->quantity >= $productVariant->quantity) {
                return response()->json(['message' => 'Sản phẩm đã đạt số lượng tối đa trong kho'], 400);
            }
            $cartItem->quantity += 1;
        } elseif ($request->action === 'decrease') {
            if ($cartItem->quantity <= 1) {
                $cartItem->delete();
                return response()->json(['message' => 'Sản phẩm đã được xóa khỏi giỏ hàng'], 200);
            }
            $cartItem->quantity -= 1;
        }

        $discountedPrice = $product->discounted_price ?? $product->original_price;
        $cartItem->total_price = $discountedPrice * $cartItem->quantity;
        $cartItem->save();

        return response()->json([
            'message' => 'Cập nhật giỏ hàng thành công',
            'quantity' => $cartItem->quantity,
            'total_price' => $cartItem->total_price
        ], 200);
    }

    public function removeFromCart($cartItemId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Vui lòng đăng nhập để xóa sản phẩm khỏi giỏ hàng'], 401);
        }

        $cart = Cart::where('user_id', $user->id)->first();
        if (!$cart) {
            return response()->json(['message' => 'Giỏ hàng trống'], 400);
        }

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('id', $cartItemId)
            ->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Sản phẩm không có trong giỏ hàng'], 404);
        }

        $cartItem->delete();

        return response()->json(['message' => 'Sản phẩm đã được xóa khỏi giỏ hàng'], 200);
    }
}