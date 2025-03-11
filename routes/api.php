<?php


use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\NewsController;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\UserController;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Đăng ký tài khoản
Route::post('/register', [RegisterController::class, 'register'])->name('api.register');

// Đăng nhập
Route::post('/login', [LoginController::class, 'login'])->name('api.login');

// Các route yêu cầu xác thực
Route::middleware('auth:sanctum')->group(function () {
    // Đăng xuất
    Route::post('/logout', [LogoutController::class, 'logout'])->name('api.logout');

    // Lấy thông tin người dùng
    // Route::get('/user', [UserController::class, 'getUser'])->name('api.user');
});


// Đăng nhập
Route::post('/login', [LoginController::class, 'login'])->name('api.login');

// Các route yêu cầu xác thực
Route::middleware('auth:sanctum')->group(function () {
    // Đăng xuất
    Route::post('/logout', [LogoutController::class, 'logout'])->name('api.logout');
 
    // Lấy thông tin người dùng
    // Route::get('/user', [UserController::class, 'getUser'])->name('api.user');
});

Route::resource('comments', CommentController::class);
Route::resource('news', NewsController::class);
