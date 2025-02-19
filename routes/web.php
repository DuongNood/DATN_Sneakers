<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\ProductVariantController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::prefix('admins.')
// ->as('admins.')
// ->group(function(){
        Route::prefix('categories')
        ->as('categories.')
        ->group(function(){
            Route::get('/', [CategoryController::class, 'index'])->name('index');
            Route::get('create', [CategoryController::class, 'create'])->name('create');
            Route::post('store', [CategoryController::class, 'store'])->name('store');
            Route::get('{id}/edit', [CategoryController::class, 'edit'])->name('edit');
            Route::put('update/{id}', [CategoryController::class, 'update'])->name('update');
            Route::delete('destroy/{id}', [CategoryController::class, 'destroy'])->name('destroy');
        }); 
         Route::prefix('products')
        ->as('products.')
        ->group(function(){
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('create', [ProductController::class, 'create'])->name('create');
            Route::post('store', [ProductController::class, 'store'])->name('store');
            Route::get('{id}/edit', [ProductController::class, 'edit'])->name('edit');
            Route::put('update/{id}', [ProductController::class, 'update'])->name('update');
            Route::delete('destroy/{id}', [ProductController::class, 'destroy'])->name('destroy');
            Route::get('product_discontinued',[ProductController::class, 'productDiscontinued'])->name('productDiscontinued');
        });
        Route::prefix('product_variants')
        ->as('product_variants.')
        ->group(function(){
            Route::get('/', [ProductVariantController::class, 'index'])->name('index');
            Route::get('create', [ProductVariantController::class, 'create'])->name('create');
            Route::post('store', [ProductVariantController::class, 'store'])->name('store');
            Route::get('{id}/edit', [ProductVariantController::class, 'edit'])->name('edit');
            Route::put('update/{id}', [ProductVariantController::class, 'update'])->name('update');
            Route::delete('destroy/{id}', [ProductVariantController::class, 'destroy'])->name('destroy');          
        });                     
// });