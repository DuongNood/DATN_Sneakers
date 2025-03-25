<?php


use App\Http\Controllers\api\DetailController;
use App\Http\Controllers\Api\MomopaymentController;
use App\Http\Controllers\Api\SettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Api\BannerController;


use App\Http\Controllers\api\HomeController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\UserController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ProductController;
use App\Http\Controllers\CartController;

use App\Http\Controllers\Auth\RegisterController;

use App\Http\Controllers\Api\StatisticsController;

use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Models\Promotion;

Route::apiResource('banners', BannerController::class);

// Đăng ký tài khoản
Route::post('/register', [RegisterController::class, 'register'])->name('api.register');
        
// Đăng nhập và đăng xuất
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');

// Đổi mật khẩu
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
//danh sách sản phẩm
Route::get('/products', [ProductController::class, 'index']);



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// route bình luận
Route::resource('comments', CommentController::class);
// lấy ra bình luận theo id sản phẩm 
Route::get('getCmtByProductId/{product}', [CommentController::class, 'getCmtByProductId'])->name('api.showCmt');
// route tin tức
Route::resource('news', NewsController::class);
// User
Route::apiResource('users', UserController::class);
Route::delete('users/{user}/forceDestroy', [UserController::class, 'forceDestroy'])->name('users.forceDestroy');
// Setting
Route::get('settings', [SettingController::class, 'index']);
// Promotion
Route::get('/promotions', function () {
    return response()->json(Promotion::where('status', 1)->get());
});


Route::get('/statistics/total-revenue', [StatisticsController::class, 'totalRevenue']);
Route::get('/statistics/total-orders', [StatisticsController::class, 'totalOrders']);
Route::get('/statistics/best-selling-products', [StatisticsController::class, 'bestSellingProducts']);
Route::get('/statistics/top-customers', [StatisticsController::class, 'topCustomers']);

// mua hàng
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/orders/buy/{product_id}', [OrderController::class, 'buyProduct']);
    Route::post('/orders/confirm/{order_id}', [OrderController::class, 'confirmOrder']);
});

Route::get('/home-products', [HomeController::class, 'getHomeProducts']);
Route::get('/detail-product/{id}', [DetailController::class, 'getProductDetail']);
Route::get('/products-related/{id}', [DetailController::class, 'getRelatedProducts']);
Route::get('/categories', [HomeController::class, 'getCategories']);
Route::get('/productbycategory/{id}', [HomeController::class, 'categoryByProduct']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cart', [CartController::class, 'getUserCart']);
    Route::get('/admin/carts', [CartController::class, 'getAllCarts'])->middleware('isAdmin');
    Route::post('/cart/add', [CartController::class, 'addToCart']);
    Route::put('/cart/update/{id}', [CartController::class, 'updateCartItem']);
    Route::delete('/cart/remove/{id}', [CartController::class, 'removeCartItem']);
    Route::delete('/cart/clear', [CartController::class, 'clearCart']);
    Route::post('/cart/checkout', [CartController::class, 'checkout']);
});

// MomoPayment 
// tạo thanh toán momo
Route::post('/momo/payment', [MomopaymentController::class, 'createPayment']);
// xử lí phản hồi từ momo
Route::post('/momo/callback', [MomopaymentController::class, 'momoCallback']);
// lấy danh sách giao dịch 
Route::get('/momo/transactions', [MomopaymentController::class, 'getTransactions']);




