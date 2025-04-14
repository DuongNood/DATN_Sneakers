<?php

namespace App\Http\Controllers\admin;

use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class ProductVariantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $title = "Biến thể sản phẩm";
        $productVariant = ProductVariant::latest('id') ->paginate(10);

    return view('admin.product_variants.index', compact('title', 'productVariant'));;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(String $id)
    {
        //
        $product = Product::find($id);
        $size= ProductSize::get();
        $title ="product variant";
        return view('admin.product_variants.create',compact('title','product','size'));
    }

    /**
     * Store a newly created resource in storage.
     */


    public function store(Request $request)
{
    
    DB::beginTransaction();
    try {
        $validatedData = $request->validate([
            'product_variants'                   => 'required|array',
            'product_variants.*.product_size_id' => 'required',
            'product_variants.*.product_id'      => 'required|exists:products,id',
            'product_variants.*.quantity'        => 'required|integer|min:0',
            'product_variants.*.status'          => 'nullable|in:0,1',
        ], [
            'product_variants.required' => 'Danh sách biến thể không được để trống!',
            'product_variants.array' => 'Dữ liệu biến thể không hợp lệ!',
            'product_variants.*.product_size_id.required' => 'Mỗi biến thể phải có một product_size_id!',
            'product_variants.*.product_id.required' => 'Mỗi biến thể phải có một product_id!',
            'product_variants.*.product_id.exists' => 'product_id không hợp lệ!',
            'product_variants.*.quantity.required' => 'Số lượng là bắt buộc!',
            'product_variants.*.quantity.integer' => 'Số lượng phải là số nguyên!',
            'product_variants.*.status.in' => 'Trạng thái chỉ được là 0 hoặc 1!',
        ]);

        foreach ($validatedData['product_variants'] as $variant) {
            ProductVariant::create([
                'product_size_id' => $variant['product_size_id'],
                'product_id'      => $variant['product_id'],
                'quantity'        => $variant['quantity'],
                'status'          => $variant['status'] ?? 1,
            ]);
        }

        DB::commit();
        return redirect()->route('admin.product_variants.index')->with('success', 'Thêm biến thể thành công!');
    } catch (ValidationException $e) {
        DB::rollBack(); // 👈 rollback nếu validate fail
        return back()->withErrors($e->errors())->withInput();
    } catch (QueryException $e) {
        DB::rollBack(); // 👈 rollback nếu lỗi SQL

        if ($e->errorInfo[1] == 1062) {
            $errors = ['product_variants.*.product_size_id' => 'SKU đã tồn tại cho sản phẩm này!'];
            return back()->withErrors($errors)->withInput();
        }

        return redirect()->route('admin.product_variants.index')->withErrors(['error' => 'Có lỗi xảy ra, vui lòng thử lại!'])->withInput();
    } catch (\Exception $e) {
        DB::rollBack(); // 👈 rollback nếu lỗi khác
        return redirect()->route('admin.product_variants.index')->withErrors(['error' => 'Lỗi hệ thống: ' . $e->getMessage()])->withInput();
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
        $productVariant = ProductVariant::find($id);
        $product = Product::get();
        $title ="product variant";
        if (!$productVariant) {
            return redirect()->route('admin.product_variants.index')->with('error','Sản phẩm không tồn tại!');
        }
        return view('admin.product_variants.edit', compact('productVariant','product','title'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $productVariant = ProductVariant::find($id);

        if (!$productVariant) {
            return redirect()->route('admin.product_variants.index')->with('error', 'Danh Mục Không Tồn Tại!');
        }

        // Validate dữ liệu gửi lên từ form
        $param = $request->validate([        
            'product_id' => 'required|exists:products,id',  
            'product_size_id' => 'required',        
            'quantity' => 'required|integer|min:0',
            'status' => 'required|in:0,1'
        ]);

        // Thực hiện cập nhật
        $productVariant->update($param);

        return redirect()->route('admin.product_variants.index')->with('success', 'Cập Nhật Danh Mục Thành Công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function variantDiscontinued()
    {
        //
        $title = "Biến thể sản phẩm";
        $listVariant = ProductVariant::where('status', false)->get();
        return view('admin.product_variants.variantDiscontinued',compact('title','listVariant'));
    }
}
