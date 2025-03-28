<?php

namespace App\Http\Controllers\admin;

use App\Models\Product;
use App\Models\Category;
use App\Models\ImageProduct;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = "Sản phẩm";
        $query = Product::query();

        // Xử lý tìm kiếm
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('product_name', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('product_code', 'LIKE', '%' . $request->search . '%');

                // Nếu search là số, tìm theo giá
                if (is_numeric($request->search)) {
                    $q->orWhere('original_price', $request->search)
                        ->orWhere('discounted_price', $request->search);
                }
            });
        }

        // Xử lý lọc trạng thái (status: 0 = Inactive, 1 = Active)
        if ($request->has('status') && in_array($request->status, ['0', '1'])) {
            $query->where('status', $request->status);
        }

        // Lọc theo danh mục
        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }

        $categories = Category::all();
        $listProduct = $query->latest('id')->paginate(10);

        return view('admin.products.index', compact('title', 'listProduct', 'categories'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $title = "Product";
        $listCategories = Category::where('status', true)->get();
        return view('admin.products.create', compact('title', 'listCategories'));
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

        try {
            // 👉 Upload ảnh chính lên Cloudinary
            if ($request->hasFile('image')) {
                $uploadedFileUrl = Cloudinary::upload($request->file('image')->getRealPath())->getSecurePath();
                $params['image'] = $uploadedFileUrl ?: null;
            }

            // 👉 Tạo sản phẩm mới
            $product = Product::create($params);

            // 👉 Lấy ID của sản phẩm vừa tạo
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

            return redirect()->route('products.index')->with('success', 'Thêm sản phẩm thành công!');
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                // 👉 Lỗi trùng lặp khóa duy nhất
                $validator = Validator::make($request->all(), []);
                $validator->errors()->add('product_name', 'Mã sản phẩm đã tồn tại, vui lòng chọn mã khác.');

                // 👉 Quay lại với thông báo lỗi
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // 👉 Trả về lỗi khác nếu có
            return redirect()->back()->with('error', 'Đã xảy ra lỗi! Vui lòng thử lại.');
        }
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
        $title = "Cap nhat San pham";
        $product = Product::find($id);
        $category = Category::where('status', true)->get();
        return view('admin.products.edit', compact('title', 'product', 'category'));
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
        if (!isset($params['is_show_home'])) {
            $params['is_show_home'] = $product->is_show_home;
        }

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

        return redirect()->route('admin.products.index')->with('success', 'Successfully updated product');
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
        $title = "Sản phẩm";
        $listProduct = Product::where('status', false)->get();
        return view('admin.products.productDiscontinued', compact('title', 'listProduct'));
    }
}
