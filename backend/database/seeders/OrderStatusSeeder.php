<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusSeeder extends Seeder
{
    public function run()
    {
$statuses = [
    ['name' => 'Started', 'description' => 'Order has been started', 'color' => 'bg-blue-500', 'sequence' => 1], // Cool tone for initial stage
    ['name' => 'Paid', 'description' => 'Order has been paid', 'color' => 'bg-green-500', 'sequence' => 2], // Green for success/payment
    ['name' => 'In Production', 'description' => 'Order is in production', 'color' => 'bg-yellow-500', 'sequence' => 3], // Yellow for active work
    ['name' => 'Received from Production', 'description' => 'Order has been received from production', 'color' => 'bg-purple-500', 'sequence' => 4], // Purple for transition phase
    ['name' => 'Ready for Pickup', 'description' => 'Order is ready for pickup', 'color' => 'bg-indigo-500', 'sequence' => 5], // Indigo for preparedness
    ['name' => 'Picked Up', 'description' => 'Order has been picked up', 'color' => 'bg-gray-500', 'sequence' => 6], // Neutral gray for completion
    ['name' => 'Delivered to Customer', 'description' => 'Order has been delivered to customer', 'color' => 'bg-teal-500', 'sequence' => 7], // Teal for final delivery
];
        DB::table('statuses')->insert($statuses);
    }
}