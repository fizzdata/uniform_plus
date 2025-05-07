<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stage;

class StagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stages = [
            [
                'name' => 'New Order',
                'color' => '#3498db', // Blue
                'description' => 'Order just received',
                'sort_order' => 1
            ],
            [
                'name' => 'Processing',
                'color' => '#f39c12', // Orange
                'description' => 'Order is being processed',
                'sort_order' => 2
            ],
            [
                'name' => 'Ready to Ship',
                'color' => '#2ecc71', // Green
                'description' => 'Order is packed and ready for shipping',
                'sort_order' => 3
            ],
            [
                'name' => 'Shipped',
                'color' => '#9b59b6', // Purple
                'description' => 'Order has been shipped',
                'sort_order' => 4
            ],
            [
                'name' => 'Delivered',
                'color' => '#16a085', // Turquoise
                'description' => 'Order has been delivered',
                'sort_order' => 5
            ],
            [
                'name' => 'Completed',
                'color' => '#27ae60', // Emerald
                'description' => 'Order is complete',
                'sort_order' => 6
            ],
            [
                'name' => 'Cancelled',
                'color' => '#e74c3c', // Red
                'description' => 'Order was cancelled',
                'sort_order' => 7
            ]
        ];

        foreach ($stages as $stage) {
            Stage::create($stage);
        }
    }
}