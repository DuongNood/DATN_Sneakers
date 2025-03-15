<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Comment::latest('id')->paginate(30);

        return response()->json([
            'status' => true,
            'message' => 'Thành công',
            'data' => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'content' => 'required',
            'status' => 'nullable|in:0,1'
        ]);

        try {

            $comment = Comment::query()->create($data);

            return response()->json($comment, 201);

        } catch (\Throwable $th) {
            Log::error(
                __CLASS__ . '@' . __FUNCTION__,
                ['error' => $th->getMessage()]
            );

            return response()->json([
                'message' => 'Lỗi hệ thống ' . $th
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $comment = Comment::find($id);

        if ($comment) {
            return response()->json($comment);
        }

        return response()->json([
            'message' => 'Không tồn tại cmt có id=' . $id
        ], 404);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'content' => 'required',
            'status' => 'nullable|in:0,1'
        ]);

        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json([
                'message' => 'KHông tồn tại cmt có id=' . $id
            ], 404);
        }
        try {

            // $data['status'] ??= 0;

            $comment->update($data);

            return response()->json($comment);

        } catch (\Throwable $th) {

            Log::error(
                __CLASS__ . '@' . __FUNCTION__,
                ['error' => $th->getMessage()]
            );

            return response()->json([
                'message' => 'Loi he thong ' . $th
            ], 500);

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Comment::destroy($id);

        return response()->json([], 204);

    }
    public function getCmtByProductId(Product $product)
    {

        try {
            $comments = Comment::where('product_id', $product->id)->get();

            if ($comments->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'không tìm thấy dữ liệu bản ghi có id sản phẩm = ' . $product->id
                ], 404);
            }
            return response()->json([
                'status' => true,
                'message' => 'Hiển thị thành công',
                'data' => $comments
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi hệ thống: ' . $th->getMessage()
            ], 500);
        }

    }
}
