<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $title ="product variant";
        $productVariant= ProductVariant::get();
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
    // Kiểm tra request có chứa 'product_variants' không

    if (!$request->has('product_variants') || !is_array($request->product_variants)) {
        return back()->with('error', 'Dữ liệu không hợp lệ!');
    }

    foreach ($request->product_variants as $variant) {
        // Kiểm tra xem các trường quan trọng có tồn tại không
        if (!isset($variant['sku'], $variant['product_id'], $variant['price'], $variant['quantity'])) {
            return back()->with('error', 'Thiếu dữ liệu cần thiết!');
        }

        // Lưu dữ liệu vào database
        ProductVariant::create([
            'sku'                => $variant['sku'],
            'product_id'         => $variant['product_id'],
            'price'              => $variant['price'],
            'promotional_price'  => $variant['promotional_price'] ?? null,
            'quantity'           => $variant['quantity'],
            'status'             => $variant['status'] ?? 1, // Mặc định là 1 (hoạt động)
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
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
