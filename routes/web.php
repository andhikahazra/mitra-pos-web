<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogStokController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RopController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StokBatchController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

Route::middleware(['auth', 'pemilik'])->group(function (): void {
    // Dashboard Overview
    Route::get('/', [DashboardController::class, 'index'])->name('pemilik.dashboard');

    // Produk
    Route::resource('produk', ProductController::class);

    // Barang Masuk
    Route::get('/barang-masuk', [BarangMasukController::class, 'index'])->name('barang-masuk.index');
    Route::get('/barang-masuk/{barangMasuk}', [BarangMasukController::class, 'show'])->name('barang-masuk.show');
    Route::put('/barang-masuk/{barangMasuk}/status', [BarangMasukController::class, 'updateStatus'])->name('barang-masuk.update-status');

    // Supplier
    Route::resource('supplier', SupplierController::class)->except(['destroy']);

    // Log & Batch Stok
    Route::get('/log-stok', [LogStokController::class, 'index'])->name('log-stok.index');
    Route::get('/log-stok/{logStok}', [LogStokController::class, 'show'])->name('log-stok.show');
    Route::get('/stok-batch', [StokBatchController::class, 'index'])->name('stok-batch.index');
    Route::get('/stok-batch/{produk}', [StokBatchController::class, 'show'])->name('stok-batch.show');

    // ROP
    Route::get('/rop', [RopController::class, 'index'])->name('rop.index');
    Route::get('/rop/{produk}', [RopController::class, 'show'])->name('rop.show');

    // Transaksi
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/{transaksi}', [TransaksiController::class, 'show'])->name('transaksi.show');

    // Laporan
    Route::get('/laporan', [\App\Http\Controllers\LaporanController::class, 'index'])->name('laporan.index');

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});
