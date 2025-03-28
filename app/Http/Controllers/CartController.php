<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\ProductSize;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function addToCart(Request $request)
{
    $request->validate([
        'products_id' => 'required|exists:products,id',
        'product_size_id' => 'required|exists:product_sizes,id',
        'quantity' => 'required|integer|min:1'
    ]);

    // Tìm biến thể sản phẩm theo product_id và product_size_id
    $productVariant = ProductVariant::where('product_id', $request->products_id)
        ->where('product_size_id', $request->product_size_id)
        ->with(['product', 'productSize']) // Lấy luôn thông tin sản phẩm và size
        ->first();

    if (!$productVariant) {
        return response()->json(['message' => 'Sản phẩm với kích cỡ này không tồn tại'], 404);
    }

    // Kiểm tra số lượng tồn kho
    if ($request->quantity > $productVariant->quantity) {
        return response()->json(['message' => 'Số lượng sản phẩm trong kho không đủ'], 400);
    }

    // Lấy giỏ hàng của user hiện tại (hoặc tạo mới nếu chưa có)
    $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

    // Kiểm tra xem sản phẩm cùng kích cỡ đã có trong giỏ hàng chưa
    $cartItem = CartItem::where([
        'cart_id' => $cart->id,
        'products_id' => $productVariant->id,
    ])->first();

    if ($cartItem) {
        return response()->json(['message' => 'Sản phẩm này đã có trong giỏ hàng'], 400);
    }

    // Nếu chưa có thì thêm mới
    $cartItem = CartItem::create([
        'cart_id' => $cart->id,
        'products_id' => $productVariant->id,
        'quantity' => $request->quantity
    ]);

    // Lấy lại thông tin để trả về đầy đủ chi tiết
    $cartItem->load(['productVariant.product', 'productVariant.productSize']);

    return response()->json([
        'message' => 'Đã thêm vào giỏ hàng',
        'cartItem' => [
            'id' => $cartItem->id,
            'quantity' => $cartItem->quantity,
            'product' => [
                'id' => $cartItem->productVariant->product->id,
                'product_name' => $cartItem->productVariant->product->product_name,
                'size' => $cartItem->productVariant->productSize->name
            ]
        ]
    ]);
}

    



    // 📜 Lấy danh sách sản phẩm trong giỏ hàng của người dùng
    public function getCart()
{
    $user = Auth::user();

    $cart = Cart::where('user_id', $user->id)
        ->with([
            'cartItems.productVariant.product',  // Lấy thông tin sản phẩm
            'cartItems.productVariant.productSize' // Lấy kích cỡ sản phẩm
        ])
        ->first();

    if (!$cart) {
        return response()->json(['message' => 'Giỏ hàng trống'], 200);
    }

    return response()->json(['cart' => $cart]);
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
