<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
        $data = $request->validate([
            'title' => 'required',
            'image' => 'required|mimes:jpg,jpeg,png,webp|max:2048',
            'content' => 'required'
        ]);

        try {
            if ($request->hasFile('image')) {
                $data['image'] = Storage::disk('public')->put('news', $request->file('image'));
            }
            News::query()->create($data);
            // dd($data);

            return response()->json([
                'status' => true,
                'message' => 'Thêm mới tin tức thành công',
                'data' => $data
            ], 201);

        } catch (\Throwable $th) {
            //throw $th;
            if (!empty($data['image']) && Storage::exists($data['image'])) {
                Storage::dick('public')->delete($data['image']);
            }
            Log::error(
                __CLASS__ . '@' . __FUNCTION__,
                ['error' => $th->getMessage()]
            );
            return response()->json([
                'status' => false,
                'message' => 'Lỗi hệ thống ' . $th 
            ], 500);
        }
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
        $data = $request->validate([
            'title' => 'required',
            'image' => 'nullable|mimes:jpg,jpeg,png,webp|max:2048',
            'content' => 'required'
        ]);

        $news = News::find($id);
        // dd($news);
        if (!$news) {
            return response()->json([
                'message' => 'Không tồn tại tin tức có id=' . $id
            ], 404);
        }

        try {

            if ($request->hasFile('image')) {
                $data['image'] = Storage::disk('public')->put('news', $request->file('image'));
            }

            $curentImage = $news->image;

            $news->update($data);
            // dd($data);
            if (
                $request->hasFile('image')
                && !empty($curentImage)
                && Storage::disk('public')->exists($curentImage)
            ) {
                Storage::disk('public')->delete($curentImage);
            }

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật tin tức thành công',
                'data' => $news
            ], 200);

        } catch (\Throwable $th) {
            //throw $th;
            if (!empty($data['image']) 
                && Storage::disk('public')->exists($data['image'])) 
            {
                Storage::disk('public')->delete($data['image']);
            }
            Log::error(
                __CLASS__ . '@' . __FUNCTION__,
                ['error' => $th->getMessage()]
            );
            return response()->json([
                'status' => false,
                'message' => 'Lỗi hệ thống ' . $th 
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        News::destroy($id);

        return response()->json([], 204);
    }
}