<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Nhớ import Log


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

    // Lấy thông tin sản phẩm và kích thước
    $product = Product::find($request->product_id);
    $productSize = $request->product_size_id ? ProductSize::find($request->product_size_id) : null;

    if (!$product) {
        return response()->json(['message' => 'Sản phẩm không tồn tại'], 404);
    }

    if ($request->product_size_id && !$productSize) {
        return response()->json(['message' => 'Kích thước sản phẩm không tồn tại'], 404);
    }

    // Kiểm tra tồn kho
    $productVariant = $productSize 
        ? ProductVariant::where('product_id', $product->id)
                        ->where('product_size_id', $productSize->id)
                        ->first()
        : null;

    if ($productVariant && $productVariant->quantity < $request->quantity) {
        return response()->json(['message' => 'Sản phẩm không đủ số lượng trong kho'], 400);
    }

    // Lấy giá gốc và giá khuyến mãi
    $originalPrice = $product->original_price;
    $discountedPrice = $product->discounted_price ?? $originalPrice;
    $totalPrice = $discountedPrice * $request->quantity;

    // Kiểm tra nếu sản phẩm đã có trong giỏ hàng
    $existingCartItem = CartItem::where('cart_id', $cart->id)
        ->where('product_id', $request->product_id)
        ->where('product_size_id', $request->product_size_id)
        ->first();

        if ($existingCartItem) {
            return response()->json(['message' => 'Sản phẩm đã có trong giỏ hàng, không thể thêm nữa'], 400);
        }

    // Thêm sản phẩm mới vào giỏ hàng
    $cartItem = CartItem::create([
        'cart_id' => $cart->id,
        'product_id' => $request->product_id,
        'quantity' => $request->quantity,
        'product_size_id' => $request->product_size_id,
        'original_price' => $originalPrice,
        'discounted_price' => $discountedPrice,
        'total_price' => $totalPrice,
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

    // Lấy giỏ hàng của người dùng (nếu có)
    $cart = Cart::where('user_id', $user->id)->first();

    // Kiểm tra nếu giỏ hàng không tồn tại
    if (!$cart) {
        return response()->json(['message' => 'Giỏ hàng trống'], 200);
    }

    // Lấy các mục trong giỏ hàng (cart items)
    $cartItems = CartItem::where('cart_id', $cart->id)->get();

    // Trả về thông tin giỏ hàng cùng các sản phẩm trong giỏ
    $response = $cartItems->map(function ($item) {
        $product = Product::find($item->product_id);
        $size = ProductSize::where('id', $item->product_size_id)->first(); // Lấy variant tương ứng

        // Kiểm tra xem sản phẩm và variant có tồn tại không
        if (!$product || !$size) {
            return null; // Bỏ qua nếu không có sản phẩm hoặc variant
        }

        // Trả về thông tin chi tiết giỏ hàng
        return [
            'id' => $item->id,
            'cart_id' => $item->cart_id,
            'product_id' => $item->product_id,
            'product_name' => $product->product_name,
            'quantity' => $item->quantity,
            'created_at' => $item->created_at,
            'updated_at' => $item->updated_at,
            'product_size_id' => $item->product_size_id,
            'original_price' => $item->original_price,
            'discounted_price' => $item->discounted_price,
            'total_price' => $item->total_price,
            'name' => $size->name ?? 'Không có kích thước',
        ];
    });

    // Lọc bỏ các giá trị null
    $response = $response->filter();

    return response()->json($response->values(), 200);
}



public function updateCart(Request $request)
{
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'product_size_id' => 'nullable|exists:product_sizes,id',  // optional for variants
        'action' => 'required|in:increase,decrease',
    ]);

    $user = Auth::user();
    $cart = Cart::where('user_id', $user->id)->first();

    if (!$cart) {
        return response()->json(['error' => 'Giỏ hàng trống'], 400);
    }

    // Tìm sản phẩm trong giỏ hàng
    $cartItem = CartItem::where('cart_id', $cart->id)
        ->where('product_id', $request->product_id)
        ->where('product_size_id', $request->product_size_id)  // Kiểm tra cả product_size_id nếu có
        ->first();

    if (!$cartItem) {
        return response()->json(['error' => 'Sản phẩm không có trong giỏ hàng'], 400);
    }

    $product = Product::find($request->product_id);

    // Tìm biến thể sản phẩm nếu có product_size_id
    $productVariant = null;
    if ($request->product_size_id) {
        $productVariant = ProductVariant::where('product_id', $product->id)
            ->where('product_size_id', $request->product_size_id)
            ->first();
    }

    if ($request->action === 'increase') {
        // Kiểm tra số lượng tồn kho trước khi tăng
        if ($productVariant && $cartItem->quantity >= $productVariant->quantity) {
            return response()->json(['error' => 'Sản phẩm đã đạt số lượng tối đa trong kho'], 400);
        }
        $cartItem->quantity += 1;
    } else {
        // Giảm số lượng nhưng không được nhỏ hơn 1
        if ($cartItem->quantity > 1) {
            $cartItem->quantity -= 1;
        } else {
            return response()->json(['error' => 'Số lượng sản phẩm tối thiểu là 1'], 400);
        }
    }

    // Cập nhật tổng giá trị mỗi khi số lượng thay đổi
    $originalPrice = $product->original_price;
    $discountedPrice = $product->discounted_price ?? $originalPrice;
    $totalPrice = $discountedPrice * $cartItem->quantity;

    // Cập nhật lại total_price cho giỏ hàng
    $cartItem->total_price = $totalPrice;
    $cartItem->save();

    return response()->json([
        'message' => 'Cập nhật giỏ hàng thành công',
        'quantity' => $cartItem->quantity,
        'total_price' => $cartItem->total_price
    ], 200);
}

}
