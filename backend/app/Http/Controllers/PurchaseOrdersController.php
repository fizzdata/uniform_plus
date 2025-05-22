<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class PurchaseOrdersController extends Controller
{
   public function index(Request $request)
{
   $perPage = $request->input('per_page', 20);
    
    $orders = DB::table('purchase_orders')
        ->join('suppliers', 'purchase_orders.supplier_id', '=', 'suppliers.id')
        ->join('products', 'purchase_orders.product_id', '=', 'products.id')
        ->select(
            'purchase_orders.id',
            'suppliers.name as supplier',
            'products.id as product_id',
            'products.name as product_name',
            'purchase_orders.ordered',
            'purchase_orders.received',
            'purchase_orders.status',
            'purchase_orders.created_at'
        )
        ->paginate($perPage);

    return response()->json([
        'data' => $orders->items(),
        'total' => $orders->total(),
        'current_page' => $orders->currentPage(),
        'per_page' => $orders->perPage(),
    ]);
}

public function receive($orderId, Request $request)
{
    DB::transaction(function () use ($orderId, $request) {
        // Update purchase order
        DB::table('purchase_orders')
            ->where('id', $orderId)
            ->update([
                'quantity_received' => DB::raw("quantity_received + {$request->quantity}"),
                'status' => $this->calculateStatus($orderId, $request->quantity),
                'received_at' => now()
            ]);

        // Update Shopify inventory
        $order = DB::table('purchase_orders')->find($orderId);
        Http::withHeaders([/* shopify headers */])
            ->post("https://store.myshopify.com/admin/api/2024-10/inventory_levels/adjust.json", [
                'inventory_item_id' => $order->shopify_product_id,
                'location_id' => 'your-location-id',
                'available_adjustment' => $request->quantity
            ]);
    });

    return response()->json(['success' => true]);
}

private function calculateStatus($orderId, $receivedQuantity)
{
    $order = DB::table('purchase_orders')->find($orderId);
    
    if ($order->quantity_ordered === ($order->quantity_received + $receivedQuantity)) {
        return 'received';
    }

    return ($order->quantity_received + $receivedQuantity) > 0 ? 'partial' : 'pending';
}
}
