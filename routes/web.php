<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BannerController;
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

Route::get('/', function () {
    return view('welcome');
});
Route::resource('banners')
->as('banners.')
->group(function(){
    Route::get('/', [BannerController::class, 'index'])->name('index');
    Route::get('create', [BannerController::class, 'create'])->name('create');
    Route::post('store', [BannerController::class, 'store'])->name('store');
    Route::get('{id}/edit', [BannerController::class, 'edit'])->name('edit');
    Route::put('update/{id}', [BannerController::class, 'update'])->name('update');
    Route::delete('destroy/{id}', [BannerController::class, 'destroy'])->name('destroy');
});
Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics.index');
//
