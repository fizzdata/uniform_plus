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

        $request->validate(['shop' => 'required|string']);
        $shop = $request->input('shop');

        $storedShop = DB::table('shops')->where('shop_domain', $shop)->first();
        if (!$storedShop) {
            return response()->json(['error' => 'Shop not found']);
        }




$graphqlQuery = <<<GRAPHQL
query {
  inventoryItems(first: 250) {
    edges {
      node {
        id
        sku
        variant {
          product {
            title
          }
          title
        }
        inventoryLevels(first: 250) {
          edges {
            node {
              available
              location {
                name
              }
            }
          }
        }
      }
    }
  }
}
GRAPHQL;

try {
        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $storedShop->access_token,
            'Content-Type' => 'application/json',
        ])->post("https://{$shop}/admin/api/2024-10/graphql.json", [
            'query' => $graphqlQuery
        ]);

        if ($response->failed()) {
            return response()->json([
                'error' => 'Shopify API request failed',
                'details' => $response->body()
            ], 500);
        }

        $data = $response->json();

        // Transform nested GraphQL response to clean JSON
        $formatted = collect($data['data']['inventoryItems']['edges'])
            ->map(function ($itemEdge) {
                $item = $itemEdge['node'];
                return [
                    'sku' => $item['sku'],
                    'product_title' => $item['variant']['product']['title'] ?? 'N/A',
                    'variant_title' => $item['variant']['title'] ?? 'Default',
                    'stock' => collect($item['inventoryLevels']['edges'])
                        ->map(fn($levelEdge) => [
                            'location' => $levelEdge['node']['location']['name'],
                            'quantity' => $levelEdge['node']['available']
                        ])
                ];
            });

        return response()->json([
            'data' => $formatted,
            'success' => true
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }

    }

    public function receiveItems(Request $request)
    {

         $validated = $request->validate([
            'shopify_item_id' => 'required|string',
            'shopify_location_id' => 'required|string',
            'quantity' => 'required|integer|min:1',

        ]);

        $request->validate(['shop' => 'required|string']);
        $shop = $request->input('shop');


        $storedShop = DB::table('shops')->where('shop_domain', $shop)->first();
        if (!$storedShop) {
            return response()->json(['error' => 'Shop not found'], 404);
        }
        
       

        // Update Shopify inventory
        $response = ShopifyInventoryHelper::adjustInventory(
            $storedShop->shop_domain,
            $storedShop->access_token,
            $validated['shopify_item_id'],
            $validated['shopify_location_id'],
            $validated['quantity'],

        );

        // Record action using Query Builder
        DB::table('inventory_actions')->insert([
            'shopify_item_id' => $validated['shopify_item_id'],
            'shopify_location_id_to' => $validated['shopify_location_id'],
            'quantity' => $validated['quantity'],
            'action_type' => 'RECEIVE',
            'performed_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'data' => $response
        ]);
    }

    public function transferItems(Request $request)
    {
        $validated = $request->validate([
            'shopify_item_id' => 'required|string',
            'from_location_id' => 'required|string',
            'to_location_id' => 'required|string',
            'quantity' => 'required|integer|min:1'
        ]);

        $request->validate(['shop' => 'required|string']);
        $shop = $request->input('shop');


        $storedShop = DB::table('shops')->where('shop_domain', $shop)->first();
        if (!$storedShop) {
            return response()->json(['error' => 'Shop not found'], 404);
        }

        // Execute transfer through Shopify API
        $response = ShopifyInventoryHelper::moveInventory(
            $storedShop->shop_domain,
            $storedShop->access_token,
            $validated['shopify_item_id'],
            $validated['from_location_id'],
            $validated['to_location_id'],
            $validated['quantity'],
        );

        // Log transfer action
        DB::table('inventory_actions')->insert([
            'shopify_item_id' => $validated['shopify_item_id'],
            'shopify_location_id_from' => $validated['from_location_id'],
            'shopify_location_id_to' => $validated['to_location_id'],
            'quantity' => $validated['quantity'],
            'action_type' => 'TRANSFER',
            'performed_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'data' => $response
        ]);
    }
}