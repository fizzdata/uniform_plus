<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ShopifyAuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', function () {
    if (session('shopify_domain') && session('shopify_token')) {
        return redirect()->route('orders.index');
    }
    return redirect()->route('shopify.install');
});

// Shopify installation routes
Route::get('/install', [ShopifyAuthController::class, 'installShop'])->name('shopify.install');
Route::get('/auth/callback', [ShopifyAuthController::class, 'handleCallback'])->name('shopify.callback');

// Protected routes (require Shopify authentication)
Route::middleware(['auth.shopify'])->group(function () {
    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders/{orderId}/update-stage', [OrderController::class, 'updateStage'])->name('orders.update-stage');
});