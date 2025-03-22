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

   
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|string',
            'status' => 'boolean',
        ]);

        $banner = Banner::create($request->all());
        return response()->json($banner, 201);
    }

   
    public function show($id)
    {
        $banner = Banner::findOrFail($id);
        return response()->json($banner);
    }

  
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'image' => 'sometimes|string',
            'status' => 'sometimes|boolean',
        ]);

        $banner = Banner::findOrFail($id);
        $banner->update($request->all());
        return response()->json($banner);
    }

  
    public function destroy($id)
    {
        Banner::destroy($id);
        return response()->json(null, 204);
    }
}