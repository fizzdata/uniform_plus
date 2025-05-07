<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Shopify\Clients\Rest;


class WebhookController extends Controller
{
    public function handleOrderCreate(Request $request)
    {
        // Get the order data from the webhook
        $orderData = $request->all();
        
        // Get the first stage by position
        $firstStage = DB::table('order_stages')
            ->orderBy('position')
            ->first();
            
        if ($firstStage) {
            // Insert new order mapping
            DB::table('order_stage_mappings')
                ->insert([
                    'order_stage_id' => $firstStage->id,
                    'shopify_order_id' => $orderData['id'],
                    'entered_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        }
        
        return response()->json(['success' => true]);
    }
    
    public function handleOrderUpdate(Request $request)
    {
        // Log the order update or perform any necessary actions
        $orderData = $request->all();
        
        // Optional: log order updates for monitoring
        DB::table('webhook_logs')->insert([
            'event' => 'order_update',
            'payload' => json_encode($orderData),
            'created_at' => now(),
        ]);
        
        return response()->json(['success' => true]);
    }
}
