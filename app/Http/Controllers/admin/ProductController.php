<?php

namespace App\Http\Controllers\admin;

use App\Models\Brand;
use App\Models\Banner;
use App\Models\Gender;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductSize;
use App\Models\ImageProduct;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
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
        $listGender = Gender::where('status', true)->get();
        $listBrand = Brand::where('status', 'active')->get();
        $size= ProductSize::get();
        return view('admin.products.create', compact('title', 'listCategories','listGender','listBrand','size'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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
            $validatedData = $request->validate([
            'product_variants'                  => 'required|array', 
            'product_variants.*.product_size_id'=> 'required',
             
            'product_variants.*.quantity'       => 'required|integer|min:0', 
            'product_variants.*.status'         => 'nullable|in:0,1'
        ],[
            //THÊM THÔNG BÁO LỖI TÙY CHỈNH
            'product_variants.required' => 'Danh sách biến thể không được để trống!',
            'product_variants.array' => 'Dữ liệu biến thể không hợp lệ!',
            'product_variants.*.product_size_id.required' => 'Mỗi biến thể phải có một product_size_id!',                   
            'product_variants.*.quantity.required' => 'Số lượng là bắt buộc!',
            'product_variants.*.quantity.integer' => 'Số lượng phải là số nguyên!',
            'product_variants.*.status.in' => 'Trạng thái chỉ được là 0 hoặc 1!',
        ]);

        // Lưu vào database
        foreach ($validatedData['product_variants'] as $variant) {
            ProductVariant::create([
                'product_size_id'=> $variant['product_size_id'],
                'product_id'=> $productID,               
                'quantity'=> $variant['quantity'],
                'status' => $variant['status'] ?? 1,
            ]);
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
        $listGender = Gender::where('status', true)->get();
        $listBrand = Brand::where('status', 'active')->get();
        $size= ProductSize::get();
        $listVariant = ProductVariant::where('product_id', $id)->get();
        return view('admin.products.edit', compact('title', 'product', 'category','listGender','size','listBrand','listVariant'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, string $id)
    {
        
        try {
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

            // ✅ Xử lý ảnh đại diện
            if ($request->hasFile('image')) {
                $uploadedFileUrl = Cloudinary::upload($request->file('image')->getRealPath())->getSecurePath();

                if ($uploadedFileUrl) {
                    if ($product->image) {
                        try {
                            $publicId = $this->getCloudinaryPublicId($product->image);
                            Cloudinary::destroy($publicId);
                        } catch (\Exception $e) {
                            Log::error('Lỗi khi xóa ảnh đại diện trên Cloudinary: ' . $e->getMessage());
                        }
                    }
                    $params['image'] = $uploadedFileUrl;
                }
            }

            // ✅ Xử lý ảnh album
            $currentImages = $product->imageProduct()->pluck('id')->toArray();
            $listImages = is_array($request->list_image) ? $request->list_image : [];

            foreach ($currentImages as $imageId) {
                if (!array_key_exists($imageId, $listImages)) {
                    $img = ImageProduct::find($imageId);
                    if ($img) {
                        try {
                            $publicId = $this->getCloudinaryPublicId($img->image_product);
                            Cloudinary::destroy($publicId);
                            $img->delete();
                        } catch (\Exception $e) {
                            Log::error('Lỗi khi xóa ảnh cũ: ' . $e->getMessage());
                        }
                    }
                }
            }

            foreach ($listImages as $key => $image) {
                if ($request->hasFile("list_image.$key")) {
                    $uploadedFileUrl = Cloudinary::upload($request->file("list_image.$key")->getRealPath())->getSecurePath();

                    if ($uploadedFileUrl) {
                        $img = ImageProduct::where('product_id', $id)->where('id', $key)->first();

                        if ($img) {
                            try {
                                $publicId = $this->getCloudinaryPublicId($img->image_product);
                                Cloudinary::destroy($publicId);
                                $img->update(['image_product' => $uploadedFileUrl]);
                            } catch (\Exception $e) {
                                Log::error('Lỗi khi cập nhật ảnh: ' . $e->getMessage());
                            }
                        } else {
                            $product->imageProduct()->create([
                                'product_id' => $id,
                                'image_product' => $uploadedFileUrl
                            ]);
                        }
                    }
                }
            }

            // ✅ Cập nhật sản phẩm chính
            $product->update($params);

            // ✅ Validate biến thể
            $validatedData = $request->validate([
                'product_variants'                  => 'required|array',
                'product_variants.*.product_size_id'=> 'required|exists:product_sizes,id',
                'product_variants.*.quantity'       => 'required|integer|min:0',
                'product_variants.*.status'         => 'nullable|in:0,1'
            ],[
                'product_variants.required' => 'Danh sách biến thể không được để trống!',
                'product_variants.*.product_size_id.required' => 'Mỗi biến thể phải có product_size_id!',
                'product_variants.*.product_size_id.exists' => 'Product size không hợp lệ!',
                'product_variants.*.quantity.required' => 'Số lượng là bắt buộc!',
                'product_variants.*.quantity.integer' => 'Số lượng phải là số nguyên!',
                'product_variants.*.status.in' => 'Trạng thái chỉ được là 0 hoặc 1!',
            ]);

            // ✅ Xóa những biến thể không còn
            $currentVariantIds = $product->variants()->pluck('product_size_id')->toArray();
            $incomingVariantIds = collect($validatedData['product_variants'])->pluck('product_size_id')->toArray();
            $variantIdsToDelete = array_diff($currentVariantIds, $incomingVariantIds);

            if (!empty($variantIdsToDelete)) {
                ProductVariant::where('product_id', $product->id)
                    ->whereIn('product_size_id', $variantIdsToDelete)
                    ->delete();
            }

            // ✅ Cập nhật hoặc thêm mới biến thể
            foreach ($validatedData['product_variants'] as $variant) {
                ProductVariant::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'product_size_id' => $variant['product_size_id']
                    ],
                    [
                        'quantity' => $variant['quantity'],
                        'status' => $variant['status'] ?? 1
                    ]
                );
            }

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi cập nhật sản phẩm: ' . $e->getMessage());
            return back()->with('error', 'Cập nhật thất bại! Lỗi: ' . $e->getMessage());
        }
    }

/**
 * Trích xuất public_id từ URL Cloudinary
 */
private function getCloudinaryPublicId($url)
{
    $parts = explode('/', parse_url($url, PHP_URL_PATH));
    $filename = end($parts);
    return pathinfo($filename, PATHINFO_FILENAME);
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
