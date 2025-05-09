<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReportController;

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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    // Admin and Manager
    Route::middleware(['role:admin,manager'])->group(function () {
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('menus', MenuController::class);

        // Reports
        Route::get('/reports/sales', [ReportController::class, 'salesReport']);
    });

    // Admin, Manager and Waiter
    Route::middleware(['role:admin,manager,waiter', 'verified'])->group(function () {
        Route::apiResource('orders', OrderController::class)->except(['store', 'destroy']);
    });

    // Customers can only create orders
    Route::post('/orders', [OrderController::class, 'store'])->middleware(['role:customer,waiter', 'verified']);
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->middleware(['role:admin', 'verified']);
});
