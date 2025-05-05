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
    public function getDailyData(Request $request)
    {
        $today = Carbon::now();

        // Tính toán giá trị hiện tại và tăng trưởng cho doanh thu theo từng khoảng thời gian
        $dailySales = $this->calculatePeriodData('Day', 'Sale');
        $weeklySales = $this->calculatePeriodData('Week', 'Sale');
        $monthlySales = $this->calculatePeriodData('Month', 'Sale');
        $yearlySales = $this->calculatePeriodData('Year', 'Sale');

        // Tính toán giá trị hiện tại và tăng trưởng cho đơn hàng theo từng khoảng thời gian
        $dailyOrders = $this->calculatePeriodData('Day', 'Order');
        $weeklyOrders = $this->calculatePeriodData('Week', 'Order');
        $monthlyOrders = $this->calculatePeriodData('Month', 'Order');
        $yearlyOrders = $this->calculatePeriodData('Year', 'Order');

        // Thống kê hiện tại (theo bộ lọc nếu có, mặc định là ngày) - giữ nguyên để hiển thị trên các card lớn
        $period = $request->input('period', 'daily');
        $startDate = Carbon::now()->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        switch ($period) {
            case 'weekly':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'monthly':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'yearly':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
        }

        $totalSales = Order::whereBetween('created_at', [$startDate, $endDate])->sum('total_price');
        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $bestSellingProduct = $this->getBestSellingProduct($startDate, $endDate);
        $cancelledOrders = Order::whereBetween('created_at', [$startDate, $endDate])->where('status', 'huy_don_hang')->count();
        // $newCustomers = User::whereBetween('created_at', [$startDate, $endDate])->latest()->take(10)->get();
        // $recentOrders = Order::whereBetween('created_at', [$startDate, $endDate])->latest()->take(10)->get();

        return response()->json([
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'best_selling_product' => $bestSellingProduct,
            'cancelled_orders' => $cancelledOrders,
            // 'new_customers' => $newCustomers,
            // 'recent_orders' => $recentOrders,

            'daily_sales' => $dailySales,
            'weekly_sales' => $weeklySales,
            'monthly_sales' => $monthlySales,
            'yearly_sales' => $yearlySales,

            'daily_orders' => $dailyOrders,
            'weekly_orders' => $weeklyOrders,
            'monthly_orders' => $monthlyOrders,
            'yearly_orders' => $yearlyOrders,
        ]);
    }

    private function calculatePeriodData($periodType, $model = 'Sale')
    {
        $today = Carbon::now();
        $currentStart = $today->copy()->{'startOf' . $periodType}();
        $currentEnd = $today->copy()->{'endOf' . $periodType}();
        $previousStart = $today->copy()->{'sub' . $periodType}()->{'startOf' . $periodType}();
        $previousEnd = $today->copy()->{'sub' . $periodType}()->{'endOf' . $periodType}();

        $currentTotal = 0;
        $previousTotal = 0;

        if ($model === 'Sale') {
            $currentTotal = Order::whereBetween('created_at', [$currentStart, $currentEnd])->sum('total_price');
            $previousTotal = Order::whereBetween('created_at', [$previousStart, $previousEnd])->sum('total_price');
        } elseif ($model === 'Order') {
            $currentTotal = Order::whereBetween('created_at', [$currentStart, $currentEnd])->count();
            $previousTotal = Order::whereBetween('created_at', [$previousStart, $previousEnd])->count();
        }

        return [
            'current' => $currentTotal,
            'growth_rate' => $this->calculateGrowthRate($currentTotal, $previousTotal),
        ];
    }

    private function calculateGrowthRate($current, $previous)
    {
        if ($previous > 0) {
            return round((($current - $previous) / $previous) * 100, 2);
        }
        return $current > 0 ? 100 : 0;
    }

    private function getBestSellingProduct($startDate, $endDate)
    {
        return OrderDetail::select('products.product_name', DB::raw('SUM(order_details.quantity) as total_sold'))
            ->join('product_variants', 'order_details.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('products.id', 'products.product_name')
            ->orderByDesc('total_sold')
            ->value('product_name');
    }

    // public function getAllNewCustomers()
    // {
    //     $newCustomers = User::latest()->take(10)->get(); // Lấy 10 khách hàng mới nhất (bạn có thể điều chỉnh số lượng)
    //     return response()->json($newCustomers);
    // }

    // public function getAllRecentOrders()
    // {
    //     $recentOrders = Order::latest()->take(10)->get(); // Lấy 10 đơn hàng gần đây nhất (bạn có thể điều chỉnh số lượng)
    //     return response()->json($recentOrders);
    // }

    public function getRevenueLast30Days()
    {
        $endDate = Carbon::now()->endOfDay();
        $startDate = Carbon::now()->subDays(29)->startOfDay();

        $dailySales = Order::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_price) as revenue'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('revenue', 'date')
            ->toArray();

        return response()->json($dailySales);
    }

    // public function getCombinedStatuses()
    // {
    //     $paymentStatuses = Order::select('payment_status', DB::raw('count(*) as total'))
    //         ->groupBy('payment_status')
    //         ->get()
    //         ->pluck('total', 'payment_status')
    //         ->toArray();

    //     $orderStatuses = Order::select('status', DB::raw('count(*) as total'))
    //         ->groupBy('status')
    //         ->get()
    //         ->pluck('total', 'status')
    //         ->toArray();

    //     return response()->json([
    //         'payment_statuses' => $paymentStatuses,
    //         'order_statuses' => $orderStatuses,
    //     ]);
    // }
}
