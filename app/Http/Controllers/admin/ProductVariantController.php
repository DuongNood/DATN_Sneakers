<?php

namespace App\Http\Controllers\admin;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class ProductVariantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $title ="product variant";
        $productVariant= ProductVariant::where('status',true)->get();
        return view('admin.product_variants.index',compact('title','productVariant'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $product = Product::where('status',true)->get();
        
        $title ="product variant";
        return view('admin.product_variants.create',compact('title','product'));
    }

    /**
     * Store a newly created resource in storage.
     */


    public function store(Request $request)
    {
        // Validate toàn bộ dữ liệu
        $validatedData = $request->validate([
            'product_variants'                  => 'required|array', // Phải là một mảng
            'product_variants.*.sku'            => [
                'required', 
                'string', 
                'max:50', 
                Rule::unique('product_variants')->where(function ($query) use ($request) {
                    return $query->where('product_id', $request->input('product_id'));
                })
            ],
            'product_variants.*.product_id'     => 'required|exists:products,id', // Phải tồn tại trong bảng products
            'product_variants.*.price'          => 'required|numeric|min:0', // Giá không âm
            'product_variants.*.promotional_price' => 'nullable|numeric|min:0|lte:product_variants.*.price', // Giá khuyến mãi phải nhỏ hơn hoặc bằng giá gốc
            'product_variants.*.quantity'       => 'required|integer|min:0', // Số lượng phải là số nguyên không âm
            'product_variants.*.status'         => 'nullable|in:0,1' // Trạng thái chỉ nhận 0 hoặc 1
        ], [
            // Thông báo lỗi tùy chỉnh
            'product_variants.required' => 'The variant list cannot be empty!',
            'product_variants.array' => 'Invalid variant data!',
            'product_variants.*.sku.required' => 'Each variant must have a SKU!',
            'product_variants.*.sku.unique' => 'The SKU already exists for this product!',
            'product_variants.*.product_id.required' => 'Each variant must have a product_id!',
            'product_variants.*.product_id.exists' => 'Invalid product_id!',
            'product_variants.*.price.required' => 'Product price is required!',
            'product_variants.*.price.min' => 'Price cannot be negative!',
            'product_variants.*.promotional_price.lte' => 'Promotional price must be less than or equal to the original price!',
            'product_variants.*.quantity.required' => 'Quantity is required!',
            'product_variants.*.quantity.integer' => 'Quantity must be an integer!',
            'product_variants.*.status.in' => 'Status must be either 0 or 1!'
        ]);

        // Lưu vào database
        foreach ($validatedData['product_variants'] as $variant) {
            ProductVariant::create([
                'sku'                => $variant['sku'],
                'product_id'         => $variant['product_id'],
                'price'              => $variant['price'],
                'promotional_price'  => $variant['promotional_price'] ?? null,
                'quantity'           => $variant['quantity'],
                'status'             => $variant['status'] ?? 1,
            ]);
        }

        return back()->with('success', 'Thêm biến thể thành công!');
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
            return redirect()->route('product_variants.index')->with('error','Product does not exist!');
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
            return redirect()->route('product_variants.index')->with('error', 'Danh Mục Không Tồn Tại!');
        }

        // Validate dữ liệu gửi lên từ form
        $param = $request->validate([
            'sku' => ['required','string','max:50',     
                Rule::unique('product_variants')->where(function ($query) use ($request) {
                    return $query->where('product_id', $request->product_id);
                })->ignore($id),
            ],
            'product_id' => 'required|exists:products,id',
            'price' => 'required|numeric|min:0',
            'promotional_price' => 'nullable|numeric|min:0|lte:price',
            'quantity' => 'required|integer|min:0',
            'status' => 'required|in:0,1'
        ]);

        // Thực hiện cập nhật
        $productVariant->update($param);

        return redirect()->route('product_variants.index')->with('success', 'Cập Nhật Danh Mục Thành Công!');
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
        $title = "Product variant";
        $listVariant = ProductVariant::where('status', false)->get();
        return view('admin.product_variants.variantDiscontinued',compact('title','listVariant'));
    }
}
