<?php

namespace App\Http\Controllers\admin;

use App\Models\Product;
use App\Models\Category;
use App\Models\ImageProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

            return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công!');
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
    
    try {
        // Tìm sản phẩm theo ID
        $product = Product::findOrFail($id);
    } catch (ModelNotFoundException $e) {
        return back()->with('error', 'Sản phẩm không tồn tại!');
    }

    $params = $request->except('_token', '_method');

    if (!isset($params['is_show_home'])) {
        $params['is_show_home'] = $product->is_show_home;
    }

    try {
        DB::beginTransaction();

        //Xử lý ảnh đại diện (upload lên Cloudinary)
        if ($request->hasFile('image')) {
            $uploadedFileUrl = Cloudinary::upload($request->file('image')->getRealPath())->getSecurePath();
            if ($uploadedFileUrl) {
                // Xóa ảnh cũ trên Cloudinary nếu tồn tại
                if ($product->image) {
                    try {
                        Cloudinary::destroy($product->image);
                    } catch (\Exception $e) {
                        Log::error('Lỗi khi xóa ảnh trên Cloudinary: ' . $e->getMessage());
                    }
                }
                $params['image'] = $uploadedFileUrl;
            }
        }

        // Xử lý ảnh album (list_image)
        $currentImages = $product->imageProduct()->pluck('id')->toArray();

        // Kiểm tra nếu `list_image` không phải mảng thì gán thành mảng rỗng
        $listImages = is_array($request->list_image) ? $request->list_image : [];

        // ✅ Xóa ảnh không còn trong danh sách `list_image`
        foreach ($currentImages as $imageId) {
            if (!in_array($imageId, array_keys($listImages))) {
                $hinhAnhSp = ImageProduct::where('product_id', $id)->where('id', $imageId)->first();
                if ($hinhAnhSp) {
                    try {
                        Cloudinary::destroy($hinhAnhSp->image_product);
                        $hinhAnhSp->delete();
                    } catch (\Exception $e) {
                        Log::error('Lỗi khi xóa ảnh cũ: ' . $e->getMessage());
                    }
                }
            }
        }

        // ✅ Thêm hoặc cập nhật ảnh mới vào album
        foreach ($listImages as $key => $image) {
            if ($request->hasFile("list_image.$key")) {
                $uploadedFileUrl = Cloudinary::upload($request->file("list_image.$key")->getRealPath())->getSecurePath();

                if ($uploadedFileUrl) {
                    $hinhAnhSp = ImageProduct::where('product_id', $id)->where('id', $key)->first();
                    if ($hinhAnhSp) {
                        // Cập nhật ảnh cũ
                        try {
                            Cloudinary::destroy($hinhAnhSp->image_product);
                            $hinhAnhSp->update(['image_product' => $uploadedFileUrl]);
                        } catch (\Exception $e) {
                            Log::error('Lỗi khi cập nhật ảnh: ' . $e->getMessage());
                        }
                    } else {
                        // Thêm ảnh mới
                        $product->imageProduct()->create([
                            'product_id' => $id,
                            'image_product' => $uploadedFileUrl
                        ]);
                    }
                }
            }
        }

        // ✅ Cập nhật thông tin sản phẩm
        $product->update($params);

        DB::commit();

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công!');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Lỗi khi cập nhật sản phẩm: ' . $e->getMessage());
        return back()->with('error', 'Cập nhật sản phẩm thất bại! Lỗi: ' . $e->getMessage());
    }
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
