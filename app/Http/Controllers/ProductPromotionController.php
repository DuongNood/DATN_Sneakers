<?php

namespace App\Http\Controllers;

use App\Models\ProductPromotion;
use App\Models\ProductVariant;
use App\Models\Promotion;
use Illuminate\Http\Request;

class ProductPromotionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'promotion_id' => 'required|exists:promotions,id',
        ]);

        ProductPromotion::create($request->all());

        return back()
            ->with('success', 'Mã giảm giá đã được áp dụng cho sản phẩm.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        ProductPromotion::findOrFail($id)->delete();
        return back()
            ->with('success', 'Mã giảm giá đã được gỡ khỏi sản phẩm.');
    }
}
