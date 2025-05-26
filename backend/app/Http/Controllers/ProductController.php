<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20);

        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $request->access_token,
        ])->get("https://{$request->shop}/admin/api/2024-10/products.json", [
            'fields' => 'id,title,variants',
            'limit' => 250
        ]);
        if ($response->failed()) {
            return response()->json(['error' => 'Failed to fetch products', 'details' => $response->json()], $response->status());
        }
        
        return response()->json([
            'data' => $response->json()['products'],
            'total' => count($response->json()['products']),
            'current_page' => 1,
            'per_page' => $perPage,
        ]);
    }
}
