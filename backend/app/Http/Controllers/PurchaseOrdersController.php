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
        'inventory_item_id' => 'required|integer',
        'paid' => 'boolean',

    ]);

    if ($validate->fails()) {
        return response()->json(['error' => $validate->errors()], 422);
    }


    $orderId = DB::table('purchase_orders')->insertGetId([
        'shop_id' => $request->shop['id'],
        'shopify_product_id' => $request->shopify_product_id,
        'quantity_ordered' => $request->quantity_ordered,
        'supplier_name' => $request->supplier,
        'inventory_item_id' => $request->inventory_item_id,
        'paid' => $request->paid ?? false,
        'status' => 'pending',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return response()->json(['success' => true, 'order_id' => $orderId]);


}

public function update(Request $request)
{
    $validate = Validator::make($request->all(), [
        'quantity_ordered' => 'required|integer|min:1',
        'supplier' => 'required|string|max:255',
    ]);

    if ($validate->fails()) {
        return response()->json(['error' => $validate->errors()], 422);
    }

    DB::table('purchase_orders')
        ->where('id', $request->order_id)
        ->update([
            'quantity_ordered' => $request->quantity_ordered,
            'supplier_name' => $request->supplier,
            'updated_at' => now(),
        ]);

    return response()->json(['success' => true, 'message' => 'Order updated successfully']);
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

       $order = DB::table('purchase_orders')->where('id', $request->order_id)->first();

       if($order->quantity_received < 1):
            $connect = Http::withHeaders([
                        'X-Shopify-Access-Token' => $request->shop['access_token'],
                    ])->post("https://{$request->shop['shop_domain']}/admin/api/2024-10/inventory_levels/connect.json", [
                        'location_id' => $request->location_id,          // From step 1
                        'inventory_item_id' => $order->inventory_item_id,   
                    ]); 
                    // Abort if connection fails
        if ($connect->failed()) {
            throw new \Exception("Failed to connect inventory item to location.");
        }   
        endif;

        DB::table('purchase_orders')
            ->where('id', $request->order_id)
            ->update([
                'quantity_received' => DB::raw("quantity_received + {$request->quantity}"),
                'status' => $this->calculateStatus($request->order_id, $request->quantity),
                'received_at' => now()
            ]);
        
            
        $adjustmentResponse = Http::withHeaders([
            'X-Shopify-Access-Token' => $request->shop['access_token'],
        ])->post("https://{$request->shop['shop_domain']}/admin/api/2024-10/inventory_levels/adjust.json", [
            'location_id' => $request->location_id,          // From step 1
            'inventory_item_id' => $order->inventory_item_id,   
            'available_adjustment' => + $request->quantity
        ]);

            // Abort if inventory update fails
    if ($adjustmentResponse->failed()) {
        throw new \Exception("Failed to update Shopify inventory.");
    }
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

public function delete(Request $request)
{
    $validate = Validator::make($request->all(), [
        'order_id' => 'required|integer|exists:purchase_orders,id',
    ]);

    if ($validate->fails()) {
        return response()->json(['error' => $validate->errors()], 422);
    }

    DB::table('purchase_orders')->where('id', $request->order_id)->delete();

    return response()->json(['success' => true, 'message' => 'Purchase Order deleted successfully']);
}

}
