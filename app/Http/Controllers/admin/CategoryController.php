<?php

namespace App\Http\Controllers\admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    const PATH_VIEW = 'admin.categories.';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $title = "Danh sách danh mục";
        $category = Category::latest('id')->paginate(10);    
        return view(self::PATH_VIEW . __FUNCTION__, compact('category','title'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //

        $title = "Thêm danh mục";
        return view(self::PATH_VIEW . __FUNCTION__,compact('title'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $params = $request->validate([
            'category_name' => 'required|max:255|unique:categories',
        ]);

        $params['status'] = isset($request->status) ? 1 : 0;

        Category::create($params);

        return redirect()->route('admin.categories.index')->with('success', 'Thêm danh mục thành công!');
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

        $title = "Chỉnh sửa danh mục";
        $category = Category::findOrFail($id);
        return view(self::PATH_VIEW . 'edit',compact('title','category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        $params = $request->validate([
            'category_name' => 'required|max:255|unique:categories,category_name,' . $category->id,
            'status' => 'required|in:0,1'
        ]);

        $category->update($params);

        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);

        // Kiểm tra xem danh mục có sản phẩm không (nếu có liên kết với products)
        if ($category->product()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Danh mục đang được sử dụng, không thể xóa!');
        }

        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Xóa danh mục thành công!');
    }
}
