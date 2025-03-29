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
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'product_size_id' => 'nullable|exists:product_sizes,id'
        ]);

        $user = Auth::user();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        // Kiểm tra nếu sản phẩm có cùng product_id và product_size_id đã tồn tại
        $existingCartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->where('product_size_id', $request->product_size_id)
            ->first();

        if ($existingCartItem) {
            return response()->json(['message' => 'Sản phẩm với kích thước này đã có trong giỏ hàng'], 400);
        }

        // Lấy thông tin sản phẩm và kích thước
        $product = Product::find($request->product_id);
        $productSize = $request->product_size_id ? ProductSize::find($request->product_size_id) : null;

        // Nếu không có sản phẩm hoặc kích thước, trả về lỗi
        if (!$product) {
            return response()->json(['message' => 'Sản phẩm không tồn tại'], 404);
        }

        // Nếu có product_size_id nhưng không có kích thước, trả về lỗi
        if ($request->product_size_id && !$productSize) {
            return response()->json(['message' => 'Kích thước sản phẩm không tồn tại'], 404);
        }

        // Thêm sản phẩm vào giỏ hàng
        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'product_size_id' => $request->product_size_id,
        ]);

        // Tạo dữ liệu trả về với tên sản phẩm và tên kích thước
        $responseData = [
            'message' => 'Sản phẩm đã được thêm vào giỏ hàng',
            'product_name' => $product->product_name,
            'name' => $productSize ? $productSize->name : 'Không có kích thước',
            'quantity' => $cartItem->quantity
        ];

        return response()->json($responseData, 200);
    }
    public function listCart()
{
    $user = Auth::user();
    
    // Lấy giỏ hàng của người dùng, nếu không có thì tạo mới
    $cart = Cart::firstOrCreate(['user_id' => $user->id]);

    // Lấy tất cả các item trong giỏ hàng với eager load product và productSize
    $cartItems = CartItem::where('cart_id', $cart->id)
        ->with(['product', 'productSize']) // Eager load product và productSize
        ->get();

    // Tạo mảng trả về dữ liệu
    $responseData = $cartItems->map(function($cartItem) {
        return [
            'product_name' => $cartItem->product ? $cartItem->product->product_name : 'Sản phẩm không tồn tại', // Kiểm tra sự tồn tại của product
            'name' => $cartItem->productSize ? $cartItem->productSize->name : 'Không có kích thước', // Kiểm tra sự tồn tại của productSize
            'quantity' => $cartItem->quantity, // Số lượng
            'original_price' => $cartItem->product ? $cartItem->product->original_price : 0, // Giá gốc
            'discounted_price' => $cartItem->product ? $cartItem->product->discounted_price : 0, // Giá đã giảm
            'total' => $cartItem->quantity * ($cartItem->product ? $cartItem->product->discounted_price : 0) // Tổng tiền
        ];
    });
    return response()->json([
        'message' => 'Danh sách giỏ hàng',
        'cart_items' => $responseData
    ], 200);
}

}
