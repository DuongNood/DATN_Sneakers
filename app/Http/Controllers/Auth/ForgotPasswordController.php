<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Gửi link đặt lại mật khẩu đến email người dùng
     */
    public function forgotPassword(Request $request)



    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        // Tìm user theo email
        $user = User::where('email', $request->email)->first();


        // Tạo mật khẩu tạm thời, random 8 kí tự 
        $temporaryPassword = Str::random(8);
        $user->password = Hash::make($temporaryPassword);
        $user->save();

        // Gửi email chứa mật khẩu tạm thời
        Mail::send('admin.emails.reset-password', ['user' => $user, 'temporaryPassword' => $temporaryPassword], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Khôi phục mật khẩu.');
        });

        return response()->json([
            'message' => 'Mật khẩu mới đã được gửi đến email của bạn, vui lòng kiểm tra hộp thư đến.',
        ], 200);
    }
}
