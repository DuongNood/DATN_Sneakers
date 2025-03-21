<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Oder;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class StatisticsController extends Controller
{
  
    public function totalRevenue()
    {
        $totalRevenue = Oder::sum('total_price');
        return response()->json(['total_revenue' => $totalRevenue]);
    }

   
    public function totalOrders()
    {
        $totalOrders = Oder::count();
        return response()->json(['total_orders' => $totalOrders]);
    }

 
    public function bestSellingProducts()
    {
        $bestSellingProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->with('product')
            ->take(5)
            ->get();
        return response()->json($bestSellingProducts);
    }

  
    public function topCustomers()
    {
        $topCustomers = Oder::select('customer_id', DB::raw('COUNT(id) as total_orders'))
            ->groupBy('customer_id')
            ->orderByDesc('total_orders')
            ->with('customer')
            ->take(5)
            ->get();

        return response()->json($topCustomers);
    }
}
