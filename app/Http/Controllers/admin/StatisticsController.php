<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index()
    {
        return view('admin.statistics.index');
    }

    public function getData(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subMonth());
        $endDate = $request->input('end_date', Carbon::now());

        // Thống kê doanh thu theo ngày
        $salesData = Order::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_price) as revenue'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Danh sách sản phẩm bán chạy (thông qua bảng product_variants)
        $bestSellingProducts = Product::select('products.product_name', DB::raw('SUM(order_details.quantity) as total_sold'))
            ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->join('order_details', 'product_variants.id', '=', 'order_details.product_variant_id')
            ->groupBy('products.id', 'products.product_name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Thống kê theo danh mục (liên kết thông qua product_variants)
        $categorySales = OrderDetail::select('categories.category_name', DB::raw('SUM(order_details.quantity) as total_sold'))
            ->join('product_variants', 'order_details.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->groupBy('categories.id', 'categories.category_name')
            ->orderByDesc('total_sold')
            ->get();

        // Top khách hàng theo tổng số tiền đã chi tiêu
        $topCustomers = Order::select('users.name', 'users.email', DB::raw('SUM(orders.total_price) as total_spent'))
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get();

        return response()->json([
            'sales' => $salesData,
            'best_sellers' => $bestSellingProducts,
            'category_sales' => $categorySales,
            'top_customers' => $topCustomers
        ]);
    }
}
