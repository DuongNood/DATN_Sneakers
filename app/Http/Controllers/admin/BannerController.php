<?php

namespace App\Http\Controllers\admin;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    const PATH_VIEW = 'admin.banners.';

    public function index(Request $request)
    {
        $query = Banner::query();

        // 🔍 Xử lý tìm kiếm
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'LIKE', '%' . $request->search . '%');
            });
        }

        // Xử lý lọc trạng thái (status: 0 = Inactive, 1 = Active)
        if ($request->has('status') && in_array($request->status, ['0', '1'])) {
            $query->where('status', $request->status);
        }

        // Lấy danh sách banner và phân trang
        $data = $query->latest('id')->paginate(10);

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
        'title' => 'required|string|max:255',
        'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        'status' => ['nullable', Rule::in([0, 1])],
    ]);

    try {
        DB::beginTransaction(); // Sử dụng transaction để đảm bảo dữ liệu nhất quán

        if ($request->hasFile('image')) {
            $uploadedFile = $request->file('image');
            $uploadResult = Cloudinary::upload($uploadedFile->getRealPath(), [
                'folder' => 'banners', // Đưa vào thư mục banners trên Cloudinary
                'quality' => 'auto', // Tự động giảm chất lượng để tối ưu tốc độ
                'fetch_format' => 'auto', // Chọn định dạng tối ưu (webp, jpg, png, ...)
                'crop' => 'scale' // Cắt ảnh theo tỷ lệ
            ]);

            $data['image'] = $uploadResult->getSecurePath(); // Lấy đường dẫn ảnh từ Cloudinary
        }

        // Xử lý trạng thái mặc định nếu không có
        $data['status'] = $data['status'] ?? 1;

        // Tạo mới banner
        Banner::create($data);

        DB::commit();

        return redirect()->route('admin.banners.index')->with('success', 'Thêm mới banner thành công!');
    } catch (\Exception $e) {
        DB::rollBack(); // Rollback nếu có lỗi
        Log::error('Lỗi khi thêm banner: ' . $e->getMessage()); // Ghi log để debug
        return back()->with('error', 'Thêm banner thất bại! Lỗi: ' . $e->getMessage());
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
