<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // Các method khác giữ nguyên...

    /**
     * Update the authenticated user's profile
     */
    public function update(Request $request)
    {
        // Lấy user đã authenticate (vì route có middleware auth:sanctum)
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|regex:/^[0-9]{10}$/',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048' 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Chuẩn bị data để update
            $data = [
                'name' => $request->input('name'),
                'address' => $request->input('address'),
                'phone' => $request->input('phone'),
            ];

            // Xử lý avatar nếu có
            if ($request->hasFile('avatar')) {
                if ($user->avatar) {
                    Storage::delete('public/avatars/' . $user->avatar);
                }
                $file = $request->file('avatar');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/avatars', $filename);
                $data['avatar'] = $filename;
            }

            // Update user
            $user->update($data);

            return response()->json([
                'message' => 'Profile updated successfully',
                'data' => $user
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Các method khác giữ nguyên...
}