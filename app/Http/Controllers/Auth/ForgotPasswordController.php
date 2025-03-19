<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /**
     * Gửi link đặt lại mật khẩu đến email người dùng
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Đã gửi email đặt lại mật khẩu!',
            ], 200);
        }

        return response()->json([
            'message' => 'Không thể gửi email đặt lại mật khẩu!',
        ], 400);
    }
}
     