<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;


class TestController extends Controller
{
    public function index(Request $request){
        
        $shop = 'test-shop.myshopify.com';

         $storedShop = DB::table('shops')->where('shop_domain', $shop)->first();

            if (!$storedShop) {
                return response()->json(['success' => false, 'error' => 'Shop not found']);
            }

            // Convert to an array before merging
            $request->merge(['shop' => (array) $storedShop]);


            try {


                //test here




                //end here
       
    } catch (\Exception $e) {
        dd(['error' => $e->getMessage()]);
    }

    }
}
