<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Shopify\Clients\Rest;


class OrderController extends Controller
{
    public function updateStage(Request $request, $orderId)
    {
        $validated = $request->validate([
            'stage_id' => 'required|exists:order_stages,id',
            'notes' => 'nullable|string',
        ]);
        
        // Check if mapping exists
        $existingMapping = DB::table('order_stage_mappings')
            ->where('shopify_order_id', $orderId)
            ->first();
            
        if ($existingMapping) {
            // Update existing mapping
            DB::table('order_stage_mappings')
                ->where('shopify_order_id', $orderId)
                ->update([
                    'order_stage_id' => $validated['stage_id'],
                    'entered_at' => now(),
                    'notes' => $validated['notes'],
                    'updated_at' => now(),
                ]);
        } else {
            // Create new mapping
            DB::table('order_stage_mappings')
                ->insert([
                    'order_stage_id' => $validated['stage_id'],
                    'shopify_order_id' => $orderId,
                    'entered_at' => now(),
                    'notes' => $validated['notes'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        }
        
        // Update Shopify order tags or metafields
        $stageName = DB::table('order_stages')
            ->where('id', $validated['stage_id'])
            ->value('name');
            
        $client = new Rest(
            $request->session()->get('shop'),
            $request->session()->get('access_token')
        );
        
        $client->put(
            path: "orders/{$orderId}",
            body: [
                'order' => [
                    'id' => $orderId,
                    'tags' => "stage:{$stageName}", // Add stage as a tag
                ]
            ]
        );
        
        return response()->json(['success' => true]);
    }
    
   public function getOrders(Request $request)
{
    $shopDomain = $request->session()->get('shopify_domain');
    $accessToken = $request->session()->get('shopify_token');


    try {
        // Initialize client with explicit HTTP client
        $client = new Rest(
            $shopDomain,
            $accessToken,
            [
                'api_version' => '2024-04',
                'http_client' => new \GuzzleHttp\Client()
            ]
        );

       
                 dump($client);

        $response = $client->get(
            'orders/',
            [
                'query' => ['fields' => 'id,title']
            ]
        );
        

        // First verify API version compatibility
        $shopResponse = $client->get('shop');
        $shopData = $shopResponse->getDecodedBody();
        
        if (version_compare($shopData['shop']['api_version'], '2024-04', '<')) {
            throw new \Exception("Store API version {$shopData['shop']['api_version']} is too low");
        }

        // Get orders with pagination
        $response = $client->get('orders', [
            'query' => [
                'status' => 'any',
                'limit' => 250
            ]
        ]);

        // Debug full response
        logger()->debug('Shopify Orders Response', [
            'status' => $response->getStatusCode(),
            'headers' => $response->getHeaders(),
            'body' => $ordersData = $response->getDecodedBody()
        ]);

        $shopifyOrders = $ordersData['orders'] ?? [];

        // ... rest of your existing code ...

    } catch (\Exception $e) {
        logger()->error("Order Fetch Error: {$e->getMessage()}", [
            'exception' => $e
        ]);
        return back()->withErrors($e->getMessage());
    }
}
    
    public function getOrdersWithStage(Request $request, $stageId)
    {
        // Get all orders assigned to a specific stage
        $client = new Rest(
            $request->session()->get('shop'),
            $request->session()->get('access_token')
        );
        
        $response = $client->get('orders');
        $allOrders = $response->getDecodedBody();
        
        // Get order IDs for the specified stage
        $stageOrderIds = DB::table('order_stage_mappings')
            ->where('order_stage_id', $stageId)
            ->pluck('shopify_order_id')
            ->toArray();
            
        // Filter orders
        $filteredOrders = array_filter($allOrders['orders'], function($order) use ($stageOrderIds) {
            return in_array($order['id'], $stageOrderIds);
        });
        
        $orders = ['orders' => $filteredOrders];
        
        // Get stage details
        $stage = DB::table('order_stages')
            ->where('id', $stageId)
            ->first();
            
        // Get all stages for the stage selector
        $stages = DB::table('order_stages')
            ->orderBy('position')
            ->get();
            
        return view('orders.by_stage', compact('orders', 'stage', 'stages'));
    }
}
