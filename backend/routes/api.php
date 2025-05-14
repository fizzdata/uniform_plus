<?php

use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrdersController;

Route::get('/shopify/install', function (Request $request) {
    $shop = $request->query('shop');
    $storedShop = Shop::where('shop_domain', $shop)->first();

    if (!$storedShop) {
        return response()->json(['error' => 'Shop not found, please install the app first.']);
    }

    return response()->json(['success' => true]);
});



Route::get('/shopify/orders', [OrdersController::class, 'index'])->name('orders.index');
