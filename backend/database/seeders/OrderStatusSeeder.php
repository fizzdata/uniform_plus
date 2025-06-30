<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusSeeder extends Seeder
{
    public function run()
    {
        DB::table('statuses')->truncate();

        /*
|POS|Financial|Shipping|Logo|Step|Name|Description|Color|

 $lookup = [
        'source' => [
            1 => 'POS',
            2 => 'Web',
            3 => 'Draft',
            4 => 'Phone',
            5 => 'Custom',
            9 => 'Unknown',
        ],
        'financial' => [
            0 => 'Unpaid / Pending',
            1 => 'Paid',
            2 => 'Partially Paid',
            3 => 'Refunded',
        ],
        'shipping' => [
            0 => 'Pickup',
            1 => 'Delivery',
        ],
        'logo' => [
            0 => 'No Logo',
            1 => 'Logo Order',
        ],


        */

        $statuses = [
            // POS with delivery (no logo)
            [1, 1, 1, 0, 1, 'Started', 'Order placed by customer in store', 'bg-gray-500'],
            [1, 1, 1, 0, 2, 'Completed', 'Standard order completed', 'bg-green-500'],
            [1, 1, 1, 0, 3, 'Ready for Delivery', 'Package is ready to go out', 'bg-amber-500'],
            [1, 1, 1, 0, 4, 'Delivered', 'Package delivered successfully', 'bg-green-600'],

            // POS simple pickup (no logo)
            [1, 1, 0, 0, 1, 'Started', 'Order placed by customer in store', 'bg-gray-500'],
            [1, 1, 0, 0, 2, 'Completed', 'Standard order completed', 'bg-green-500'],

            // POS with delivery + logo
            [1, 1, 1, 1, 1, 'Paid', 'Payment made in store', 'bg-blue-500'],
            [1, 1, 1, 1, 2, 'Logo Production', 'Logo design or processing stage', 'bg-purple-500'],
            [1, 1, 1, 1, 3, 'Dropped Off Logo', 'Items left for logo processing', 'bg-yellow-500'],
            [1, 1, 1, 1, 4, 'Picked Up Logo', 'Items returned after processing', 'bg-yellow-500'],
            [1, 1, 1, 1, 5, 'Ready for Delivery', 'Package is ready to go out', 'bg-amber-500'],
            [1, 1, 1, 1, 6, 'Delivered', 'Package delivered successfully', 'bg-green-600'],

            // POS pickup with logo
            [1, 1, 0, 1, 1, 'Paid', 'Payment made in store', 'bg-blue-500'],
            [1, 1, 0, 1, 2, 'Logo Production', 'Logo design or processing stage', 'bg-purple-500'],
            [1, 1, 0, 1, 3, 'Dropped Off Logo', 'Items left for logo processing', 'bg-yellow-500'],
            [1, 1, 0, 1, 4, 'Picked Up Logo', 'Items returned after processing', 'bg-yellow-500'],
            [1, 1, 0, 1, 7, 'Ready for Pickup', 'Waiting for customer pickup', 'bg-orange-500'],
            [1, 1, 0, 1, 8, 'Picked Up', 'Picked up by customer', 'bg-lime-500'],

            // POS phone delivery (no logo)
            [1, 1, 1, 0, 1, 'Paid', 'Payment completed over the phone', 'bg-indigo-500'],
            [1, 1, 1, 0, 2, 'Order Picked', 'Items gathered for phone order', 'bg-pink-500'],
            [1, 1, 1, 0, 3, 'Ready for Delivery', 'Ready for delivery (from phone)', 'bg-amber-500'],
            [1, 1, 1, 0, 4, 'Delivered', 'Delivered (phone order)', 'bg-green-600'],

            // POS phone pickup (no logo)
            [1, 1, 0, 0, 1, 'Paid', 'Payment completed over the phone', 'bg-indigo-500'],
            [1, 1, 0, 0, 2, 'Order Picked', 'Items gathered for phone order', 'bg-pink-500'],
            [1, 1, 0, 0, 5, 'Ready for Pickup', 'Awaiting pickup (phone)', 'bg-orange-500'],
            [1, 1, 0, 0, 6, 'Picked Up', 'Picked up (phone order)', 'bg-lime-500'],

            // Shopify Draft Order (no logo)
            [3, 0, 0, 0, 1, 'Draft Order Created', 'Draft order created in Shopify', 'bg-gray-500'],
            [3, 1, 0, 0, 2, 'Draft Order Paid', 'Payment received for draft order', 'bg-blue-500'],
            [3, 1, 0, 0, 3, 'Draft Order Completed', 'Draft order completed', 'bg-green-500'],

            // Web order with pickup (no logo)
            [2, 1, 0, 0, 1, 'Paid', 'Payment done online', 'bg-teal-500'],
            [2, 1, 0, 0, 2, 'Order Picked', 'Items selected for pickup', 'bg-pink-500'],
            [2, 1, 0, 0, 3, 'Ready for Pickup', 'Ready for customer pickup', 'bg-orange-500'],
            [2, 1, 0, 0, 4, 'Picked Up', 'Picked up by customer', 'bg-green-600'],


            // Web order with shipping (no logo)
            [2, 1, 1, 0, 1, 'Paid', 'Payment done online', 'bg-teal-500'],
            [2, 1, 1, 0, 2, 'Order Picked', 'Items selected for shipping', 'bg-pink-500'],
            [2, 1, 1, 0, 3, 'Order Packed', 'Packaged for shipment', 'bg-orange-500'],
            [2, 1, 1, 0, 4, 'Shipped', 'Items dispatched', 'bg-blue-500'],
            [2, 1, 1, 0, 5, 'Delivered', 'Delivered (website order)', 'bg-green-600'],

            // Logo order with delivery
            [1, 1, 1, 1, 1, 'Logo Order Started', 'Logo order created', 'bg-gray-500'],
            [1, 1, 1, 1, 2, 'Production Started', 'Manufacturing in progress', 'bg-purple-500'],
            [1, 1, 1, 1, 3, 'Order sent to Factory', 'Production request sent', 'bg-fuchsia-500'],
            [1, 1, 1, 1, 4, 'Goods Received', 'Production finished & returned', 'bg-rose-500'],
            [1, 1, 1, 1, 5, 'Contacted Customer', 'Customer has been contacted', 'bg-sky-500'],
            [1, 1, 1, 1, 6, 'Collected', 'Logo goods collected', 'bg-violet-500'],
            // Logo order with pickup
            [1, 1, 0, 1, 1, 'Logo Order Started', 'Logo order created', 'bg-gray-500'],
            [1, 1, 0, 1, 2, 'Production Started', 'Manufacturing in progress', 'bg-purple-500'],
            [1, 1, 0, 1, 3, 'Order sent to Factory', 'Production request sent', 'bg-fuchsia-500'],
            [1, 1, 0, 1, 4, 'Goods Received', 'Production finished & returned', 'bg-rose-500'],
            [1, 1, 0, 1, 5, 'Contacted Customer', 'Customer has been contacted', 'bg-sky-500'],
            [1, 1, 0, 1, 6, 'Collected', 'Logo goods collected', 'bg-violet-500'],
            // Phone order with delivery
            [1, 1, 1, 0, 1, 'Phone Order Started', 'Phone order created', 'bg-gray-500'],
            [1, 1, 1, 0, 2, 'Production Started', 'Manufacturing in progress', 'bg-purple-500'],
            [1, 1, 1, 0, 3, 'Order sent to Factory', 'Production request sent', 'bg-fuchsia-500'],
            [1, 1, 1, 0, 4, 'Goods Received', 'Production finished & returned', 'bg-rose-500'],
            [1, 1, 1, 0, 5, 'Contacted Customer', 'Customer has been contacted', 'bg-sky-500'],
            [1, 1, 1, 0, 6, 'Collected', 'Phone goods collected', 'bg-violet-500'],
            // Custom workflow
            [4, 1, 0, 0, 1, 'Custom Order Started', 'Custom order created', 'bg-purple-500'],
            [4, 1, 0, 0, 2, 'Production Started', 'Manufacturing in progress', 'bg-fuchsia-500'],
            [4, 1, 0, 0, 3, 'Order sent to Factory', 'Production request sent', 'bg-rose-500'],
            [4, 1, 0, 0, 4, 'Goods Received', 'Production finished & returned', 'bg-emerald-500'],
            [4, 1, 0, 0, 5, 'Contacted Customer', 'Customer has been contacted', 'bg-sky-500'],
            [4, 1, 0, 0, 6, 'Collected', 'Custom goods collected', 'bg-violet-500'],
        ];

        foreach ($statuses as [$src, $fin, $ship, $logo, $step, $name, $desc, $color]) {
            $s_id = $src * 10000 + $fin * 1000 + $ship * 100 + $logo * 10 + $step;

            DB::table('statuses')->insert([
                's_id' => $s_id,
                'name' => $name,
                'description' => $desc,
                'color' => $color,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}