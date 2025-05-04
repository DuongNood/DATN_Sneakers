<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ], [
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Email không đúng định dạng.',
            'password.required' => 'Mật khẩu không được để trống.'
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->role->id === 1 || $user->role->id === 2) {
                return redirect()->route('admin.index');
            }

            Auth::logout();
            return back()->withErrors(['email' => 'Bạn không có quyền truy cập!']);
        }

        // Kiểm tra riêng email và mật khẩu để đưa ra thông báo lỗi chi tiết hơn
        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email không tồn tại.']);
        }

        return back()->withErrors(['password' => 'Mật khẩu không đúng.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.auth.login')->with('message', 'Đăng xuất thành công!');
    }
}