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
        'supplier' => 'required|string|max:255',
        'paid' => 'boolean',

        'items' => 'required|array',
        'items.*.inventory_item_id' => 'required|integer',
        'items.*.quantity_ordered' => 'required|integer|min:1',
    ]);

    if ($validate->fails()) {
        return response()->json(['error' => $validate->errors()], 422);
    }


   foreach ($request->items as $item):
    $orderId = DB::table('purchase_orders')->insert([
        'shop_id' => $request->shop['id'],
        'shopify_product_id' => $request->shopify_product_id,
        'quantity_ordered' => $item['quantity_ordered'],
        'supplier_name' => $request->supplier,
        'inventory_item_id' => $item['inventory_item_id'],
        'paid' => $request->paid ?? false,
        'status' => 'pending',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    endforeach;

    return response()->json(['success' => true, 'message' => 'Product ' . $request->shopify_product_id . ' order Created ' . count($request->items) . ' items added to order']);


}

public function update(Request $request)
{
    $validate = Validator::make($request->all(), [

        
        'quantity_ordered' => 'required|integer|min:1',
        'supplier' => 'required|string|max:255',
        'paid' => 'boolean',
    ]);

    if ($validate->fails()) {
        return response()->json(['error' => $validate->errors()], 422);
    }

    DB::table('purchase_orders')
        ->where('id', $request->order_id)
        ->update([
            'quantity_ordered' => $request->quantity_ordered,
            'supplier_name' => $request->supplier,
            'paid' => $request->paid,
            'updated_at' => now(),
        ]);

    return response()->json(['success' => true, 'message' => 'Order updated successfully']);
}



public function receive(Request $request)
{

    $validate = Validator::make($request->all(), [
        'items' => 'required|array',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.inventory_item_id' => 'required|integer',
        'location_id' => 'required|integer',
    ]);
    if ($validate->fails()) {
        return response()->json(['error' => $validate->errors()], 422);
    }


    foreach ($request->items as $item):
          

       $order = DB::table('purchase_orders')->where('inventory_item_id', $item['inventory_item_id'])->first();

       if (!$order) {
            return response()->json(['error' => 'Order not found for inventory item ID: ' . $item['inventory_item_id']], 404);
        }

        // Check if the order is already fully received
        if ($order->status === 'received') {
            return response()->json(['error' => 'Order already fully received.'], 422);
        }

        // Ensure the quantity to receive does not exceed what was ordered
        if ($item['quantity'] > ($order->quantity_ordered - $order->quantity_received)) {
            return response()->json(['error' => 'Cannot receive more than ordered.'], 422);
        }

         // Connect inventory item to location if not already connected

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
            ->where('inventory_item_id', $item['inventory_item_id'])
            ->update([
                'quantity_received' => DB::raw("quantity_received + {$item['quantity']}"),

                'status' => $this->calculateStatus($order->id, $item['quantity']),
                'received_at' => now()
            ]);
        
            
        $adjustmentResponse = Http::withHeaders([
            'X-Shopify-Access-Token' => $request->shop['access_token'],
        ])->post("https://{$request->shop['shop_domain']}/admin/api/2024-10/inventory_levels/adjust.json", [
            'location_id' => $request->location_id,          // From step 1
            'inventory_item_id' => $order->inventory_item_id,   
            'available_adjustment' => + $item['quantity']
        ]);

            // Abort if inventory update fails
    if ($adjustmentResponse->failed()) {
        throw new \Exception("Failed to update Shopify inventory.");
    }
endforeach;


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
