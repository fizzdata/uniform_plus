<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopifyAuthController;
use App\Http\Controllers\ShopifyWebhookController;

Route::get('/', function () {
    return 'welcome to the Shopify App';
});

Route::get('/auth/shopify', [ShopifyAuthController::class, 'redirectToShopify'])->name('shopify.auth');
Route::get('/auth/callback', [ShopifyAuthController::class, 'handleCallback']);

Route::get('/test', function () {
    return DB::table('orders')->get();
});

Route::post('/shopify/webhooks/inventory-updated', [ShopifyWebhookController::class, 'handleInventoryUpdate']);
Route::post('/shopify/webhooks/order-created', [ShopifyWebhookController::class, 'handleOrderCreated']);

