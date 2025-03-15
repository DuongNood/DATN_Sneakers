<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        User::destroy($id);
        
        return response()->json([], 204);
    }

    public function forceDestroy(string $id)
    {
        $user = User::find($id);

        if ($user) {
            $user->forceDelete();

            return response()->json([], 204);
        }

        return response()->json([
            'message' => 'Không tồn tại bản ghi có ID là: ' . $id
        ], 404);
    }
}
