<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Shopify;
use App\Models\Status;
use Illuminate\Support\Facades\Http;


class OrdersController extends Controller
{
     public function index(Request $request){

       $shop = new Shopify($request->shop['id']); // Use the shop ID from the request

    // Fetch orders from Shopify
    $orders = $shop->get_orders(); 



    //$orders = Orders::demo();


    if (empty($orders)) :
        return response()->json(['message' => 'No orders found'], 200);
    endif;


     // Step 2: Fetch latest status for each order using Query Builder
    $orderStatuses = DB::table('orders')
    ->join('statuses', 'orders.status_id', '=', 'statuses.id')
    ->select('orders.shopify_order_id', 'orders.status_id')
        ->get()
        ->pluck('status_id', 'shopify_order_id') // Creates associative array
        ->toArray();
    // Step 3: Match orders with status & format response
    $formattedOrders = [];

    foreach ($orders as $order):
        $OrderId = (string) $order['name'];
        $shopifyOrderId = (string) $order['id'];
         // Ensure ID format consistency

        $customerName = isset($order['customer']) ? "{$order['customer']['first_name']} {$order['customer']['last_name']}" : 'Guest Checkout';

        // Check if the order is NOT in the existing statuses list
    if (!array_key_exists($shopifyOrderId, $orderStatuses)):
        $Status_id = Status::assign($order); // Assign new status
    else:
        $Status_id = $orderStatuses[$shopifyOrderId]; // Get existing status
    endif;

    
        //get next order status

    $nextStatus = DB::table('order_status_transitions')
        ->select('to_status')
        ->where('from_status', $Status_id)
        ->get();


        $formattedOrders[] = [
            'id' => $OrderId,
            'shopify_order_id' => $shopifyOrderId,
            'customer_name' => $customerName,
            'created_at' => $order['created_at'],
            'status_id' => $Status_id,
            'next_status' => $nextStatus,
            'amount' => $order['current_total_price'],
            'link' => $order['order_status_url'],
        ];
    endforeach;

    return response()->json(['orders' => $formattedOrders, 'next_page_info' => $orders['next_page_info'] ?? null], 200);

    }
}
