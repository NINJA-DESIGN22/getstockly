<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InboundController;
use App\Http\Controllers\OutboundController;
use App\Http\Controllers\ProductGridController;
use App\Http\Controllers\ProductListController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // === tambahan untuk edit & simpan foto profil
    Route::post('/profile/photo', [AuthController::class, 'updatePhoto'])->name('profile.photo.update');

    // Inbound (hanya bisa diakses jika login)
    Route::get('/inbound', [InboundController::class, 'index'])->name('inbound.index');
    Route::post('/inbound', [InboundController::class, 'store'])->name('inbound.store');
    Route::post('/inbound/sync-missing', [\App\Http\Controllers\InboundController::class, 'syncMissing'])
    ->name('inbound.sync-missing');


    // (opsional) outbound juga diamankan
    Route::get('/outbound',  [OutboundController::class, 'index'])->name('outbound.index');
    Route::post('/outbound', [OutboundController::class, 'store'])->name('outbound.store');

    // PRODUCT GRID
    Route::get('/products/grid', [ProductGridController::class, 'index'])->name('products.grid');
    Route::get('/api/products/latest', [ProductGridController::class, 'api'])->name('products.api');
    Route::post('/products/{product}/photo', [ProductGridController::class, 'updatePhoto'])->name('products.photo.update');

    // PRODUCT LIST
    Route::get('/products/list', [ProductListController::class, 'index'])->name('productslist.index');
    Route::get('/products/export/excel', [ProductListController::class, 'exportExcel'])->name('products.export.excel');
    Route::get('/products/export/pdf',   [ProductListController::class, 'exportPdf'])->name('products.export.pdf');
    Route::post('/products/import', [ProductListController::class, 'importExcel'])->name('products.import.excel');
    Route::put('/products/{product}/minimum-stock', [ProductListController::class, 'updateMinimumStock'])
        ->name('products.minimum-stock.update');
});

Route::get('/', fn () => redirect()->route('dashboard'))->name('home');
