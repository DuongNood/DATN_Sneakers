<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['productVariant', 'brand']);

        // 🔍 Tìm kiếm theo tên hoặc mã sản phẩm
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'LIKE', "%$search%")
                  ->orWhere('product_code', 'LIKE', "%$search%");
            });
        }

        // 📂 Lọc theo danh mục
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 🏷️ Lọc theo thương hiệu
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // 💰 Lọc theo khoảng giá trong bảng products
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $minPrice = (int) $request->input('min_price', 0);
            $maxPrice = (int) $request->input('max_price', PHP_INT_MAX);

            if ($minPrice > $maxPrice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Giá tối thiểu không được lớn hơn giá tối đa.'
                ], 422);
            }

            $query->where(function ($q) use ($minPrice, $maxPrice) {
                $q->whereBetween('original_price', [$minPrice, $maxPrice])
                  ->orWhereBetween('discounted_price', [$minPrice, $maxPrice]);
            });
        }

        // 🛒 Lọc theo SKU
        if ($request->filled('sku')) {
            $sku = $request->sku;
            $query->whereHas('productVariant', function ($q) use ($sku) {
                $q->where('sku', 'LIKE', "%$sku%");
            });
        }

        // ✅ Lọc theo trạng thái biến thể
        if ($request->filled('variant_status')) {
            $variantStatus = filter_var($request->variant_status, FILTER_VALIDATE_BOOLEAN);
            $query->whereHas('productVariant', function ($q) use ($variantStatus) {
                $q->where('status', $variantStatus);
            });
        }

        // 📦 Lấy danh sách và format
        $products = $query->paginate(12)->through(function ($product) {
            $firstVariant = $product->productVariant->first();

            return [
                'id' => $product->id,
                'product_code' => $product->product_code,
                'product_name' => $product->product_name,
                'image' => $product->image,
                'description' => $product->description,
                'original_price' => $product->original_price,
                'discounted_price' => $product->discounted_price,
                'category_id' => $product->category_id,
                'brand_id' => $product->brand_id,
                'brand_name' => $product->brand->brand_name ?? null,
                'gender_id' => $product->gender_id,
                'gender_name' => $product->gender->gender_name ?? null,
                'care_instructions' => $product->care_instructions,
                'view' => $product->view,
                'status' => (bool) $product->status,
                'is_show_home' => (bool) $product->is_show_home,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
}

