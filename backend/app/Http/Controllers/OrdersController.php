<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;


class OrdersController extends Controller
{
    public function index(Request $request){
    
    
    $shop = $request->query('shop');

    if (!$shop) {
        return response()->json(['error' => 'Shop parameter is required'], 400);
    }

    $storedShop = DB::table('shops')->where('shop_domain', $shop)->first();
    if (!$storedShop) {
        return response()->json(['error' => 'Shop not found'], 404);
    }

    // Fetch orders from Shopify
    $response = Http::withHeaders([
        'X-Shopify-Access-Token' => $storedShop->access_token,
    ])->get("https://{$shop}/admin/api/2024-10/orders.json");

    return $response->json();
    }
}
