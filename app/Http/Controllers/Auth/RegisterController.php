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
        // 'phone' => 'nullable|string|max:15',
        // 'address' => 'nullable|string|max:255',
        'password' => ['required', 'string', 'min:8', 'confirmed'], // Thêm 'confirmed'
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
            // Nếu role 1 hoặc 2 đã có, chặn việc tạo user mới
            return response()->json([
                'error' => "Role ID $roleId đã tồn tại, không thể tạo thêm tài khoản!",
            ], 403);
        }
    } else {
        // Lấy role_id lớn nhất trong bảng roles và tăng thêm 1
        $maxRoleId = \DB::table('roles')->max('id') ?? 2;
        $roleId = $maxRoleId + 1;

        // Tạo role mới
        \DB::table('roles')->insert([
            'id' => $roleId,
            'name' => 'User ' . $roleId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // Tạo user mới
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        // 'phone' => $request->phone,
        // 'address' => $request->address,
        'password' => Hash::make($request->password),
        'role_id' => $roleId,
    ]);

    return response()->json([
        'message' => 'Đăng ký thành công!',
        'user' => $user,
    ], 201);
}

}
