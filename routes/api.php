<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ProductController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StatisticsController;
use Illuminate\Http\Request;
use App\Http\Controllers\BannerController;

Route::resource('banners', BannerController::class);

// Đăng ký tài khoản
Route::post('/register', [RegisterController::class, 'register'])->name('api.register');

// Đăng nhập và đăng xuất
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');

// Đổi mật khẩu khi đã đăng nhập
Route::post('/change-password', [ChangePasswordController::class, 'changePassword'])
    ->middleware('auth:sanctum')->name('api.change-password');

// Gửi email đặt lại mật khẩu
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

// Đặt lại mật khẩu bằng token
Route::get('/reset-password/{token}', function ($token) {
    return response()->json(['token' => $token]);
})->name('password.reset');

// Xử lý đặt lại mật khẩu
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
//danh sách sản phẩmphẩm
Route::get('/products', [ProductController::class, 'index']);



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/statistics/total-revenue', [StatisticsController::class, 'totalRevenue']);
Route::get('/statistics/total-orders', [StatisticsController::class, 'totalOrders']);
Route::get('/statistics/best-selling-products', [StatisticsController::class, 'bestSellingProducts']);
Route::get('/statistics/top-customers', [StatisticsController::class, 'topCustomers']);
