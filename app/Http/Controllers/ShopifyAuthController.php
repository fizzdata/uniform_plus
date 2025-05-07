<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShopifyAuthController extends Controller
{
    public function __construct()
    {
        // Initialize Shopify Context
        Context::initialize(
            apiKey: config('shopify.api_key'),
            apiSecretKey: config('shopify.api_secret'),
            scopes: config('shopify.scopes'),
            hostName: parse_url(config('shopify.redirect_uri'), PHP_URL_HOST),
            isEmbedded: true
        );
    }
    
    public function begin(Request $request)
    {
        $shop = $request->query('shop');
        
        if (!$shop) {
            return response('Shop parameter is required', 400);
        }
        
        // Check if we already have an access token
        if ($request->session()->has('access_token') && $request->session()->get('shop') === $shop) {
            return redirect()->route('home');
        }
        
        // Build auth URL
        $authUrl = OAuth::beginAuth(
            shop: $shop,
            redirectPath: route('shopify.callback'),
            isOnline: false
        );
        
        return redirect($authUrl);
    }
    
    public function callback(Request $request)
    {
        $session = OAuth::callback(
            request: $request->query(),
            isOnline: false
        );
        
        // Store session data in Laravel session
        $request->session()->put('access_token', $session->getAccessToken());
        $request->session()->put('shop', $session->getShop());
        
        // Optional: Store shop info in database for persistence
        $this->storeShopInfo($session->getShop(), $session->getAccessToken());
        
        // Register webhooks (optional but recommended)
        $this->registerWebhooks($session->getShop(), $session->getAccessToken());
        
        return redirect()->route('home');
    }
    
    private function registerWebhooks($shop, $accessToken)
    {
        $client = new Rest($shop, $accessToken);
        
        // Register for order creation webhook
        $client->post(
            'webhooks',
            [
                'webhook' => [
                    'topic' => 'orders/create',
                    'address' => route('webhooks.orders.create'),
                    'format' => 'json'
                ]
            ]
        );
        
        // Register for order update webhook
        $client->post(
            'webhooks',
            [
                'webhook' => [
                    'topic' => 'orders/updated',
                    'address' => route('webhooks.orders.update'),
                    'format' => 'json'
                ]
            ]
        );
    }
    
    private function storeShopInfo($shop, $accessToken)
    {
        // Check if shop already exists in the database
        $shopExists = DB::table('shops')
            ->where('shop_domain', $shop)
            ->exists();
            
        if ($shopExists) {
            // Update existing shop record
            DB::table('shops')
                ->where('shop_domain', $shop)
                ->update([
                    'access_token' => $accessToken,
                    'updated_at' => now(),
                ]);
        } else {
            // Create new shop record
            DB::table('shops')
                ->insert([
                    'shop_domain' => $shop,
                    'access_token' => $accessToken,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        }
    }
}
