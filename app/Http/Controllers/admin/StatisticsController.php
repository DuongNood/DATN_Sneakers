<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class StatisticsController extends Controller
{
    public function index()
    {
        
        $totalRevenue = Order::sum('total_price');

        
        $totalOrders = Order::count();
        
        $bestSellingProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->with('product')
            ->take(5)
            ->get();

        
        $topCustomers = Order::select('customer_id', DB::raw('COUNT(id) as total_orders'))
            ->groupBy('customer_id')
            ->orderByDesc('total_orders')
            ->with('customer')
            ->take(5)
            ->get();

        return view('admin.statistics.index', compact('totalRevenue', 'totalOrders', 'bestSellingProducts', 'topCustomers'));
    }
}

