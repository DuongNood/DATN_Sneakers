<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class FilterProductsController extends Controller
{
    public function filterProducts(Request $request)
    {
        $query = Product::query()->with(['variants', 'variants.size', 'category', 'brand']);

        // Lọc theo tên sản phẩm (tìm gần đúng)
        if ($request->filled('product_name')) {
            $query->where('product_name', 'like', '%' . $request->product_name . '%');
        }

        // Lọc theo danh mục
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Lọc theo thương hiệu
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // Lọc theo khoảng giá (ưu tiên discounted_price, nếu không có thì dùng original_price)
        if ($request->filled('min_price')) {
            $query->where(function ($q) use ($request) {
                $q->where('discounted_price', '>=', $request->min_price)
                  ->orWhere('original_price', '>=', $request->min_price);
            });
        }

        if ($request->filled('max_price')) {
            $query->where(function ($q) use ($request) {
                $q->where('discounted_price', '<=', $request->max_price)
                  ->orWhere('original_price', '<=', $request->max_price);
            });
        }

        // Lọc theo trạng thái sản phẩm (active/hidden...)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo hiển thị trang chủ
        if ($request->filled('is_show_home')) {
            $query->where('is_show_home', $request->is_show_home);
        }

        // Lọc theo ngày tạo
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Lọc theo 1 hoặc nhiều size
        if ($request->filled('product_size_ids')) {
            $sizeIds = is_array($request->product_size_ids)
                ? $request->product_size_ids
                : [$request->product_size_ids];

            $query->whereHas('variants', function ($q) use ($sizeIds) {
                $q->whereIn('product_size_id', $sizeIds);
            });
        }

        // Sắp xếp theo trường chỉ định
        if ($request->filled('sort_by')) {
            $sortField = $request->sort_by; // VD: 'view', 'created_at', 'original_price'
            $sortDirection = $request->get('sort_direction', 'desc');

            // Kiểm tra trường hợp 'sort_by' có hợp lệ không (optional)
            $validSortFields = ['view', 'created_at', 'original_price', 'discounted_price'];
            if (in_array($sortField, $validSortFields)) {
                $query->orderBy($sortField, $sortDirection);
            }
        }

        // Trả về kết quả phân trang
        $products = $query->paginate(12);

        // Trả về dữ liệu phân trang cùng thông tin khác
        return response()->json([
            'current_page' => $products->currentPage(),
            'total_pages' => $products->lastPage(),
            'total_items' => $products->total(),
            'data' => $products->items()
        ]);
    }
}

