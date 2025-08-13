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

public function get_orders($page_info = null) {



    $url = "https://{$this->shop_domain}/admin/api/2024-10/orders.json";
    $limit = 20; // Default limit for pagination
    $fields = 'id,name,customer,created_at,current_total_price,order_status_url,source_name,financial_status,fulfillment_status,note,shipping_lines,line_items';

    $params = [
        'fields' => $fields,
        'limit' => $limit,
    ];

    if ($page_info) {
        // Only send page_info — no filters allowed
        $params['page_info'] = $page_info;
    } else {
        // First request — include filters
        $params['status'] = 'any';
        $params['financial_status'] = 'any';
        $params['fulfillment_status'] = 'any';
    }

   $response = Http::withHeaders([
        'X-Shopify-Access-Token' => $this->access_token,
    ])->get($url, $params);

    if ($response->failed()) {
        throw new \Exception('Failed to fetch orders');
    }
    
    
    $orders = $response['orders'];
    $linkHeader = $response->header('Link');
    $nextPageInfo = null;
    
    if ($linkHeader && preg_match('/<[^>]+page_info=([^&>]+)[^>]*>; rel="next"/', $linkHeader, $matches)) {
        $nextPageInfo = $matches[1];
    }

    return [
        'orders' => $orders,
        'next_page_info' => $nextPageInfo,
    ];

}
        public function set_order_status($orderId, $status, $key = 'status', $namespace = 'custom_status', $type = 'single_line_text_field')

{
    $response = Http::withHeaders([
        'X-Shopify-Access-Token' => $this->access_token,
    ])->post("https://{$this->shop_domain}/admin/api/2024-04/orders/{$orderId}/metafields.json", [
        'metafield' => [
            'namespace' => $namespace,
            'key' => $key,
            'value' => $status->id,
            'type' => $type,
        ]
    ]);

    if ($response->failed()) {
        throw new \Exception("Failed to set metafield for order ID {$orderId}");
    }

        $updateResponse = Http::withHeaders([
        'X-Shopify-Access-Token' => $this->access_token,
    ])->put("https://{$this->shop_domain}/admin/api/2024-04/orders/{$orderId}.json", [
        'order' => [
            'id' => $orderId,
            'tags' => $status->name
        ]
    ]);

    if ($updateResponse->failed()) {
        throw new \Exception("Failed to update tag for order {$orderId}");
    }

    return $response->json();
}

     public function orders_count(){
                $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $this->access_token,
        ])->get("https://{$this->shop_domain}/admin/api/2024-10/orders/count.json", [
            'status' => 'any'
        ]);
        
        return $response['count'];
}
    
        
}
