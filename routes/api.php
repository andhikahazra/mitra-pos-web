<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\IncomingGoodsController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\SettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/settings', [SettingController::class, 'index']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/user', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Produk & Kategori API
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::get('/categories', [CategoryController::class, 'index']);

    // Dashboard API
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Barang Masuk API
    Route::get('/incoming-goods', [IncomingGoodsController::class, 'index']);
    Route::post('/incoming-goods', [IncomingGoodsController::class, 'store']);
    Route::get('/incoming-goods/{id}', [IncomingGoodsController::class, 'show']);
    Route::post('/incoming-goods/{id}/approve', [IncomingGoodsController::class, 'approve']);

    // Transaksi API
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::get('/transactions/{id}', [TransactionController::class, 'show']);
    Route::patch('/transactions/{id}/settle', [TransactionController::class, 'settle']);

    // Supplier API
    Route::get('/suppliers', [SupplierController::class, 'index']);
});
