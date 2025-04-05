<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Log toàn bộ dữ liệu nhận được
        Log::info('Request data received:', [
            'all' => $request->all(),
            'files' => $request->files->all(),
            'headers' => $request->headers->all(),
            'content_type' => $request->header('Content-Type'),
        ]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|regex:/^[0-9]{10}$/',
            'image_user' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', [
                'errors' => $validator->errors()->all(),
                'request_data' => $request->all(),
            ]);
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = $request->only('name', 'address', 'phone');

            if ($request->hasFile('image_user')) {
                $file = $request->file('image_user');
                Log::info('Image file detected:', [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                    'path' => $file->getRealPath(),
                ]);

                $cloudinaryUpload = Cloudinary::upload($file->getRealPath(), [
                    'folder' => 'image_users',
                    'public_id' => 'user_' . $user->id . '_' . time(),
                    'overwrite' => true,
                ]);
                $data['image_user'] = $cloudinaryUpload->getSecurePath();
                Log::info('Image uploaded to Cloudinary:', ['url' => $data['image_user']]);
            } else {
                Log::info('No image file detected in request');
            }

            $user->update($data);

            return response()->json([
                'message' => 'Profile updated successfully',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'image_user' => $user->image_user,
                    'role_id' => $user->role_id,
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating profile', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'message' => 'Error updating profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}