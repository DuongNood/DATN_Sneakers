<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $news = News::all();

        return response()->json([
            'status' => true,
            'message' => 'Hiển thị tin tức thành công',
            'data' => $news
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $news = News::find($id);

        if ($news) {
            return response()->json([
                'status' => true,
                'message' => 'Hiển thị thành công tin tức có id=' . $id,
                'data' => $news
            ]);
        }
        return response()->json([
            'message' => 'Không tìm thấy tin tức có id=' . $id,
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
