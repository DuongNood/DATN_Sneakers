<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    const PATH_VIEW = 'admin.promotions.';

    public function index(Request $request)
    {
        $query = Promotion::query();

        // 🔍 Xử lý tìm kiếm
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('promotion_name', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('discount_value', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('max_discount_value', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('recipient_address', 'LIKE', '%' . $request->search . '%');
            });
        }

        // Lọc theo loại (Giảm theo % hoặc giảm số tiền)
        if ($request->has('discount_type') && in_array($request->discount_type, ['Giảm theo %', 'Giảm số tiền'])) {
            $query->where('discount_type', $request->discount_type);
        }

        // Xử lý lọc trạng thái (status: 0 = Inactive, 1 = Active)
        if ($request->has('status') && in_array($request->status, ['0', '1'])) {
            $query->where('status', $request->status);
        }

        // Lấy danh sách mã giảm giá và phân trang
        $data = $query->latest('id')->paginate(10);
        return view(self::PATH_VIEW . __FUNCTION__, compact('data'));
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
            'status' => 'required|in:0,1',
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
            'status' => 'required|in:0,1',
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

    /**
     * Kiểm tra mã giảm giá qua API
     */
    public function checkPromotion(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required|string',
            ]);

            $code = strtoupper($request->input('code'));
            $promotion = Promotion::where('promotion_name', $code)
                ->where('status', 1)
                ->where('start_date', '<=', Carbon::now())
                ->where('end_date', '>=', Carbon::now())
                ->first();

            if (!$promotion) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn.',
                ], 404);
            }

            // Ánh xạ discount_type sang type mà frontend mong đợi
            $type = $promotion->discount_type === Promotion::PHAN_TRAM ? 'percentage' : 'fixed';

            return response()->json([
                'status' => 'success',
                'data' => [
                    'code' => $promotion->promotion_name,
                    'type' => $type,
                    'value' => $promotion->discount_value,
                    'max_discount_value' => $promotion->max_discount_value, // Thêm nếu cần
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không thể kiểm tra mã giảm giá: ' . $e->getMessage(),
            ], 500);
        }
    }
}