<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Helpers\ShopifyInventoryHelper;
use App\Models\Inventory;

class InventoryController extends Controller
{
    protected $shopifyService;

    public function __construct(ShopifyInventoryHelper $shopifyService)
    {
        $this->shopifyService = $shopifyService;
    }

   public function getInventory(Request $request)
    {
        //return response()->json(Inventory::demo());



try {
        $ItemResponse = Http::withHeaders([
            'X-Shopify-Access-Token' => $request->shop['access_token'],
        ])->get("https://{$request->shop['shop_domain']}/admin/api/2024-10/products.json?limit=250");
        
        $data = $ItemResponse->json();
        
        // Extract inventory_item_ids from variants
        $inventoryItemIds = collect($data['products'])->flatMap(function ($product) {
            return collect($product['variants'])->pluck('inventory_item_id');
        })->toArray();
        
        //dump($inventoryItemIds);


        $singleItemResponse = Http::withHeaders([
            'X-Shopify-Access-Token' => $request->shop['access_token'],
        ])->get("https://{$request->shop['shop_domain']}/admin/api/2024-10/inventory_levels.json", [
            'inventory_item_ids' => implode(',', $inventoryItemIds)]);
        
        //dd($singleItemResponse->json());

        return response()->json([
            'data' => $singleItemResponse->json()['inventory_levels'],
            'success' => true
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }

    }

    // public function receiveItems(Request $request)
    // {

    //      $validated = $request->validate([
    //         'shopify_item_id' => 'required|string',
    //         'shopify_location_id' => 'required|string',
    //         'quantity' => 'required|integer|min:1',

    //     ]);


    //     // Update Shopify inventory
    //     $response = ShopifyInventoryHelper::adjustInventory(
    //         $request->shop_domain,
    //         $request->access_token,
    //         $validated['shopify_item_id'],
    //         $validated['shopify_location_id'],
    //         $validated['quantity'],

    //     );

    //     // Record action using Query Builder
    //     DB::table('inventory_actions')->insert([
    //         'shopify_item_id' => $validated['shopify_item_id'],
    //         'shopify_location_id_to' => $validated['shopify_location_id'],
    //         'quantity' => $validated['quantity'],
    //         'action_type' => 'RECEIVE',
    //         'performed_at' => now(),
    //         'created_at' => now(),
    //         'updated_at' => now()
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'data' => $response
    //     ]);
    // }

    public function transferItems(Request $request)
    {
        $validated = $request->validate([
            'inventory_item_id' => 'required|string',
            'from_location_id' => 'required|string',
            'to_location_id' => 'required|string',
            'quantity' => 'required|integer|min:1'
        ]);

       $deductResponse = Http::withHeaders([
            'X-Shopify-Access-Token' => $request->shop['access_token'],
        ])->post("https://{$request->shop['shop_domain']}/admin/api/2024-10/inventory_levels/adjust.json", [
            'location_id' => $request->from_location_id,  // Location to deduct from
            'inventory_item_id' => $request->inventory_item_id, // Item being transferred
            'available_adjustment' => -$request->quantity // Negative value to reduce stock
        ]);

        dump($deductResponse->json());

        $addResponse = Http::withHeaders([
            'X-Shopify-Access-Token' => $request->shop['access_token'],
        ])->post("https://{$request->shop['shop_domain']}/admin/api/2024-10/inventory_levels/adjust.json", [
            'location_id' => $request->from_location_id,  // 
            'inventory_item_id' => $request->inventory_item_id, // Item being transferred
            'available_adjustment' => + $request->quantity // 
        ]);

        dump($addResponse->json());
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
        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $request->shop['access_token'],
        ])->get("https://{$request->shop['shop_domain']}/admin/api/2024-10/locations.json");

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to fetch locations', 'details' => $response->json()], $response->status());
        }

        return response()->json([
            'data' => $response->json()['locations'],
            'success' => true
        ]);
    }
}