<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Http\Request;


class ProductController extends Controller
{
    /**
     * Lấy danh sách sản phẩm cùng thông tin biến thể đầu tiên
     */
    public function index(Request $request)
{
    $query = Product::with('variants');

    // 🔍 Tìm kiếm theo tên hoặc mã sản phẩm
    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where(function ($q) use ($search) {
            $q->where('product_name', 'LIKE', '%' . $search . '%')
              ->orWhere('product_code', 'LIKE', '%' . $search . '%');
        });
    }

    // 🗂 Lọc theo danh mục
    if ($request->filled('category_id')) {
        $query->where('category_id', $request->input('category_id'));
    }

    // 💰 Lọc theo khoảng giá (giá gốc hoặc giá khuyến mãi trong biến thể)
    if ($request->filled('min_price') || $request->filled('max_price')) {
        $minPrice = $request->input('min_price', 0);
        $maxPrice = $request->input('max_price', PHP_INT_MAX);

        $query->whereHas('variants', function ($q) use ($minPrice, $maxPrice) {
            $q->where(function ($q) use ($minPrice, $maxPrice) {
                $q->whereBetween('price', [$minPrice, $maxPrice])
                  ->orWhereBetween('promotional_price', [$minPrice, $maxPrice]);
            });
        });
    }

    // 📦 Lọc theo biến thể (ví dụ: SKU, trạng thái)
    if ($request->filled('sku')) {
        $sku = $request->input('sku');
        $query->whereHas('variants', function ($q) use ($sku) {
            $q->where('sku', 'LIKE', '%' . $sku . '%');
        });
    }

    if ($request->filled('variant_status')) {
        $variantStatus = filter_var($request->input('variant_status'), FILTER_VALIDATE_BOOLEAN);
        $query->whereHas('variants', function ($q) use ($variantStatus) {
            $q->where('status', $variantStatus);
        });
    }

    $products = $query->get()->map(function ($product) {
        // Lấy biến thể đầu tiên (nếu có) để hiển thị giá và số lượng
        $firstVariant = $product->variants->first();
        
        return [
            'id' => $product->id,
            'product_code' => $product->product_code,
            'product_name' => $product->product_name,
            'image' => $product->image,
            'description' => $product->description,
            'status' => $product->status,
            'is_show_home' => $product->is_show_home,
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
            'original_price' => $firstVariant ? $firstVariant->price : null,
            'discounted_price' => $firstVariant ? $firstVariant->promotional_price : null,
            'quantity' => $firstVariant ? $firstVariant->quantity : null,
        ];
    });

    return response()->json([
        'success' => true,
        'data' => $products
    ]);
}

}
