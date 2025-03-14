<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'email_verified_at' => 'nullable|date',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'remember_token' => 'nullable|string|max:100',
            'created_at' => 'nullable|date',
            'updated_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        // Tạo user mới
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'email_verified_at' => $request->email_verified_at,
            'password' => Hash::make($request->password),
            'remember_token' => $request->remember_token ?? Str::random(10),
            'created_at' => $request->created_at ?? now(),
            'updated_at' => $request->updated_at ?? now(),
        ]);

        return response()->json([
            'message' => 'Đăng ký thành công!',
            'user' => $user
        ], 201);
    }
}
