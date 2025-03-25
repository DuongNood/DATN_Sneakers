<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class StatisticsController extends Controller
{
    public function index()
    {
        
        $totalRevenue = Order::sum('total_price');

        
        $totalOrders = Order::count();
        
        $bestSellingProducts = OrderDetail::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->with('product')
            ->take(5)
            ->get();

        
        $topUsers = Order::select('user_id', DB::raw('COUNT(id) as total_orders'))
            ->groupBy('user_id')
            ->orderByDesc('total_orders')
            ->with('user')
            ->take(5)
            ->get();

        return view('admin.statistics.index', compact('totalRevenue', 'totalOrders', 'bestSellingProducts', 'topUsers'));
    }
}

