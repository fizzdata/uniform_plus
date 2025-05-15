<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusSeeder extends Seeder
{
    public function run()
    {
        $statuses = [
            ['name' => 'Stared', 'description' => 'Order has been stared', 'color' => 'blue', 'sequence' => 1],
            ['name' => 'paid', 'description' => 'Order has been paid', 'color' => 'green', 'sequence' => 2],
            ['name' => 'in production', 'description' => 'Order is in production', 'color' => 'orange', 'sequence' => 3],
            ['name' => ' received from production', 'description' => 'Order has been received from production', 'color' => 'pink', 'sequence' => 4],
            ['name' => 'ready for pickup', 'description' => 'Order is ready for pickup', 'color' => 'purple', 'sequence' => 5],
            ['name' => 'picked up', 'description' => 'Order has been picked up', 'color' => '#FF5733', 'sequence' => 6],
            ['name' => 'delivered to customer', 'description' => 'Order has been delivered to customer', 'color' => '#C70039', 'sequence' => 7],
        ];

        DB::table('statuses')->insert($statuses);
    }
}