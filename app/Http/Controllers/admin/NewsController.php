<?php

namespace App\Http\Controllers\admin;

use App\Models\News;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class NewsController extends Controller
{
    const PATH_VIEW = "admin.news.";

    public function index(){

        $data = News::query()->latest()->paginate(10);

        return view(self::PATH_VIEW . __FUNCTION__ , compact('data'));
    }

    public function create(){

        return view(self::PATH_VIEW . __FUNCTION__);

    }

    public function store(Request $request){
        $data = $request->validate([
        'title' => 'required|string|max:255',
        'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        'content' => 'required|string'
    ]);

    try {
        if ($request->hasFile('image')) {
            // ✅ Upload ảnh lên Cloudinary
            $uploadedFileUrl = Cloudinary::upload($request->file('image')->getRealPath())->getSecurePath();
            $data['image'] = $uploadedFileUrl;
        }

        // ✅ Tạo bản ghi trong bảng `news`
        News::create($data);

        return redirect()->route(self::PATH_VIEW . 'index')->with('success', 'Create news successfully');
    } catch (\Throwable $th) {
        return back()->with('error', $th->getMessage());
    }
    }

    public function show(News $news){
        return view(self::PATH_VIEW . __FUNCTION__, compact('news'));
    }

    public function edit(News $news){
        return view(self::PATH_VIEW . __FUNCTION__, compact('news'));
    }

    public function update(Request $request, News $news) {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|mimes:jpg,jpeg,png,webp|max:2048',
            'content' => 'required|string'
        ]);

        try {
            $currentImage = $news->image;

            // ✅ Nếu có file mới được upload
            if ($request->hasFile('image')) {
                // ⭐ Xóa ảnh cũ trên Cloudinary nếu tồn tại
                if ($currentImage) {
                    Cloudinary::destroy($currentImage);
                }

                // ⭐ Upload ảnh mới lên Cloudinary
                $uploadedFileUrl = Cloudinary::upload($request->file('image')->getRealPath())->getSecurePath();
                $data['image'] = $uploadedFileUrl;
            }

            // ✅ Cập nhật thông tin bản ghi
            $news->update($data);

            return back()->with('success', 'Update news successfully!');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }


    public function destroy(News $news)
    {
        try {

            $news->delete();

            return redirect()->route(self::PATH_VIEW . 'index')->with('success', 'Đã xóa thành công tin tức!');

        } catch (\Throwable $th) {
            return back()
                ->with('success', true)
                ->with('error', $th->getMessage());

        }
    }

}
