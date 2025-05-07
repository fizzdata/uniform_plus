<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ShopifyAuthController extends Controller
{
    protected $apiKey;
    protected $apiSecret;
    protected $scopes;
    protected $redirectUri;
    
    public function __construct()
    {
        $this->apiKey = env('SHOPIFY_API_KEY');
        $this->apiSecret = env('SHOPIFY_API_SECRET');
        $this->scopes = env('SHOPIFY_SCOPES', 'read_orders,write_orders');
        $this->redirectUri = env('SHOPIFY_REDIRECT_URI');
    }
    
    public function installShop(Request $request)
    {
        $shop = $request->get('shop');
        
        if (!$shop) {
            return view('shopify.install');
        }
        
        // Validate the shop domain
        if (!$this->isValidShopifyDomain($shop)) {
            return redirect()->back()->with('error', 'Invalid Shopify shop domain');
        }
        
        // Generate install URL
        $installUrl = "https://{$shop}/admin/oauth/authorize?"
            . "client_id={$this->apiKey}"
            . "&scope={$this->scopes}"
            . "&redirect_uri={$this->redirectUri}";
        
        return redirect($installUrl);
    }
    
    public function handleCallback(Request $request)
    {
        $shop = $request->get('shop');
        $code = $request->get('code');
        
        if (!$shop || !$code) {
            return redirect()->route('shopify.install')->with('error', 'Missing required parameters');
        }
        
        try {
            // Exchange temporary code for a permanent access token
            $response = Http::post("https://{$shop}/admin/oauth/access_token", [
                'client_id' => $this->apiKey,
                'client_secret' => $this->apiSecret,
                'code' => $code
            ]);
            
            if (!$response->successful()) {
                return redirect()->route('shopify.install')->with('error', 'Failed to authenticate with Shopify');
            }
            
            $data = $response->json();
            
            // Store the access token and shop domain in session
            session([
                'shopify_token' => $data['access_token'],
                'shopify_domain' => $shop
            ]);
            
            // You could also store this in your database for a more permanent solution
            
            return redirect()->route('orders.index')->with('success', 'Successfully connected to your Shopify store');
            
        } catch (\Exception $e) {
            return redirect()->route('shopify.install')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    
    private function isValidShopifyDomain($shop)
    {
        return preg_match('/^[a-zA-Z0-9][a-zA-Z0-9\-]*\.myshopify\.com$/', $shop);
    }
}