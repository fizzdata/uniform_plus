<?php

use App\Models\Shop;
use App\Models\Status;
use Illuminate\Http\Request;
use App\Http\Middleware\VerifyShop;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PurchaseOrdersController;



Route::middleware(VerifyShop::class)->group(function () {

Route::get('/shopify/install', function (Request $request) {
    return response()->json(['success' => true, 'shop' => $request->shop]);
})->name('shopify.install');

Route::get('/shopify/orders', [OrdersController::class, 'index'])->name('orders.index');

Route::get('/orders/update-status/{order_id}', [StatusController::class, 'updateStatus'])->name('orders.updateStatus');
Route::get('/statuses', [StatusController::class, 'getStatus'])->name('statuses.index');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');


Route::get('/purchase-orders', [PurchaseOrdersController::class, 'index'])->name('purchase_orders.index');
Route::post('/purchase-order', [PurchaseOrdersController::class, 'store'])->name('purchase_orders.store');
Route::post('/purchase-orders/{order_id}/update', [PurchaseOrdersController::class, 'update'])->name('purchase_orders.update');
Route::post('/purchase-orders/receive', [PurchaseOrdersController::class, 'receive'])->name('purchase_orders.receive');
Route::delete('/purchase-orders/{order_id}', [PurchaseOrdersController::class, 'delete'])->name('purchase_orders.delete');


Route::get('/inventory/locations', [InventoryController::class, 'get_locations'])->name('inventory.locations');
Route::get('/inventory', [InventoryController::class, 'getInventory'])->name('inventory.get');
Route::post('/inventory/receive', [InventoryController::class, 'receiveItems'])->name('inventory.receive');


});//end of middleware group