<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    const PATH_VIEW = 'admin/promotions.';

    public function index()
    {
        $promotions = Promotion::latest()->paginate(10);
        return view(self::PATH_VIEW . __FUNCTION__, compact('promotions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view(self::PATH_VIEW . __FUNCTION__);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'promotion_name' => 'required|string|max:255',
            'discount_type' => 'required|in:' . Promotion::SO_TIEN . ',' . Promotion::PHAN_TRAM,
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'max_discount_value' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        Promotion::create($request->all());

        return redirect()
            ->route(self::PATH_VIEW . 'index')
            ->with('success', 'Mã giảm giá đã được tạo.');
    }

    /**
     * Hiển thị form chỉnh sửa mã giảm giá.
     */
    public function edit(Promotion $promotion)
    {
        return view(self::PATH_VIEW . __FUNCTION__, compact('promotion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Promotion $promotion)
    {
        $request->validate([
            'promotion_name' => 'required|string|max:255',
            'discount_type' => 'required|in:' . Promotion::SO_TIEN . ',' . Promotion::PHAN_TRAM,
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'max_discount_value' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $promotion->update($request->all());

        return redirect()
            ->route(self::PATH_VIEW . 'index')
            ->with('success', 'Mã giảm giá đã được cập nhật.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promotion $promotion)
    {
        $promotion->delete();
        return redirect()
            ->route(self::PATH_VIEW . 'index')
            ->with('success', 'Mã giảm giá đã được xóa.');
    }
}
