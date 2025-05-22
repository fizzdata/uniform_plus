<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusTransitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Get status IDs by name
    $statuses = Status::pluck('status_id', 'name');

    $transitions = [
        // In-store payment workflow
        ['Order Created', 'Paid in Store'],
        ['Paid in Store', 'Completed'],
        ['Paid in Store', 'Logo'], // For delivery
        ['Completed', 'Ready for Pickup'], // For pickup
        ['Logo', 'Dropped Off'],
        ['Dropped Off', 'Picked Up Logo'],
        ['Picked Up Logo', 'Ready for Delivery'],
        ['Ready for Delivery', 'Delivered'],
        ['Ready for Pickup', 'Picked Up'],

        // Phone payment workflow
        ['Order Created', 'Paid on Phone'],
        ['Paid on Phone', 'Order Picked (Phone)'],
        ['Order Picked (Phone)', 'Ready for Delivery (Phone)'],
        ['Ready for Delivery (Phone)', 'Delivered to Customer'],
        ['Paid on Phone', 'Ready for Pickup'], // Phone pickup path

        // Website payment workflow
        ['Order Created', 'Paid on Website'],
        ['Paid on Website', 'Order Picked (Website)'],
        ['Order Picked (Website)', 'Order Packed'],
        ['Order Packed', 'Ready for Shipment'],
        ['Order Packed', 'Ready for Pickup (Website)'],
        ['Ready for Pickup (Website)', 'Picked Up'],

        // Custom Production Flow
        ['Order Created', 'Custom Order Production'],
        ['Custom Order Production', 'Order sent to Factory'],
        ['Order sent to Factory', 'Goods Received'],
        ['Goods Received', 'Contacted Customer Ready for Collection'],
        ['Contacted Customer Ready for Collection', 'Collected'],
        ['Collected', 'Custom Order Completed']
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
