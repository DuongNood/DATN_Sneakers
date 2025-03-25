<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Category;

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
    public function getCategories(){
        $categories = Category::where('status', true)->get();
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
    public function categoryByProduct($id)
    {

        $category = Product::where('status', true)->where('category_id', $id)->get();

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }
}
