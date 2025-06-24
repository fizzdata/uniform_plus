<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusSeeder extends Seeder
{
    public function run()
{
    
     // Clear existing statuses to avoid duplicates
    DB::table('statuses')->delete();

     $statuses = [
            // Simple in-store buy (Workflow 1)
            ['s_id' => 11, 'name' => 'Started', 'description' => 'Order placed by customer in store', 'color' => 'bg-gray-500'],
            ['s_id' => 12, 'name' => 'Completed', 'description' => 'Standard order completed', 'color' => 'bg-green-500'],

            // Order with Logo and delivery (Workflow 2)
            ['s_id' => 21, 'name' => 'Paid', 'description' => 'Payment made in store', 'color' => 'bg-blue-500'],
            ['s_id' => 22, 'name' => 'Logo Production', 'description' => 'Logo design or processing stage', 'color' => 'bg-purple-500'],
            ['s_id' => 23, 'name' => 'Dropped Off Logo', 'description' => 'Items left for logo processing', 'color' => 'bg-yellow-500'],
            ['s_id' => 24, 'name' => 'Picked Up Logo', 'description' => 'Items returned after processing', 'color' => 'bg-yellow-500'],
            ['s_id' => 25, 'name' => 'Ready for Delivery', 'description' => 'Package is ready to go out', 'color' => 'bg-amber-500'],
            ['s_id' => 26, 'name' => 'Delivered', 'description' => 'Package delivered successfully', 'color' => 'bg-green-600'],


            // Order with Logo and pickup (Workflow 2)
            ['s_id' => 31, 'name' => 'Paid', 'description' => 'Payment made in store', 'color' => 'bg-blue-500'],
            ['s_id' => 32, 'name' => 'Logo Production', 'description' => 'Logo design or processing stage', 'color' => 'bg-purple-500'],
            ['s_id' => 33, 'name' => 'Dropped Off Logo', 'description' => 'Items left for logo processing', 'color' => 'bg-yellow-500'],
            ['s_id' => 34, 'name' => 'Picked Up Logo', 'description' => 'Items returned after processing', 'color' => 'bg-yellow-500'],
            ['s_id' => 37, 'name' => 'Ready for Pickup', 'description' => 'Waiting for customer pickup', 'color' => 'bg-orange-500'],
            ['s_id' => 38, 'name' => 'Picked Up', 'description' => 'Picked up by customer', 'color' => 'bg-lime-500'],

            // Phone Order workflow delivery (Workflow 3)
            ['s_id' => 41, 'name' => 'Paid', 'description' => 'Payment completed over the phone', 'color' => 'bg-indigo-500'],
            ['s_id' => 42, 'name' => 'Order Picked', 'description' => 'Items gathered for phone order', 'color' => 'bg-pink-500'],
            ['s_id' => 43, 'name' => 'Ready for Delivery', 'description' => 'Ready for delivery (from phone)', 'color' => 'bg-amber-500'],
            ['s_id' => 44, 'name' => 'Delivered', 'description' => 'Delivered (phone order)', 'color' => 'bg-green-600'],

            // Phone Order workflow pickup (Workflow 3b)
            ['s_id' => 51, 'name' => 'Paid', 'description' => 'Payment completed over the phone', 'color' => 'bg-indigo-500'],
            ['s_id' => 52, 'name' => 'Order Picked', 'description' => 'Items gathered for phone order', 'color' => 'bg-pink-500'],
            ['s_id' => 55, 'name' => 'Ready for Pickup', 'description' => 'Awaiting pickup (phone)', 'color' => 'bg-orange-500'],
            ['s_id' => 56, 'name' => 'Picked Up', 'description' => 'Picked up (phone order)', 'color' => 'bg-lime-500'],

            // Website Order with Shipping (Workflow 4)
            ['s_id' => 61, 'name' => 'Paid', 'description' => 'Payment done online', 'color' => 'bg-teal-500'],
            ['s_id' => 62, 'name' => 'Order Picked', 'description' => 'Items selected for shipping', 'color' => 'bg-pink-500'],
            ['s_id' => 63, 'name' => 'Order Packed', 'description' => 'Packaged for shipment', 'color' => 'bg-orange-500'],
            ['s_id' => 64, 'name' => 'Shipped', 'description' => 'Items dispatched', 'color' => 'bg-blue-500'],
            ['s_id' => 65, 'name' => 'Delivered', 'description' => 'Delivered (website order)', 'color' => 'bg-green-600'],


            // Custom Order workflow (Workflow 5)
            ['s_id' => 71, 'name' => 'Custom Order Started', 'description' => 'Custom order created', 'color' => 'bg-purple-500'],
            ['s_id' => 72, 'name' => 'Production Started', 'description' => 'Manufacturing in progress', 'color' => 'bg-fuchsia-500'],
            ['s_id' => 73, 'name' => 'Order sent to Factory', 'description' => 'Production request sent', 'color' => 'bg-rose-500'],
            ['s_id' => 74, 'name' => 'Goods Received', 'description' => 'Production finished & returned', 'color' => 'bg-emerald-500'],
            ['s_id' => 75, 'name' => 'Contacted Customer', 'description' => 'Customer has been contacted', 'color' => 'bg-sky-500'],
            ['s_id' => 76, 'name' => 'Collected', 'description' => 'Custom goods collected', 'color' => 'bg-violet-500'],
        ];

        foreach ($statuses as $s) {
            DB::table('statuses')->insert([
                'id' => $s['s_id'],
                'name' => $s['name'],
                'description' => $s['description'],
                'color' => $s['color'],
            ]);
        }
    }
}