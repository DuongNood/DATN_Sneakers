<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => ['required', 'string', 'min:8', 'confirmed'],
        'role_id' => 'nullable|integer',
    ]);

    $roleId = $request->role_id;

    if ($roleId === 1 || $roleId === 2) {
        // Kiểm tra xem role có tồn tại chưa
        $roleExists = \DB::table('roles')->where('id', $roleId)->exists();
        if (!$roleExists) {
            // Nếu role chưa có thì tạo mới
            \DB::table('roles')->insert([
                'id' => $roleId,
                'name' => $roleId == 1 ? 'Admin' : 'Staff',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            return response()->json([
                'error' => "Role ID $roleId đã tồn tại, không thể tạo thêm tài khoản!",
            ], 403);
        }
    } else {
        // Gán role_id mặc định là 3 (User)
        $roleId = 3;
    }

    // Tạo user mới
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role_id' => $roleId,
    ]);

    return response()->json([
        'message' => 'Đăng ký thành công!',
        'user' => $user,
    ], 201);
}


}
