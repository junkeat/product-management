<?php

use App\Http\Controllers\Auth\WebAuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [WebAuthController::class, 'create'])->name('login');
    Route::post('/login', [WebAuthController::class, 'store'])->name('login.store');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [WebAuthController::class, 'destroy'])->name('logout');

    Route::get('/products/export', [ProductController::class, 'export'])->name('products.export');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::post('/products/bulk-delete', [ProductController::class, 'bulkDestroy'])->name('products.bulk-destroy');
});

Route::get('/', function () {
    return view('welcome');
});
