<?php

namespace App\Http\Controllers;

use App\Models\Shopify;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class InventoryController extends Controller
{
   
   public function getInventory(Request $request)
    {
        //return response()->json(Inventory::demo());



try {
        $shop = new Shopify($request->shop['id']); // Use the shop ID from the request

        $products = $shop->get_products();
        
        // Extract inventory_item_ids from variants
        $inventoryItemIds = collect($products)->flatMap(function ($product) {
            return collect($product['variants'])->pluck('inventory_item_id');
        })->toArray();
        
        //dump($inventoryItemIds);


       $inventory_levels = $shop->get_inventory_levels($inventoryItemIds);
        
        //dd($singleItemResponse->json());

        return response()->json([
            'data' => $inventory_levels,
            'success' => true
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }

    }

 
    public function transferItems(Request $request)
    {
        $validated = $request->validate([
            'inventory_item_id' => 'required|string',
            'from_location_id' => 'required|string',
            'to_location_id' => 'required|string',
            'quantity' => 'required|integer|min:1'
        ]);

        $shop = new Shopify($request->shop['id']);

       $deductResponse = $shop->adjust_inventory_level($request->inventory_item_id, $request->to_location_id, - $request->quantity);

        //dump($deductResponse->json());

        $addResponse =  $shop->adjust_inventory_level($request->inventory_item_id, $request->from_location_id, + $request->quantity);

        //dump($addResponse->json());
        // Log transfer action
        DB::table('inventory_actions')->insert([
            'shop_id' => $request->shop['id'],
            'inventory_item_id' => $request->inventory_item_id,
            'shopify_location_id_from' => $request->from_location_id,
            'shopify_location_id_to' => $request->to_location_id,
            'quantity' => $request->quantity,
            'action_type' => 'TRANSFER',
            'performed_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Items transferred successfully',
        ]);
    }

    public function get_locations(Request $request)
    {
        // Fetch locations from Shopify

        $shop = new Shopify($request->shop['id']);

        $locations = $shop->get_locations();
       

        return response()->json([
            'data' => $locations,
            'success' => true
        ]);
    }

    public function exchange(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'item_from' => 'required|integer',
            'item_to' => 'required|integer',
            'quantity' => 'required|integer|min:1'
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }
        try{

        $shop = new Shopify($request->shop['id']);

        // Adjust inventory levels
        $shop->adjust_inventory_level($request->item_from, $request->location_id, -$request->quantity);
        $shop->adjust_inventory_level($request->item_to, $request->location_id, $request->quantity);

        // Log the exchange action
        DB::table('inventory_actions')->insert([
            'shop_id' => $request->shop['id'],
            'inventory_item_id' => $request->item_from,
            'shopify_location_id_from' => null,
            'shopify_location_id_to' => $request->location_id,
            'quantity' => -$request->quantity,
            'action_type' => 'Adjust',
            'performed_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('inventory_actions')->insert([
            'shop_id' => $request->shop['id'],
            'inventory_item_id' => $request->item_to,
            'shopify_location_id_from' => null,
            'shopify_location_id_to' => $request->location_id,
            'quantity' => $request->quantity,
            'action_type' => 'Adjust',
            'performed_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Items exchanged successfully']);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
    }
}