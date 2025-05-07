<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class EnsureShopifyAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        // Check for session-based authentication first
        if ($request->session()->has('access_token') && $request->session()->has('shop')) {
            return $next($request);
        }
        
        // If no session, check for shop parameter (embedded app)
        $shop = $request->query('shop');
        
        if ($shop) {
            // Try to find shop in database
            $shopRecord = DB::table('shops')
                ->where('shop_domain', $shop)
                ->first();
                
            if ($shopRecord) {
                // Restore session from database
                $request->session()->put('access_token', $shopRecord->access_token);
                $request->session()->put('shop', $shop);
                
                return $next($request);
            }
        }
        
        // No valid authentication, redirect to auth
        return redirect()->route('shopify.auth', ['shop' => $shop]);
    }
}
