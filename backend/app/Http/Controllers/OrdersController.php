<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;


class OrdersController extends Controller
{
    public function index(Request $request){
    
    
    $request->validate(['shop' => 'required|string']);
    $shop = $request->input('shop');


    $storedShop = DB::table('shops')->where('shop_domain', $shop)->first();
    if (!$storedShop) {
        return response()->json(['error' => 'Shop not found'], 404);
    }

    // Fetch orders from Shopify
    $response = Http::withHeaders([
        'X-Shopify-Access-Token' => $storedShop->access_token,
    ])->get("https://{$shop}/admin/api/2024-10/orders.json?status=any");

        if ($response->failed()) {
                Log::error('Shopify API error', [
                    'shop' => $shop,
                    'status' => $response->status(),
                    'response' => $response->json(),
    ]);
            return response()->json(['error' => 'Failed to fetch orders', 'details' => $response->json()], $response->status());
        } 

    $orders = $response->json()['orders'];

    if (empty($orders)) :
        return response()->json(['message' => 'No orders found'], 200);
    endif;

     // Step 2: Fetch latest status for each order using Query Builder
    $orderStatuses = DB::table('order_status')
        ->join('statuses', 'order_status.status_id', '=', 'statuses.id')
        ->select('order_status.order_id', 'statuses.name as status_name')
        ->orderBy('order_status.changed_at', 'desc')
        ->get()
        ->groupBy('order_id');

    // Step 3: Match orders with status & format response
    $formattedOrders = [];

    foreach ($orders as $order):
        $shopifyOrderId = (string) $order['id'];
         // Ensure ID format consistency
        $latestStatus = isset($orderStatuses[$shopifyOrderId]) 
            ? $orderStatuses[$shopifyOrderId]->first()->status_name 
            : 'Not Tracked'; // Default if no status found


        $customerName = isset($order['customer']) ? "{$order['customer']['first_name']} {$order['customer']['last_name']}" : 'Guest Checkout';

        $formattedOrders[] = [
            'id' => $shopifyOrderId,
            'customer_name' => $customerName,
            'status' => $latestStatus,
            'amount' => $order['total_price'],
            'link' => url("/orders/{$shopifyOrderId}/edit"),
        ];
    endforeach;

    return response()->json($formattedOrders);

    }
}
