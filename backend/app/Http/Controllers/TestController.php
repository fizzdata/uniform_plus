<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Models\Shopify;


class TestController extends Controller
{
     public function index(Request $request){

      
    $shop = new Shopify(1); // Assuming shop_id is 1 for testing
    try {
            $products = $shop->get_products();

            $inventoryItemIds = collect($products)->flatMap(function ($product) {
            return collect($product['variants'])->pluck('inventory_item_id');
            })->toArray();
            
            $inventory_levels = $shop->get_inventory_levels($inventoryItemIds);
            $locations = $shop->get_locations();

            //$adjust = $shop->adjust_inventory_level($inventoryItemIds[0], $locations[0]['id'],20);


            return response()->json([
                'products' => $products,
                'inventory_levels' => $inventory_levels,
                'locations' => $locations
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }
}
