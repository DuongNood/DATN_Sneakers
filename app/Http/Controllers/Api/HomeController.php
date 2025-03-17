<?php

namespace App\Http\Controllers\api;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    //
    public function getHomeProducts(): JsonResponse
    {
        $products = Product::with('category', 'variants')
            ->where('is_show_home', true)
            ->where('status', true)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
}
