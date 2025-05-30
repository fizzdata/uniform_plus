<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Shop;

class ShopifyAuthController extends Controller
{
    protected $shopifyApiKey;
    protected $shopifyApiSecret;
    protected $redirectUri;
    protected $scopes;

    public function __construct()
    {
        $this->shopifyApiKey = env('SHOPIFY_API_KEY');
        $this->shopifyApiSecret = env('SHOPIFY_API_SECRET');
        $this->redirectUri = env('SHOPIFY_REDIRECT_URI');

        $this->scopes = 'read_orders,
        write_orders,
        read_inventory,
        write_inventory,
        read_products,
        write_products,
        read_locations,
        write_locations,
        unauthenticated_read_product_listings '; 
        
        // Add necessary scopes
    }

    public function redirectToShopify(Request $request)
    {
        $shop = $request->input('shop');
        $installUrl = "https://{$shop}/admin/oauth/authorize?" . http_build_query([
            'client_id' => $this->shopifyApiKey,
            'scope' => $this->scopes,
            'redirect_uri' => $this->redirectUri,
            'state' => csrf_token(),
        ]);

        return redirect()->away($installUrl);
    }

    public function handleCallback(Request $request)
    {
        $shop = $request->input('shop');
        $code = $request->input('code');

        // Exchange code for access token
        $response = Http::post("https://{$shop}/admin/oauth/access_token", [
            'client_id' => $this->shopifyApiKey,
            'client_secret' => $this->shopifyApiSecret,
            'code' => $code,
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to retrieve access token'], 400);
        }

        $accessToken = $response->json()['access_token'];

        // Store access token in database (assuming you have a 'shops' table)
        Shop::updateOrCreate(
            ['shop_domain' => $shop],
            ['access_token' => $accessToken]
        );

        return response()->json(['message' => 'App installed successfully', 'shop' => $shop], 200);
    }
}