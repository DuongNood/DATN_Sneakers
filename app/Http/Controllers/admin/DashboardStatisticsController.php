<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardStatisticsController extends Controller
{
    public function getDailyData()
    {
        $today = Carbon::now();

        // Thống kê doanh thu theo ngày
        $dailySales = Order::whereDate('created_at', $today)->sum('total_price');

        // Thống kê số lượng đơn hàng theo ngày
        $dailyOrderCount = Order::whereDate('created_at', $today)->count();

        // Sản phẩm bán chạy nhất trong ngày
        $bestSellingProduct = OrderDetail::select('products.product_name', DB::raw('SUM(order_details.quantity) as total_sold'))
            ->join('product_variants', 'order_details.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->whereDate('orders.created_at', $today)
            ->groupBy('products.id', 'products.product_name')
            ->orderByDesc('total_sold')
            ->first();

        // Sản phẩm hot nhất trong ngày (dựa trên lượt xem)
        $hotProduct = Product::whereDate('created_at', $today)->orderByDesc('view')->first();

        // Lấy danh sách khách hàng mới đăng ký trong ngày
        $newCustomers = User::whereDate('created_at', $today)->limit(5)->get();

        // Lấy danh sách đơn hàng gần đây
        $recentOrders = Order::whereDate('created_at', $today)->orderByDesc('created_at')->limit(5)->get();

        // Lấy dữ liệu doanh thu theo giờ cho biểu đồ thời gian thực
        $hourlySales = Order::select(DB::raw('HOUR(created_at) as hour'), DB::raw('SUM(total_price) as revenue'))
            ->whereDate('created_at', $today)
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        return response()->json([
            'daily_sales' => $dailySales,
            'daily_order_count' => $dailyOrderCount,
            'best_selling_product' => $bestSellingProduct,
            'hot_product' => $hotProduct,
            'new_customers' => $newCustomers,
            'recent_orders' => $recentOrders,
            'hourly_sales' => $hourlySales,
        ]);
    }
}