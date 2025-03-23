<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
        $user = User::find($id);

        if ($user) {
            return response()->json($user);
        }

        return response()->json([
            'message' => 'Không tồn tại bản ghi có ID là: ' . $id
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Không tồn tại bản ghi có ID là: ' . $id
            ], 404);
        }

        try {
            if ($request->hasFile('image_user')) {
                $data['image_user'] = Storage::put('users', $request->file('image_user'));
            }

            $currentImage = $user->image_user;

            $user->update($request->all());

            if ($request->hasFile('image_user') && !empty($currentImage) && Storage::exists($currentImage)) {
                Storage::delete($currentImage);
            }

            return response()->json($user, 201);
        } catch (\Throwable $th) {

            if (!empty($data['image_user']) && Storage::exists($data['image_user'])) {
                Storage::delete($data['image_user']);
            }

            Log::error(
                __CLASS__ . '@' . __FUNCTION__,
                ['error' => $th->getMessage()]
            );

            return response()->json([
                'message' => 'Lỗi hệ thống'
            ], 500);
        }
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
