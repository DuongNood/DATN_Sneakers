<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['productVariant']);
        $query = Product::with(['productVariants', 'brand','gender']); // 👈 load thêm quan hệ brand

        //  Tìm kiếm theo tên hoặc mã sản phẩm
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'LIKE', '%' . $search . '%')
                  ->orWhere('product_code', 'LIKE', '%' . $search . '%');
            });
        }

        //  Lọc theo danh mục
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        //  Lọc theo thương hiệu
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        
    // 💰 Lọc theo khoảng giá (giá gốc hoặc giá khuyến mãi trong bảng products)
    if ($request->filled('min_price') || $request->filled('max_price')) {
        $minPrice = $request->input('min_price', 0);
        $maxPrice = $request->input('max_price', PHP_INT_MAX);
        
        if ($minPrice > $maxPrice) {
            return response()->json([
                'success' => false,
                'message' => 'Giá tối thiểu không được lớn hơn giá tối đa.'
            ], 422);
        }
        // Lọc theo giá trong bảng products
        $query->where(function ($q) use ($minPrice, $maxPrice) {
            $q->whereBetween('original_price', [$minPrice, $maxPrice])
              ->orWhereBetween('discounted_price', [$minPrice, $maxPrice]);
        });
    }

        // Lọc theo trạng thái sản phẩm
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        //  Lọc theo hiển thị trang chủ
        if ($request->filled('is_show_home')) {
            $query->where('is_show_home', $request->is_show_home);
        }

        //  Lấy kết quả và map lại với biến thể đầu tiên
        $products = $query->paginate(12)->through(function ($product) {
            $firstVariant = $product->productVariant->first();

            return [
                'id' => $product->id,
                'product_code' => $product->product_code,
                'product_name' => $product->product_name,
                'image' => $product->image,
                'description' => $product->description,
                'original_price' =>  $product->original_price,
                'discounted_price' =>  $product->discounted_price,
                'category_id' => $product->category_id,
                'brand_id' => $product->brand_id,
                'brand_name' => optional($product->brand)->brand_name,
                'gender_id' => $product->gender_id,
                'gender_name' => optional($product->gender)->gender_name,
                'care_instructions' => $product->care_instructions,
                'view' => $product->view,
                'status' =>$product->status, 
                'is_show_home' => (bool)$product->is_show_home,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }
}
