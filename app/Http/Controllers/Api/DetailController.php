<?php

namespace App\Http\Controllers\api;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class DetailController extends Controller
{
    //
     public function getProductDetail($id): JsonResponse
    {
        $product = Product::with(['category', 'variants', 'imageProduct'])
            ->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không tồn tại!'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }
    public function getRelatedProducts($id)
    {
        $product = Product::findOrFail($id);

        // Lấy các sản phẩm cùng danh mục (loại trừ sản phẩm hiện tại)
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(5) // Giới hạn số lượng sản phẩm liên quan
            ->get();

        return response()->json([
            'success' => true,
            'data' => $relatedProducts
        ]);
    }
}
