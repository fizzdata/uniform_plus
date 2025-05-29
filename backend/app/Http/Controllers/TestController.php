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
            $inventory_levels = $shop->get_inventory_levels();
            $locations = $shop->get_locations();

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
