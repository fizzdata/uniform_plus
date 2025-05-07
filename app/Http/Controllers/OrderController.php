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
        // Get Shopify orders
        $client = new Rest(
            $request->session()->get('shop'),
            $request->session()->get('access_token')
        );
        
        $response = $client->get('orders');
        $orders = $response->getDecodedBody();
        
        // Get stages for all orders in one query
        $orderIds = collect($orders['orders'])->pluck('id')->toArray();
        
        $stageMappings = DB::table('order_stage_mappings')
            ->join('order_stages', 'order_stage_mappings.order_stage_id', '=', 'order_stages.id')
            ->whereIn('order_stage_mappings.shopify_order_id', $orderIds)
            ->select(
                'order_stage_mappings.shopify_order_id', 
                'order_stages.id as stage_id', 
                'order_stages.name as stage_name',
                'order_stages.color as stage_color',
                'order_stage_mappings.notes',
                'order_stage_mappings.entered_at'
            )
            ->get()
            ->keyBy('shopify_order_id');
        
        // Merge stage data with Shopify orders
        foreach ($orders['orders'] as &$order) {
            $stageData = $stageMappings->get($order['id']);
            
            if ($stageData) {
                $order['stage'] = (object) [
                    'id' => $stageData->stage_id,
                    'name' => $stageData->stage_name,
                    'color' => $stageData->stage_color,
                    'notes' => $stageData->notes,
                    'entered_at' => $stageData->entered_at
                ];
            } else {
                $order['stage'] = null;
            }
        }
        
        // Get all stages for the stage selector
        $stages = DB::table('order_stages')
            ->orderBy('position')
            ->get();
        
        return view('orders.index', compact('orders', 'stages'));
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
