<?php

namespace App\Http\Controllers\admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $title = "Category";
        $category = Category::get();    
        return view('admin.category.index', compact('category','title'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $title = "Category";
        return view('admin.category.create',compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
       
        $params = $request->validate([
            'category_name'=> 'required|max:255|unique:categories',
        ]);
        if ($request->has('status')) {
            $params['status'] = 1;
        } 
        
        if($request->hasFile('image')){
                $params['image'] =$request->file('image')->store('uploads/category', 'public');
        }else{
                $params['image'] =null;
        }
        Category::create($params);
        return redirect()->route('categories.index')->with('success', 'Add new Success List!');

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
        $title = "Category";
        $category = Category::find($id);
        return view('admin.category.edit',compact('title','category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        
        $category = Category::find($id);

        if (!$category) {
            return redirect()->route('categories.index')->with('error', 'Category Does Not Exist!');
        }

        
        $param = $request->validate([
            'category_name' => 'required|max:255|unique:categories,category_name,'. $category->id,           
            'status' => 'required|in:0,1'
        ]);
        if($request->hasFile('image')){
                if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            $param['image'] = $request->file('image')->store('uploads/category', 'public');
        }else{
               $param['image'] = $category->image;
        }
        $param['status'] = $request->status ? 1 : 0;

        
        $category->update($param);

        return redirect()->route('categories.index')->with('success', 'Update List Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $category = Category::find($id);
        if (!$category) {
            return redirect()->route('categories.index')->with('error', 'Danh Mục Không Tồn Tại!');
        }
        // if (Category::find($id)->products->count() > 0) {
        //     return redirect()->route('categories.index')
        //         ->with('error', 'Category được sử dụng trong các sản phẩm. Bạn không thể xóa nó.');
        // }
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Xóa Danh Mục Thành Công!');
    }
}
