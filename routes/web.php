<?php


use App\Http\Controllers\admin\CommentController;

use App\Http\Controllers\StatisticsController;

use App\Http\Controllers\admin\OrderController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\CategoryController;

use App\Http\Controllers\admin\UserController;


use App\Http\Controllers\admin\NewsController;


use App\Http\Controllers\BannerController;

use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\ProductVariantController;



use App\Http\Controllers\admin\PromotionController;
use App\Http\Controllers\admin\SizeController;
use App\Http\Controllers\ProductPromotionController;



/*
 |--------------------------------------------------------------------------
 @@ -27,64 +13,9 @@
 |
 */

Route::get('/', function () {
    return view('welcome');
});


// Route::prefix('admins.')
// ->as('admins.')
// ->group(function(){
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
// });


Route::resource('news', NewsController::class);
Route::resource('comments', CommentController::class);
//  Route::prefix('banners')
//      ->as('banners.')
//      ->group(function () {
//          Route::get('/', [BannerController::class, 'index'])->name('index');
//          Route::get('create', [BannerController::class, 'create'])->name('create');
//          Route::post('store', [BannerController::class, 'store'])->name('store');
//          Route::get('{id}/edit', [BannerController::class, 'edit'])->name('edit');
//          Route::put('update/{id}', [BannerController::class, 'update'])->name('update');
//          Route::delete('destroy/{id}', [BannerController::class, 'destroy'])->name('destroy');
//      });
Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics.index');

Route::resource('users', UserController::class);
Route::delete('users/{user}/forceDestroy', [UserController::class, 'forceDestroy'])->name('users.forceDestroy');

Route::resource('orders', OrderController::class);

Route::resource('promotions', PromotionController::class);
Route::post('/product-promotions', [ProductPromotionController::class, 'store'])->name('product-promotions.store');
Route::delete('/product-promotions/{id}', [ProductPromotionController::class, 'destroy'])->name('product-promotions.destroy');

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
Route::prefix('size')
    ->as('size.')
    ->group(function () {
        Route::get('/', [SizeController::class, 'index'])->name('index');
        Route::get('create', [SizeController::class, 'create'])->name('create');
        Route::post('store', [SizeController::class, 'store'])->name('store');
        Route::get('{id}/edit', [SizeController::class, 'edit'])->name('edit');
        Route::put('update/{id}', [SizeController::class, 'update'])->name('update');
        Route::delete('destroy/{id}', [SizeController::class, 'destroy'])->name('destroy');       
    });
