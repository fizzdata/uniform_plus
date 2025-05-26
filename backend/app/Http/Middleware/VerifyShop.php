<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class VerifyShop
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        
        $shop = $request->input('shop');

        if (!$shop) {
            return response()->json(['success' => false, 'error' => 'Shop parameter is missing'], 400);
        }

        $storedShop = DB::table('shops')->where('shop_domain', $shop)->first();

        if (!$storedShop) {
            return response()->json(['success' => false, 'error' => 'Shop not found']);
        }

        $request->merge(['shop' => $storedShop->shop_domain]);
        $request->merge(['access_token' => $storedShop->access_token]);

        return $next($request);
    }
}
