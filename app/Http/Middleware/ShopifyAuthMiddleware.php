<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ShopifyAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if Shopify session exists
        if (!session('shopify_domain') || !session('shopify_token')) {
            // If this is an API request or AJAX, return a JSON response
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
            // Otherwise redirect to login/install page
            return redirect()->route('shopify.install');
        }
        
        return $next($request);
    }
}