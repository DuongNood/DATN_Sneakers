<?php

use App\Http\Controllers\admin\BrandController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\AuthController;
use App\Http\Controllers\admin\StatisticsController;
use App\Http\Controllers\admin\OrderController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\admin\NewsController;
use App\Http\Controllers\admin\BannerController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\ProductVariantController;
use App\Http\Controllers\admin\PromotionController;
use App\Http\Controllers\admin\ReviewController;
use App\Http\Controllers\admin\SettingController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Auth\CommentController;

use App\Http\Controllers\admin\SizeController;
use App\Http\Controllers\ReplyController;
use App\Models\ProductReview;
use App\Http\Controllers\admin\DashboardStatisticsController;
use App\Models\User;

/*
 |--------------------------------------------------------------------------
 @@ -27,64 +13,9 @@
 |
 */

Route::prefix('admin')
    ->as('admin.')
    ->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('auth.login');
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout')->middleware('auth');

        Route::middleware('auth')->group(function () {
            Route::get('/', function () {
                return view('admin.index');
            })->name('index');

            Route::get('/dashboard/daily-statistics', [DashboardStatisticsController::class, 'getDailyData'])->name('dashboard.daily_statistics');
            Route::get('/dashboard/revenue-last-30-days', [DashboardStatisticsController::class, 'getRevenueLast30Days'])->name('dashboard.revenue_last_30_days');
            Route::get('/dashboard/payment-statuses', [DashboardStatisticsController::class, 'getAllPaymentStatuses'])->name('dashboard.payment_statuses');
            Route::get('/dashboard/order-statuses', [DashboardStatisticsController::class, 'getAllOrderStatuses'])->name('dashboard.order_statuses');
            Route::get('/dashboard/combined-statuses', [DashboardStatisticsController::class, 'getCombinedStatuses'])->name('dashboard.combined_statuses');
            Route::get('/admin/dashboard/all-new-customers', [DashboardStatisticsController::class, 'getAllNewCustomers'])->name('dashboard.all_new_customers');
            Route::get('/admin/dashboard/all-recent-orders', [DashboardStatisticsController::class, 'getAllRecentOrders'])->name('dashboard.all_recent_orders');
        });


        Route::middleware(['auth', 'permission:manage_banners'])->group(function () {
            Route::resource('banners', BannerController::class);
        });

        Route::middleware(['auth', 'permission:manage_comments'])->group(function () {
            Route::get('comments', [CommentController::class, 'adminIndex'])->name('comments.index');
        });

        Route::middleware(['auth', 'permission:manage_news'])->group(function () {
            Route::resource('news', NewsController::class);
        });

        Route::middleware(['auth', 'permission:manage_promotions'])->group(function () {
            Route::resource('promotions', PromotionController::class);
        });

        Route::middleware(['auth', 'permission:manage_users'])->group(function () {
            Route::resource('users', UserController::class);
        });

        Route::middleware(['auth', 'permission:manage_brands'])->group(function () {
            Route::resource('brands', BrandController::class);
        });

        Route::middleware(['auth', 'permission:manage_reviews'])->group(function () {
            Route::get('review', [ReviewController::class, 'index'])->name('review.index');
        });

        Route::middleware(['auth', 'permission:manage_categories'])->group(function () {
            Route::prefix('categories')
                ->as('categories.')
                ->group(function () {
                    Route::get('/', [CategoryController::class, 'index'])->name('index');
                    Route::get('create', [CategoryController::class, 'create'])->name('create');
                    Route::post('store', [CategoryController::class, 'store'])->name('store');
                    Route::get('{id}/edit', [CategoryController::class, 'edit'])->name('edit');
                    Route::put('update/{id}', [CategoryController::class, 'update'])->name('update');
                    Route::delete('destroy/{id}', [CategoryController::class, 'destroy'])->name('destroy');

                    Route::get('category-by-product/{id}', [CategoryController::class, 'categoryByProduct'])->name('categoryByProduct');
                });
        });

        Route::middleware(['auth', 'permission:manage_orders'])->group(function () {
            Route::prefix('orders')
                ->as('orders.')
                ->group(function () {
                    Route::get('/', [OrderController::class, 'index'])->name('index');
                    Route::get('{order}/edit', [OrderController::class, 'edit'])->name('edit');
                    Route::put('update/{order}', [OrderController::class, 'update'])->name('update');
                    // Routes cho quy trình hủy
                    Route::get('pending-cancellation', [OrderController::class, 'indexPendingCancellations'])->name('pending_cancellation');
                    Route::post('{order}/cancel-direct', [OrderController::class, 'cancelOrderDirectly'])->name('cancel_direct');
                    Route::post('{order}/confirm-cancellation', [OrderController::class, 'confirmCancellation'])->name('confirm_cancellation');
                    Route::post('{order}/reject-cancellation', [OrderController::class, 'rejectCancellation'])->name('reject_cancellation');
                    Route::get('order-cancellation', [OrderController::class, 'indexOrderCancellations'])->name('order_cancellation');
                });
        });

        Route::middleware(['auth', 'permission:manage_products'])->group(function () {
            Route::prefix('products')
                ->as('products.')
                ->group(function () {
                    Route::get('/', [ProductController::class, 'index'])->name('index');
                    Route::get('create', [ProductController::class, 'create'])->name('create');
                    Route::post('store', [ProductController::class, 'store'])->name('store');
                    Route::get('{id}/edit', [ProductController::class, 'edit'])->name('edit');
                    Route::put('update/{id}', [ProductController::class, 'update'])->name('update');
                    Route::delete('destroy/{id}', [ProductController::class, 'destroy'])->name('destroy');
                    Route::get('product_discontinued', [ProductController::class, 'productDiscontinued'])->name('productDiscontinued');
                    Route::get('show/{id}', [ProductController::class, 'show'])->name('show');
                });

            Route::prefix('product_variants')
                ->as('product_variants.')
                ->group(function () {
                    Route::get('/', [ProductVariantController::class, 'index'])->name('index');
                    Route::get('{id}/create', [ProductVariantController::class, 'create'])->name('create');
                    Route::post('store', [ProductVariantController::class, 'store'])->name('store');
                    Route::get('{id}/edit', [ProductVariantController::class, 'edit'])->name('edit');
                    Route::put('update/{id}', [ProductVariantController::class, 'update'])->name('update');
                    Route::delete('destroy/{id}', [ProductVariantController::class, 'destroy'])->name('destroy');
                    Route::get('variant_discontinued', [ProductVariantController::class, 'variantDiscontinued'])->name('variantDiscontinued');
                });

            Route::prefix('sizes')
                ->as('sizes.')
                ->group(function () {
                    Route::get('/', [SizeController::class, 'index'])->name('index');
                    Route::get('create', [SizeController::class, 'create'])->name('create');
                    Route::post('store', [SizeController::class, 'store'])->name('store');
                    Route::get('{id}/edit', [SizeController::class, 'edit'])->name('edit');
                    Route::put('update/{id}', [SizeController::class, 'update'])->name('update');
                    Route::delete('destroy/{id}', [SizeController::class, 'destroy'])->name('destroy');
                });
        });

        Route::middleware(['auth', 'permission:manage_statistics'])->group(function () {
            Route::get('statistics', [StatisticsController::class, 'index'])->name('statistics.index');
            Route::get('statistics/data', [StatisticsController::class, 'getData'])->name('statistics.data');
        });

        Route::middleware(['auth', 'permission:manage_settings'])->group(function () {
            Route::prefix('settings')
                ->as('settings.')
                ->group(function () {
                    Route::get('edit', [SettingController::class, 'edit'])->name('edit');
                    Route::put('update', [SettingController::class, 'update'])->name('update');
                });
        });
    });

// bình luận
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/comments', [CommentController::class, 'adminIndex'])->name('admin.comments.index');
    Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->name('admin.comments.destroy');
    Route::post('/replies', [ReplyController::class, 'adminStore'])->name('admin.replies.store');
});

Route::get('/api/productbybrand/{id}', [App\Http\Controllers\Api\HomeController::class, 'brandsByProduct']);
