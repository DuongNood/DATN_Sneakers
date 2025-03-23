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


        $news = $request->validate([
            'title' => 'required',
            'image' => 'required|mimes:jpg,jpeg,png,webp|max:2048',
            'content' => 'required'
        ]);

        try {
            if ($request->hasFile('image')) {

                $news['image'] = Storage::disk('public')->put('news', $request->file('image'));
            }
            News::query()->create($news);
            // dd($news);

            return response()->json([
                'status' => true,
                'message' => 'Thêm mới tin tức thành công',
                'news' => $news

            ], 201);

        } catch (\Throwable $th) {
            //throw $th;

            if (!empty($news['image']) && Storage::exists($news['image'])) {
                Storage::dick('public')->delete($news['image']);
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
            'image' => 'required|mimes:jpg,jpeg,png,webp|max:2048',
            'content' => 'required'
        ]);

        $new = News::find($id);

        if (!$new) {
            return response()->json([
                'message' => 'Không tồn tại tin tức có id=' . $id
            ], 404);

        }

        try {
            if ($request->hasFile('image')) {
                $new['image'] = Storage::disk('public')->put('news', $request->file('image'));
            }

            $currentimage = $new->image;

            $new->update($data);;

            if (
                $request->hasFile('image')
                && !empty($currentimage)
                && Storage::exists($currentimage)
            ) {
                Storage::disk('public')->delete($currentimage);

            }

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật tin tức thành công',
                'news' => $new
            ], 201);

        } catch (\Throwable $th) {
            //throw $th;
            if (!empty($data['image']) && Storage::exists($data['image'])) {

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
