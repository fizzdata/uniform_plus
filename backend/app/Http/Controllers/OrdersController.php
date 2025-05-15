<?php

namespace App\Http\Controllers;

use App\Models\Orders;
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


    //$orders = Orders::demo();


    if (empty($orders)) :
        return response()->json(['message' => 'No orders found'], 200);
    endif;

     // Step 2: Fetch latest status for each order using Query Builder
$orderStatuses = DB::table('orders')
    ->join('statuses', 'orders.status_id', '=', 'statuses.id')
    ->select('orders.shopify_order_id', 'orders.status_id')
    ->pluck('orders.status_id', 'orders.shopify_order_id'); // Creates associative array

    // Step 3: Match orders with status & format response
    $formattedOrders = [];

    foreach ($orders as $order):
        $shopifyOrderId = (string) $order['id'];
         // Ensure ID format consistency
           $latestStatus = (int) data_get($orderStatuses, $shopifyOrderId, 1); // Default status if missing

        $customerName = isset($order['customer']) ? "{$order['customer']['first_name']} {$order['customer']['last_name']}" : 'Guest Checkout';

        $formattedOrders[] = [
            'id' => $shopifyOrderId,
            'customer_name' => $customerName,
            'created_at' => $order['created_at'],
            'status_id' => $latestStatus,
            'amount' => $order['current_total_price'],
            'link' => $order['order_status_url'],
        ];
    endforeach;

    return response()->json(['orders' => $formattedOrders]);

    }
}
