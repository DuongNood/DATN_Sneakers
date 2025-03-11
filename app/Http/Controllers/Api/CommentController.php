<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Log;

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
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Comment::destroy($id);

        return response()->json([], 204);

    }
}
