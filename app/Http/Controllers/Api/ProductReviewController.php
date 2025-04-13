<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Models\ProductReview;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\OrderDetail;

class ProductReviewController extends Controller
{
    //
    public function store(Request $request)
    {
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'order_detail_id' => 'required|exists:order_details,id',
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'nullable|string',
        ]);

        $user = auth()->user();

        // Kiểm tra order_detail thuộc về user
        $orderDeatil = OrderDetail::where('id', $request->order_detail_id)
            ->whereHas('order', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where('product_variant_id', $request->product_variant_id)
            ->first();

        if (!$orderDeatil) {
            return response()->json(['message' => 'Bạn không thể đánh giá sản phẩm này.'], 403);
        }

        // Kiểm tra đã đánh giá chưa
        $exists = ProductReview::where('user_id', $user->id)
            ->where('product_variant_id', $request->product_variant_id)
            ->where('order_detail_id', $request->order_detail_id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Bạn đã đánh giá sản phẩm này.'], 409);
        }

        // Tạo review
        $review = ProductReview::create([
            'user_id' => $user->id,
            'product_variant_id' => $request->product_variant_id,
            'order_detail_id' => $request->order_detail_id,
            'rating' => $request->rating,
            'content' => $request->content,
        ]);

        return response()->json(['message' => 'Đánh giá thành công.', 'review' => $review]);
    }

    public function getReviewsByProduct($productId)
{
    $reviews = ProductReview::whereIn('product_variant_id', function ($query) use ($productId) {
        $query->select('id')
            ->from('product_variants')
            ->where('product_id', $productId);
    })->with(['user', 'productVariant'])->latest()->get();

    return response()->json([
        'product_id' => $productId,
        'reviews' => $reviews,
    ]);
}

}
