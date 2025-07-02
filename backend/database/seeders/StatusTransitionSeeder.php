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
    ['POS order Started', 'Paid in Store'],
    ['Paid in Store', 'Completed'],
    ['Paid in Store', 'Logo Production'],
    ['Logo Production', 'Dropped Off logo'],
    ['Dropped Off logo', 'Picked Up logo'],
    ['Picked Up logo', 'Ready for Delivery'],
    ['Ready for Delivery', 'Delivered'],
    ['Ready for Pickup', 'Picked Up'],

    // Phone payment workflow
    ['Phone order Started', 'Paid on Phone'],
    ['Paid on Phone', 'Order Picked'],
    ['Order Picked', 'Ready for Delivery'],
    ['Paid on Phone', 'Ready for Pickup'], // Phone pickup path

    // Website payment workflow
    ['Web order Started', 'Paid on Website'],
    ['Paid on Website', 'Order Picked'],
    ['Order Picked', 'Order Packed'],
    ['Order Packed', 'Ready for Delivery'],
    ['Order Packed', 'Ready for Pickup'],

    // Custom Production Flow
    ['Custom order Started', 'Custom Order Production'],
    ['Custom Order Production', 'Order sent to Factory'],
    ['Order sent to Factory', 'Goods Received'],
    ['Goods Received', 'Contacted Customer'],
    ['Contacted Customer', 'Ready for Pickup'],
    ['Picked Up', 'Completed']
];
    $data = [];
    foreach ($transitions as $transition) {
        $data[] = [
            'from_status' => $statuses[$transition[0]],
            'to_status' => $statuses[$transition[1]]
        ];
    }

    DB::table('order_status_transitions')->insert($data);

    }
}