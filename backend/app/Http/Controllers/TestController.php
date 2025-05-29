<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;


class TestController extends Controller
{
    public function index(Request $request){
        
        $shop = 'test-shop.myshopify.com';

         $storedShop = DB::table('shops')->where('shop_domain', $shop)->first();

            if (!$storedShop) {
                return response()->json(['success' => false, 'error' => 'Shop not found']);
            }

            // Convert to an array before merging
            $request->merge(['shop' => (array) $storedShop]);


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



    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }

    }
}
