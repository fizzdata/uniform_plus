<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Status;
use Illuminate\Support\Facades\DB;

class StatusTransitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Get status IDs by name
    $statuses = Status::pluck('id', 'name');

    //dump($statuses);

$transitions = [
    // In-store payment workflow
    ['Started', 'Paid in Store'],
    ['Paid in Store', 'Completed'],
    ['Paid in Store', 'Logo Production'], // For delivery
    ['Dropped Off logo', 'Picked Up logo'],
    ['Picked Up logo', 'Ready for Delivery'],
    ['Ready for Delivery', 'Delivered'],
    ['Ready for Pickup', 'Picked Up'],

    // Phone payment workflow
    ['Started', 'Paid on Phone'],
    ['Paid on Phone', 'Order Picked'],
    ['Order Picked', 'Ready for Delivery'],
    ['Paid on Phone', 'Ready for Pickup'], // Phone pickup path

    // Website payment workflow
    ['Started', 'Paid on Website'],
    ['Paid on Website', 'Order Picked'],
    ['Order Picked', 'Order Packed'],
    ['Order Packed', 'Ready for Delivery'],
    ['Order Packed', 'Ready for Pickup'],

    // Custom Production Flow
    ['Started', 'Custom Order Production'],
    ['Custom Order Production', 'Order sent to Factory'],
    ['Order sent to Factory', 'Goods Received'],
    ['Goods Received', 'Contacted Customer'],
    ['Contacted Customer', 'Ready for Pickup'],
    ['Picked Up', 'Completed']
];

    $data = [];
    foreach ($transitions as $transition) {
        $data[] = [
            'current_status_id' => $statuses[$transition[0]],
            'next_status_id' => $statuses[$transition[1]]
        ];
    }

    DB::table('status_transitions')->insert($data);

    }
}
