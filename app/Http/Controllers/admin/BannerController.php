<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    const PATH_VIEW = 'admin.banners.';

    public function index()
    {
        $data = Banner::latest('id')->paginate(5);

        return view(self::PATH_VIEW . __FUNCTION__, compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view(self::PATH_VIEW . __FUNCTION__);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|max:255',
            'image' => 'required|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => ['nullable', Rule::in([0, 1])],
        ]);

        try {
            if ($request->hasFile('image')) {
                $uploadedFile = $request->file('image');
                $uploadResult = Cloudinary::upload($uploadedFile->getRealPath());
                $data['image'] = $uploadResult->getSecurePath();
            }

            $data['status'] ??= 1;

            Banner::create($data);

            return redirect()->route('admin.banners.index')->with('success', 'Thêm mới banner thành công!');
        } catch (\Throwable $th) {
            return back()->with('success', false)->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Banner $banner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Banner $banner)
    {
        return view(self::PATH_VIEW . __FUNCTION__, compact('banner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Banner $banner)
    {
        $data = $request->validate([
            'title' => 'required|max:255',
            'image' => 'required|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => ['nullable', Rule::in([0, 1])],
        ]);

        try {
            $data['status'] ??= 0;

            if ($request->hasFile('image')) {
                $uploadedFile = $request->file('image');
                $uploadResult = Cloudinary::upload($uploadedFile->getRealPath());
                $data['image'] = $uploadResult->getSecurePath(); // Lưu URL ảnh mới

                // Xóa ảnh cũ trên Cloudinary (nếu có)
                if ($banner->image) {
                    Cloudinary::destroy($banner->image);
                }
            }

            $banner->update($data);

            return back()->with('success', 'Cập nhật banner thành công!');
        } catch (\Throwable $th) {
            return back()->with('success', false)->with('error', $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banner $banner)
    {
        try {
            // Xóa ảnh trên Cloudinary
            if ($banner->image) {
                Cloudinary::destroy($banner->image);
            }

            $banner->delete();

            return back()->with('success', 'Xóa thành công!');
        } catch (\Throwable $th) {
            return back()->with('success', false)->with('error', $th->getMessage());
        }
    }
}
