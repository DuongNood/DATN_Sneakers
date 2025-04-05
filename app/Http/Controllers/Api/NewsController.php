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
        ], 200);
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

            $news = News::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Thêm mới tin tức thành công',
                'data' => $news
            ], 201);
        } catch (\Throwable $th) {
            if (!empty($data['image']) && Storage::disk('public')->exists($data['image'])) {
                Storage::disk('public')->delete($data['image']);
            }

            Log::error(__CLASS__ . '@' . __FUNCTION__, [
                'error' => $th->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Lỗi hệ thống, vui lòng thử lại sau',
                'data' => null
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
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Không tìm thấy tin tức có id=' . $id,
            'data' => null
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
        if (!$news) {
            return response()->json([
                'status' => false,
                'message' => 'Không tồn tại tin tức có id=' . $id,
                'data' => null
            ], 404);
        }

        try {
            if ($request->hasFile('image')) {
                $data['image'] = Storage::disk('public')->put('news', $request->file('image'));
            } else {
                $data['image'] = $news->image; // Giữ ảnh cũ nếu không upload ảnh mới
            }

            $currentImage = $news->image;

            $news->update($data);

            if (
                $request->hasFile('image') &&
                !empty($currentImage) &&
                Storage::disk('public')->exists($currentImage)
            ) {
                Storage::disk('public')->delete($currentImage);
            }

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật tin tức thành công',
                'data' => $news
            ], 200);
        } catch (\Throwable $th) {
            if (!empty($data['image']) && Storage::disk('public')->exists($data['image'])) {
                Storage::disk('public')->delete($data['image']);
            }

            Log::error(__CLASS__ . '@' . __FUNCTION__, [
                'error' => $th->getMessage(),
                'request_data' => $request->all(),
                'news_id' => $id
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Lỗi hệ thống, vui lòng thử lại sau',
                'data' => null
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $news = News::find($id);
        if (!$news) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy tin tức có id=' . $id,
                'data' => null
            ], 404);
        }

        try {
            if ($news->image && Storage::disk('public')->exists($news->image)) {
                Storage::disk('public')->delete($news->image);
            }

            $news->delete();

            return response()->json([
                'status' => true,
                'message' => 'Xóa tin tức thành công',
                'data' => null
            ], 204);
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [
                'error' => $th->getMessage(),
                'news_id' => $id
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Lỗi hệ thống, vui lòng thử lại sau',
                'data' => null
            ], 500);
        }
    }
}