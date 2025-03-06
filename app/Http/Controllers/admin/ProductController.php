<?php

namespace App\Http\Controllers\admin;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\ImageProduct;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $title = "Product";
        $listProduct = Product::where('status', true)->get();
        return view('admin.product.index',compact('title','listProduct'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $title = "Product";
        $listCategories = Category::where('status',true)->get();
        return view('admin.product.create',compact('title','listCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        //
        
            $params = $request->except('_token');
           
            if ($request->has('status')) {
            $params['status'] = 1;
            } 

            if($request->hasFile('image')){
                $params['image'] =$request->file('image')->store('uploads/product', 'public');
            }else{
                $params['image'] =null;
            }
            
            $product = Product::create($params);
            //lay id sp vua create
            $productID = $product->id;
            if($request->has('list_image')){
                foreach($request->file('list_image') as $image){
                    if($image){
                        $path = $image->store('uploads/ablum_product/id_'.$productID, 'public');
                        $product->imageProduct()->create(
                            ['product_id'=>$productID,
                            'image_product'=>$path,]);
                    }
                }
            }
            return redirect()->route('products.index')->with('success', 'Successfully added new product');
        
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
        $title ="Cap nhat San pham";
        $product = Product::find($id);
        $category = Category::where('status', true)->get();
        return view('admin.product.edit', compact('title', 'product','category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, string $id)
    {
       
        // Lấy tất cả dữ liệu trừ `_token` và `_method`
        $params = $request->except('_token', '_method');

        // Tìm sản phẩm theo ID
        $product = Product::findOrFail($id);

        // Xử lý ảnh đại diện
        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $params['image'] = $request->file('image')->store('uploads/product', 'public');
        } else {
            $params['image'] = $product->image;
        }

        // Xử lý ảnh album (list_image)
        $currentImages = $product->imageProduct()->pluck('id')->toArray();
        $arrayCombine = array_combine($currentImages, $currentImages);

        // Kiểm tra nếu `list_image` không tồn tại trong request, gán thành mảng rỗng để tránh lỗi
        $listImages = $request->list_image ?? [];

        // Xóa ảnh không còn trong danh sách `list_image`
        foreach ($arrayCombine as $key => $value) {
            if (!array_key_exists($key, $listImages)) {
                $hinhAnhSp = ImageProduct::find($key);
                if ($hinhAnhSp && Storage::disk('public')->exists($hinhAnhSp->image_product)) {
                    Storage::disk('public')->delete($hinhAnhSp->image_product);
                }
                if ($hinhAnhSp) {
                    $hinhAnhSp->delete();
                }
            }
        }

        // Thêm hoặc cập nhật ảnh mới vào album
        foreach ($listImages as $key => $image) {
            if (!array_key_exists($key, $arrayCombine)) { 
                // Nếu là ảnh mới
                if ($request->hasFile("list_image.$key")) {
                    $path = $image->store('uploads/ablum_product/id_'.$id, 'public');
                    $product->imageProduct()->create([
                        'san_pham_id' => $id,
                        'image_product' => $path
                    ]);
                }
            } elseif (is_file($image) && $request->hasFile("list_image.$key")) {
                // Nếu là ảnh đã tồn tại và cần cập nhật
                $hinhAnhSp = ImageProduct::find($key);
                if ($hinhAnhSp && Storage::disk('public')->exists($hinhAnhSp->image_product)) {
                    Storage::disk('public')->delete($hinhAnhSp->image_product);
                }
                $path = $image->store('uploads/ablum_product/id_'.$id, 'public');
                $hinhAnhSp->update([
                    'image_product' => $path
                ]);
            }
        }

        // Cập nhật sản phẩm
        $product->update($params);
        return redirect()->route('products.index')->with('success', 'Successfully updated product');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function productDiscontinued()
    {
        //
        $title = "Product";
        $listProduct = Product::where('status', false)->get();
        return view('admin.product.productDiscontinued',compact('title','listProduct'));
    }
}
