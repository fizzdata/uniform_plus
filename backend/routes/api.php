<?php

use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/shopify/token', function (Request $request) {
    $shop = $request->query('shop');
    $storedShop = Shop::where('shop_domain', $shop)->first();

    if (!$storedShop) {
        return response()->json(['error' => 'Shop not found'], 404);
    }

    return response()->json(['access_token' => $storedShop->access_token]);
});




Route::get('/shopify/orders', function (Request $request) {
    $shopifyUrl = "https://your-shop.myshopify.com/admin/api/2023-07/orders.json";
    $response = Http::withHeaders([
        'X-Shopify-Access-Token' => env('SHOPIFY_API_KEY'),
    ])->get($shopifyUrl);

    return $response->json();
});