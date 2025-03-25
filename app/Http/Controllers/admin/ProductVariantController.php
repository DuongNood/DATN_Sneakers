<?php

namespace App\Http\Controllers\admin;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\ProductSize;
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
        $productVariant = ProductVariant::where('status', true)
        ->orderBy('product_id') 
        ->paginate(10);

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
        
    try {
        $validatedData = $request->validate([
            'product_variants'                  => 'required|array', 
            'product_variants.*.product_size_id'=> 'required',
            'product_variants.*.product_id'     => 'required|exists:products,id', 
            'product_variants.*.quantity'       => 'required|integer|min:0', 
            'product_variants.*.status'         => 'nullable|in:0,1'
        ],[
            //THÊM THÔNG BÁO LỖI TÙY CHỈNH
            'product_variants.required' => 'Danh sách biến thể không được để trống!',
            'product_variants.array' => 'Dữ liệu biến thể không hợp lệ!',
            'product_variants.*.product_size_id.required' => 'Mỗi biến thể phải có một product_size_id!',
            'product_variants.*.product_id.required' => 'Mỗi biến thể phải có một product_id!',
            'product_variants.*.product_id.exists' => 'product_id không hợp lệ!',
            'product_variants.*.price.required' => 'Giá sản phẩm là bắt buộc!',
            'product_variants.*.price.min' => 'Giá không được nhỏ hơn 0!',
            'product_variants.*.promotional_price.lte' => 'Giá khuyến mãi phải nhỏ hơn hoặc bằng giá gốc!',
            'product_variants.*.quantity.required' => 'Số lượng là bắt buộc!',
            'product_variants.*.quantity.integer' => 'Số lượng phải là số nguyên!',
            'product_variants.*.status.in' => 'Trạng thái chỉ được là 0 hoặc 1!',
        ]);

        // Lưu vào database
        foreach ($validatedData['product_variants'] as $variant) {
            ProductVariant::create([
                'product_size_id'=> $variant['product_size_id'],
                'product_id'         => $variant['product_id'],               
                'quantity'           => $variant['quantity'],
                'status'             => $variant['status'] ?? 1,
            ]);
        }

        return redirect()->route('product_variants.index')->with('success', 'Thêm biến thể thành công!');
    } catch (ValidationException $e) {
        // Bắt lỗi validate của Laravel
        return back()->withErrors($e->errors())->withInput();
    } catch (QueryException $e) {
        // 💡 Bắt lỗi SQL trùng lặp SKU và tạo lỗi thủ công vào session
        if ($e->errorInfo[1] == 1062) {
            $errors = ['product_variants.*.product_size_id' => 'SKU đã tồn tại cho sản phẩm này!'];
            return back()->withErrors($errors)->withInput();
        }

        // Nếu là lỗi khác, hiển thị thông báo lỗi chung
        return redirect()->route('product_variants.index')->withErrors(['error' => 'Có lỗi xảy ra, vui lòng thử lại!'])->withInput();
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
            return redirect()->route('product_variants.index')->with('error','Sản phẩm không tồn tại!');
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
        $title = "Biến thể sản phẩm";
        $listVariant = ProductVariant::where('status', false)->get();
        return view('admin.product_variants.variantDiscontinued',compact('title','listVariant'));
    }
}
