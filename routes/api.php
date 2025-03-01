<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StatisticsController;

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
Route::get('/statistics/total-revenue', [StatisticsController::class, 'totalRevenue']);
Route::get('/statistics/total-orders', [StatisticsController::class, 'totalOrders']);
Route::get('/statistics/best-selling-products', [StatisticsController::class, 'bestSellingProducts']);
Route::get('/statistics/top-customers', [StatisticsController::class, 'topCustomers']);
