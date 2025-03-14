<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function index()
    {
        
        $totalRevenue = Order::sum('total_price');

        
        $totalOrders = Order::count();

        
        $bestSellingProducts = OrderItem::select('product_id', \DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->with('product')
            ->take(5)
            ->get();

        
        $topCustomers = Order::select('customer_id', \DB::raw('COUNT(id) as total_orders'))
            ->groupBy('customer_id')
            ->orderByDesc('total_orders')
            ->with('customer')
            ->take(5)
            ->get();

        return view('statistics.index', compact('totalRevenue', 'totalOrders', 'bestSellingProducts', 'topCustomers'));
    }
}

