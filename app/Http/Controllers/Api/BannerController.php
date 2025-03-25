<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    
    public function index()
    {
        $banners = Banner::where('status', true)->get();
        return response()->json($banners);
    }
}