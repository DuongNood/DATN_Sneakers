<?php

namespace App\Http\Controllers\admin;

use App\Models\Product;
use App\Models\Category;
use App\Models\ImageProduct;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

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
        $params = $request->except('_token');
        
        // Xử lý status
         if ($request->has('status')) {
            $params['status'] = 1;
            } 
        

        // 👉 Upload ảnh chính lên Cloudinary
        if ($request->hasFile('image')) {
            $uploadedFileUrl = Cloudinary::upload($request->file('image')->getRealPath())->getSecurePath();
            if ($uploadedFileUrl) {
                $params['image'] = $uploadedFileUrl;
            } else {
                $params['image'] = null;
            }
        }
        
        // Tạo sản phẩm mới
        $product = Product::create($params);

        // Lấy ID của sản phẩm vừa tạo
        $productID = $product->id;

        // 👉 Upload danh sách ảnh lên Cloudinary
        if ($request->hasFile('list_image')) {
            foreach ($request->file('list_image') as $image) {
                if ($image) {
                    $uploadedFileUrl = Cloudinary::upload($image->getRealPath())->getSecurePath();
                    $product->imageProduct()->create([
                        'product_id' => $productID,
                        'image_product' => $uploadedFileUrl,
                    ]);
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

    // Xử lý ảnh đại diện (upload lên Cloudinary)
    if ($request->hasFile('image')) {
        // Xóa ảnh cũ trên Cloudinary nếu có
        if ($product->image) {
            Cloudinary::destroy($product->image);
        }

        // Upload ảnh mới lên Cloudinary
        $uploadedFileUrl = Cloudinary::upload($request->file('image')->getRealPath())->getSecurePath();
        $params['image'] = $uploadedFileUrl;
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
            if ($hinhAnhSp) {
                // Xóa ảnh trên Cloudinary nếu tồn tại
                Cloudinary::destroy($hinhAnhSp->image_product);
                $hinhAnhSp->delete();
            }
        }
    }

    // Thêm hoặc cập nhật ảnh mới vào album
    foreach ($listImages as $key => $image) {
        if (!array_key_exists($key, $arrayCombine)) { 
            // Nếu là ảnh mới
            if ($request->hasFile("list_image.$key")) {
                $uploadedFileUrl = Cloudinary::upload($request->file("list_image.$key")->getRealPath())->getSecurePath();
                $product->imageProduct()->create([
                    'product_id' => $id,
                    'image_product' => $uploadedFileUrl
                ]);
            }
        } elseif (is_file($image) && $request->hasFile("list_image.$key")) {
            // Nếu là ảnh đã tồn tại và cần cập nhật
            $hinhAnhSp = ImageProduct::find($key);
            if ($hinhAnhSp) {
                // Xóa ảnh cũ trên Cloudinary
                Cloudinary::destroy($hinhAnhSp->image_product);

                // Upload ảnh mới lên Cloudinary
                $uploadedFileUrl = Cloudinary::upload($request->file("list_image.$key")->getRealPath())->getSecurePath();
                $hinhAnhSp->update([
                    'image_product' => $uploadedFileUrl
                ]);
            }
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
