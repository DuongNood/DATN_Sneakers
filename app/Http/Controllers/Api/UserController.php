<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class UserController extends Controller
{
    // Các method khác giữ nguyên...

    /**
     * Update the authenticated user's profile
     */
    public function update(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|regex:/^[0-9]{10}$/',
            'image_user' => 'nullable|image|mimes:jpg,jpeg,png|max:2048' 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = [
                'name' => $request->input('name'),
                'address' => $request->input('address'),
                'phone' => $request->input('phone'),
            ];

            // Xử lý image_user với Cloudinary
            if ($request->hasFile('image_user')) {
                // Upload ảnh lên Cloudinary
                $uploadedFile = $request->file('image_user');
                $cloudinaryUpload = Cloudinary::upload($uploadedFile->getRealPath(), [
                    'folder' => 'image_users'
                ]);

                // Lấy URL của ảnh đã upload
                $data['image_user'] = $cloudinaryUpload->getSecurePath();
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