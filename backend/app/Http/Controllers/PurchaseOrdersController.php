<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Validator;

class PurchaseOrdersController extends Controller
{
   public function index(Request $request)
{
   $perPage = $request->input('per_page', 20);
    
    $orders = DB::table('purchase_orders')
        ->select('*')
        ->where('shop_id', $request->shop['id'])
        ->paginate($perPage);

    return response()->json([
        'data' => $orders->items(),
        'total' => $orders->total(),
        'current_page' => $orders->currentPage(),
        'per_page' => $orders->perPage(),
    ]);

    
}

public function store(Request $request)
{

    $validate = Validator::make($request->all(), [
        'shopify_product_id' => 'required|integer',
        'quantity_ordered' => 'required|integer|min:1',
        'supplier' => 'required|string|max:255',
    ]);

    if ($validate->fails()) {
        return response()->json(['error' => $validate->errors()], 422);
    }

    $shop_id = DB::table('shops')->where('shop_domain', $request->shop)->value('id');

    $orderId = DB::table('purchase_orders')->insertGetId([
        'shop_id' => $shop_id,
        'shopify_product_id' => $request->shopify_product_id,
        'quantity_ordered' => $request->quantity_ordered,
        'supplier_name' => $request->supplier,
        'status' => 'pending',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return response()->json(['success' => true, 'order_id' => $orderId]);


}
public function receive(Request $request)
{

    $validate = Validator::make($request->all(), [
        'quantity' => 'required|integer|min:1',
        'location_id' => 'required|integer',
    ]);
    if ($validate->fails()) {
        return response()->json(['error' => $validate->errors()], 422);
    }

    DB::transaction(function () use ($request) {
        // Update purchase order
        DB::table('purchase_orders')
            ->where('id', $request->order_id)
            ->update([
                'quantity_received' => DB::raw("quantity_received + {$request->quantity}"),
                'status' => $this->calculateStatus($request->order_id, $request->quantity),
                'received_at' => now()
            ]);

        // Update Shopify inventory
        $order = DB::table('purchase_orders')->find($request->order_id);
        
            
        $adjustmentResponse = Http::withHeaders([
            'X-Shopify-Access-Token' => $request->shop['access_token'],
        ])->post("https://{$request->shop['shop_domain']}/admin/api/2024-10/inventory_levels/adjust.json", [
            'location_id' => $request->location_id,          // From step 1
            'inventory_item_id' => $order->shopify_product_id,   
            'available' => $order->quantity_received
        ]);
    });

    return response()->json(['success' => true, 'message' => 'Order received successfully']);
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
