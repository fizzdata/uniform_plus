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

    $paid = ($request->paid === 'true' || $request->paid === true) ? true : false;

    DB::table('purchase_orders')
        ->where('id', $request->order_id)
        ->update([
            'quantity_ordered' => $request->quantity_ordered,
            'supplier_name' => $request->supplier,
            'paid' =>  $paid,
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

         $shop = new \App\Models\Shopify($request->shop['id']);

       if($order->quantity_received < 1):
          $connect = $shop->connect($request->location_id, $order->inventory_item_id);
        endif;

        DB::table('purchase_orders')
            ->where('inventory_item_id', $item['inventory_item_id'])
            ->update([
                'quantity_received' => DB::raw("quantity_received + {$item['quantity']}"),

                'status' => $this->calculateStatus($order->id, $item['quantity']),
                'received_at' => now()
            ]);
        
        $adjust = $shop->adjust_inventory_level($order->inventory_item_id, $request->location_id, $item['quantity']);   
        
        DB::table('inventory_actions')
        ->insert([
            'shop_id' => $request->shop['id'],
            'inventory_item_id' => $order->inventory_item_id,
            'shopify_location_id_from' => null,
            'shopify_location_id_to' => $request->location_id,
            'quantity' => $item['quantity'],
            'action_type' => 'Add',
            'performed_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
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

public function undo_received(Request $request){
    // this will reset the received to 0 and revert inventory

    

    $order = DB::table('purchase_orders')
        ->where('id', $request->order_id)
        ->first();

    if (!$order) {
        return response()->json(['error' => 'Order not found'], 404);
    }


    //i need to check if there is more then one location used for this inventory item
    $find_used_location = DB::table('inventory_actions')
    ->where('inventory_item_id', $order->inventory_item_id)
    ->where('shop_id', $request->shop['id'])
    ->distinct('shopify_location_id_to')
    ->pluck('shopify_location_id_to');

    if ($find_used_location->isEmpty()) {
        return response()->json(['error' => 'No inventory actions found for this order'], 404);
    }

    if ($find_used_location->count() > 1) {
        return response()->json(['error' => 'NOT SURE TO WHICH LOCATION IT NEEDS TO GO BACK'], 400);
    }

    if ($order->quantity_received < 1) {
        return response()->json(['error' => 'Nothing to undo for this order'], 400);
    }


    $shop = new \App\Models\Shopify($request->shop['id']);

    try {
    $adjust = $shop->adjust_inventory_level($order->inventory_item_id, $find_used_location[0], -$order->quantity_received);   
      
  

        DB::table('inventory_actions')
        ->insert([
            'shop_id' => $request->shop['id'],
            'inventory_item_id' => $order->inventory_item_id,
            'shopify_location_id_from' => $find_used_location[0],
            'shopify_location_id_to' => null,
            'quantity' => $order->quantity_received,
            'action_type' => 'Adjust',
            'performed_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        //reset the received quantity to 0
    DB::table('purchase_orders')
        ->where('id', $request->order_id)
        ->update([
            'quantity_received' => 0,
            'status' => 'pending',
            'received_at' => null,
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Order received quantity reset successfully']);
        
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to adjust inventory: ' . $e->getMessage()], 500);
    }

        
}

}
