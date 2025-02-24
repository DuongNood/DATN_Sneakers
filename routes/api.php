<?php

use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\UserController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
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
