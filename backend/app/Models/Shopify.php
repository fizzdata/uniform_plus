<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;

class Shopify extends Model
{
    function __construct($shop_id){
        $shop = DB::table('shops')->where('id', $shop_id)->first();

        $this->shop_domain = $shop->shop_domain;
        $this->access_token = $shop->access_token;
    }
    
    public function get_products($fields = 'id,title,variants', $limit = 250){ 
         $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $this->access_token,
        ])->get("https://{$this->shop_domain}/admin/api/2024-10/products.json", [
            'fields' => $fields,
            'limit' => $limit
        ]);
        if ($response->failed()):
            throw new \Exception("Failed to get Shopify products");
        endif;
        
        return $response->json()['products'];
    }

    public function get_inventory_levels($inventory_item_ids = [], $limit = 250) {
    $params = ['limit' => $limit];
    $inventory_levels = [];

    $chunks = array_chunk($inventory_item_ids, 100); // Shopify might handle smaller batches better

    foreach ($chunks as $chunk) {
        $params['inventory_item_ids'] = implode(',', $chunk);

        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $this->access_token,
        ])->get("https://{$this->shop_domain}/admin/api/2024-10/inventory_levels.json", $params);

        if ($response->failed()) {
            throw new \Exception("Failed to get Shopify inventory levels");
        }

        $inventory_levels = array_merge($inventory_levels, $response->json()['inventory_levels']);
    }

    return $inventory_levels;
}

    public function get_locations($fields = 'id,name', $limit = 250){
        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $this->access_token,
        ])->get("https://{$this->shop_domain}/admin/api/2024-10/locations.json", [
            'fields' => $fields,
            'limit' => $limit
        ]);
        if ($response->failed()):
            throw new \Exception("Failed to get Shopify locations");
        endif;

        return $response->json()['locations'];
    }

    public function adjust_inventory_level($inventory_item_id, $location_id, $available_adjustment){
        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $this->access_token,
        ])->post("https://{$this->shop_domain}/admin/api/2024-10/inventory_levels/adjust.json", [
            'inventory_item_id' => $inventory_item_id,
            'location_id' => $location_id,
            'available_adjustment' => $available_adjustment
        ]);
        
        if ($response->failed()):
            throw new \Exception("Failed to adjust inventory level");
        endif;

        return $response->json();
    }

    public function connect($location_id, $inventory_item_id){
           $connect = Http::withHeaders([
                        'X-Shopify-Access-Token' => $this->access_token,
                    ])->post("https://{$this->shop_domain}/admin/api/2024-10/inventory_levels/connect.json", [
                        'location_id' => $location_id,          // From step 1
                        'inventory_item_id' => $inventory_item_id,   
                    ]); 
                    // Abort if connection fails
        if ($connect->failed()):
            throw new \Exception("Failed to connect inventory item to location.");
        endif;

        return $connect->json();
    }

    public function get_orders($status = 'any', $fields = 'id,name,customer,created_at,current_total_price', $limit = 250){
        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $this->access_token,
        ])->get("https://{$this->shop_domain}/admin/api/2024-10/orders.json", [
            'status' => $status,
            'fields' => $fields,
            'limit' => $limit
        ]);
        
        if ($response->failed()):
            throw new \Exception("Failed to get Shopify orders");
        endif;

        return $response->json()['orders'];
    }
    
        
}
