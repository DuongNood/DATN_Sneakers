<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class NewsController extends Controller
{
    const PATH_VIEW = "admin.news.";

    public function index()
    {
        $data = News::latest('id')->paginate(10);
        return view(self::PATH_VIEW . __FUNCTION__, compact('data'));
    }

    public function create()
    {
        return view(self::PATH_VIEW . __FUNCTION__);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
            'image' => 'required|mimes:jpg,jpeg,png,webp|max:2048',
            'content' => 'required'
        ]);

        try {
            if ($request->hasFile('image')) {
                $uploadedFile = Cloudinary::upload($request->file('image')->getRealPath());
                $data['image'] = $uploadedFile->getSecurePath(); // Lấy URL của ảnh trên Cloudinary
            }

            News::create($data);

            return redirect()->route(self::PATH_VIEW . 'index')->with('success', 'Tin tức đã được tạo thành công!');

        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function show(News $news)
    {
        //
    }

    public function edit(News $news)
    {
        return view(self::PATH_VIEW . __FUNCTION__, compact('news'));
    }

    public function update(Request $request, News $news)
    {
        $data = $request->validate([
            'title' => 'required',
            'image' => 'nullable|mimes:jpg,jpeg,png,webp|max:2048',
            'content' => 'required'
        ]);

        try {
            if ($request->hasFile('image')) {
                // Xóa ảnh cũ trên Cloudinary nếu có
                if ($news->image) {
                    Cloudinary::destroy($news->image);
                }

                // Upload ảnh mới lên Cloudinary
                $uploadedFile = Cloudinary::upload($request->file('image')->getRealPath());
                $data['image'] = $uploadedFile->getSecurePath(); // Lấy URL ảnh mới
            } else {
                $data['image'] = $news->image;
            }

            $news->update($data);

            return redirect()->route(self::PATH_VIEW . 'index')->with('success', 'Tin tức đã được cập nhật thành công!');

        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy(News $news)
    {
        try {
            // Xóa ảnh trên Cloudinary nếu có
            if ($news->image) {
                Cloudinary::destroy($news->image);
            }

            $news->delete();

            return back()->with('success', 'Tin tức đã được xóa!');

        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
}
