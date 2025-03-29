<?php

namespace App\Http\Controllers\Admin;

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
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->role->id === 1) {
                return redirect()->route('admin.index');
            }

            Auth::logout();
            return back()->withErrors(['email' => 'Bạn không có quyền truy cập!']);
        }

        return back()->withErrors(['email' => 'Sai thông tin đăng nhập.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.auth.login')->with('message', 'Đăng xuất thành công!');
    }
}
