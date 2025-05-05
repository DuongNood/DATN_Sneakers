<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    const PATH_VIEW = 'admin.brands.';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $title = "Danh sách thương hiệu";
        $brand = Brand::latest('id')->paginate(10);    
        return view(self::PATH_VIEW . __FUNCTION__, compact('brand','title'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //

        $title = "Thêm thương hiệu";
        return view(self::PATH_VIEW . __FUNCTION__,compact('title'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $params = $request->validate([
            'brand_name' => 'required|max:255|unique:brands',
            'description' => 'nullable',
        ]);

        $params['status'] = isset($request->status) ? 1 : 0;

        Brand::create($params);

        return redirect()->route('admin.brands.index')->with('success', 'Thêm thương hiệu thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //

        $title = "Chỉnh sửa thương hiệu";
        $brand = Brand::findOrFail($id);
        return view(self::PATH_VIEW . 'edit',compact('title','brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $brand = Brand::findOrFail($id);

        $params = $request->validate([
            'brand_name' => 'required|max:255|unique:brands,brand_name,' . $brand->id,
            'status' => 'required'
        ]);

        $brand->update($params);
         $brand->productBrand()->update(['status' => $params['status']]);

        return redirect()->route('admin.brands.index')->with('success', 'Cập nhật thương hiệu thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $brand = Brand::findOrFail($id);

        // Kiểm tra xem brand có sản phẩm không (nếu có liên kết với products)
        if ($brand->product()->count() > 0) {
            return redirect()->route('admin.brands.index')
                ->with('error', 'Thương hiệu đang được sử dụng, không thể xóa!');
        }

        $brand->delete();
        return redirect()->route('admin.brands.index')->with('success', 'Xóa thương hiệu thành công!');
    }
}
