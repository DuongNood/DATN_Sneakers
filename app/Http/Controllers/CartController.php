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
        try {
            DB::beginTransaction();

            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'size' => 'required|string'
            ]);

            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

            $productSize = ProductSize::where('name', $request->size)->first();
            if (!$productSize) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Không tìm thấy kích thước '$request->size'"
                ], 404);
            }

            $productVariant = ProductVariant::where('product_id', $request->product_id)
                ->where('product_size_id', $productSize->id)
                ->with('product')
                ->first();

            if (!$productVariant) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Không tìm thấy biến thể sản phẩm với kích thước '$request->size'"
                ], 404);
            }

            if ($productVariant->quantity < $request->quantity) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Số lượng tồn kho không đủ. Còn lại: {$productVariant->quantity}"
                ], 400);
            }

            $cartItem = CartItem::where([
                'cart_id' => $cart->id,
                'products_id' => $request->product_id, // Sửa thành product_id thay vì productVariant->id
                'size' => $request->size // Sử dụng size để kiểm tra uniqueness
            ])->first();

            if ($cartItem) {
                $newQuantity = $cartItem->quantity + $request->quantity;
                if ($newQuantity > $productVariant->quantity) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Số lượng tồn kho không đủ cho tổng số lượng yêu cầu. Còn lại: {$productVariant->quantity}"
                    ], 400);
                }
                $cartItem->quantity = $newQuantity;
                $cartItem->save();
            } else {
                $cartItem = CartItem::create([
                    'cart_id' => $cart->id,
                    'products_id' => $request->product_id, // Lưu product_id
                    'quantity' => $request->quantity,
                    'size' => $request->size
                ]);
            }

            $productVariant->quantity -= $request->quantity;
            $productVariant->save();

            DB::commit();

            $cartItem->load('productVariant.product');
            return response()->json([
                'status' => 'success',
                'message' => 'Đã thêm sản phẩm vào giỏ hàng',
                'data' => [
                    'id' => $cartItem->id,
                    'name' => $productVariant->product->name ?? 'Unknown Product',
                    'price' => (float) ($productVariant->product->original_price ?? 0),
                    'discount' => (float) ($productVariant->product->discounted_price ?? 0),
                    'quantity' => $cartItem->quantity,
                    'image' => $productVariant->product->image ?? 'https://via.placeholder.com/150',
                    'size' => $cartItem->size,
                    'selected' => true
                ]
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Dữ liệu đầu vào không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Add to cart failed: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }
}