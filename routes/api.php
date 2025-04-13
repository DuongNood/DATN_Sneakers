<?php


use App\Http\Controllers\api\DetailController;
use App\Http\Controllers\Api\MomopaymentController;
use App\Http\Controllers\Api\SettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\admin\PromotionController;

use App\Http\Controllers\api\HomeController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\UserController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ProductController;
use App\Http\Controllers\CartController;

use App\Http\Controllers\Auth\RegisterController;

use App\Http\Controllers\Api\StatisticsController;

use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Models\Promotion;
use Illuminate\Support\Facades\Auth;

Route::apiResource('banners', BannerController::class);

// Đăng ký tài khoản
Route::post('/register', [RegisterController::class, 'register'])->name('api.register');

// Đăng nhập và đăng xuất
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');

// Đổi mật khẩu
Route::post('/change-password', [ChangePasswordController::class, 'changePassword'])
    ->middleware('auth:sanctum')->name('api.change-password');

Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword'])->name('api.forgot-password');

// Đặt lại mật khẩu bằng token
Route::get('/reset-password/{token}', function ($token) {
    return response()->json(['token' => $token]);
})->name('password.reset');

// Xử lý đặt lại mật khẩu
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

//danh sách sản phẩm
Route::get('/products', [ProductController::class, 'index']);



Route::middleware('auth:sanctum')->get('/users', function (Request $request) {
    return $request->user();
});

// route tin tức
Route::resource('news', NewsController::class);
// User
Route::middleware('auth:sanctum')->group(function () {
    // Lấy thông tin user hiện tại
    Route::get('/user', function () {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'image_user' => $user->image_user,
            'role_id' => $user->role_id,
        ]);
    });

    // Cập nhật thông tin user
    Route::put('/user', [UserController::class, 'update']);
});
Route::get('settings', [SettingController::class, 'index']);
// Promotion
Route::post('/promotions', [PromotionController::class, 'checkPromotion']);

Route::get('/statistics/total-revenue', [StatisticsController::class, 'totalRevenue']);
Route::get('/statistics/total-orders', [StatisticsController::class, 'totalOrders']);
Route::get('/statistics/best-selling-products', [StatisticsController::class, 'bestSellingProducts']);
Route::get('/statistics/top-customers', [StatisticsController::class, 'topCustomers']);

Route::get('/home-products', [HomeController::class, 'getHomeProducts']);
Route::get('/detail-product/{id}', [DetailController::class, 'getProductDetail']);
Route::get('/products-related/{id}', [DetailController::class, 'getRelatedProducts']);
Route::get('/categories', [HomeController::class, 'getCategories']);
Route::get('/productbycategory/{id}', [HomeController::class, 'categoryByProduct']);
Route::get('/products/top-views', [HomeController::class, 'getTopViewedProducts']);
// mua hàng

Route::middleware('auth:sanctum')->group(function () {
    Route::post('carts/add', [CartController::class, 'addToCart']);
    Route::get('carts/list', [CartController::class, 'getCart']);
    Route::put('carts/update', action: [CartController::class, 'updateCart']);
    Route::delete('carts/remove/{cart_item_id}', [CartController::class, 'removeFromCart']);
    Route::get('/orders/{id}', [OrderController::class, 'orderDetails']);
    Route::post('/orders/buy/{product_name}', [OrderController::class, 'buyProductByName']);
    Route::get('/vnpay-return', action: [OrderController::class, 'vnpayCallback']);

});


