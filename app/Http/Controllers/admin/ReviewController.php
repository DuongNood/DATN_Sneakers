<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    //
    public function index()
    {
        //
        $title = "Danh sách danh mục";
        $productReview = ProductReview::paginate(10);  
        return view("admin.product_review.index", compact("title","productReview"));

    }
}
