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

     // Fetch local order statuses
$orderStatuses = DB::table('order_status')
    ->join('statuses', 'order_status.status_id', '=', 'statuses.id')
    ->select('order_status.order_id', 'statuses.name as status_name', 'order_status.changed_at')
    ->orderBy('order_status.changed_at', 'desc')
    ->get()
    ->groupBy('order_id');
// Build formatted response
$formattedOrders = collect($response['orders'])->map(function ($order) use ($orderStatuses) {
    $shopifyOrderId = $order['id'];
    $latestStatus = $orderStatuses[$shopifyOrderId]->last() ?? null;

    return [
        'id' => $shopifyOrderId,
        'customer_name' => "{$order['customer']['first_name']} {$order['customer']['last_name']}",
        'status' => $latestStatus ? $latestStatus->status->name : 'Not Tracked',
        'amount' => $order['total_price'],
        'link' => url("/orders/{$shopifyOrderId}/edit"),
    ];
})->toArray();   

    return response()->json($formattedOrders);
    }
}
