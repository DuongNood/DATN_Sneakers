<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    const PATH_VIEW = 'admin.comments.';

    public function index()
    {
        $users = User::all();
        $products = Product::all();
        $comments = Comment::query()->latest()->paginate(20);

        return view(self::PATH_VIEW . __FUNCTION__, compact('comments', 'users', 'products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
    //     return view(self::PATH_VIEW . __FUNCTION__);
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $data = $request->validate([
        //     'content'  => 'required',
        // ]);
        // try {

        //     Comment::query()->create($data);
        //     // dd($data);

        //     return redirect()->route('comments.index')->with('success', 'create comment successfully');

        // } catch (\Throwable $th) {
        //     //throw $th;
        //     return back()->with('error', $th->getMessage());
        // }
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        //
    }
}
