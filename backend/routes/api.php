<?php

use App\Models\Shop;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\PurchaseOrdersController;
use App\Http\Controllers\InventoryController;

Route::get('/shopify/install', function (Request $request) {
    $shop = $request->query('shop');
    $storedShop = Shop::where('shop_domain', $shop)->first();

    if (!$storedShop) {
        return response()->json(['success' => false]);
    }

    return response()->json(['success' => true]);
});



Route::get('/shopify/orders', [OrdersController::class, 'index'])->name('orders.index');

Route::get('/orders/update-status/{order_id}', [StatusController::class, 'updateStatus'])->name('orders.updateStatus');
Route::get('/statuses', [StatusController::class, 'getStatus'])->name('statuses.index');

Route::get('/purchase-orders', [PurchaseOrdersController::class, 'index']);
Route::post('/orders/{order}/receive', [PurchaseOrdersController::class, 'receive']);


Route::get('/inventory', [InventoryController::class, 'getInventory'])->name('inventory.get');
Route::post('/inventory/receive', [InventoryController::class, 'receiveItems'])->name('inventory.receive');
