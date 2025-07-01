<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusSeeder extends Seeder
{
    public function run()
{
$statuses = [
    ['name' => 'Web order Started', 'description' => 'Order has been placed by customer', 'color' => 'bg-gray-500'],
    ['name' => 'POS order Started', 'description' => 'Order initiated at physical store', 'color' => 'bg-gray-600'],
    ['name' => 'Phone order Started', 'description' => 'Order initiated via phone call', 'color' => 'bg-gray-700'],
    ['name' => 'Custom order Started', 'description' => 'Custom order process initiated', 'color' => 'bg-gray-800'],
    ['name' => 'admin_draft_order', 'description' => 'Draft order created by admin', 'color' => 'bg-gray-900'],
    ['name' => 'Paid in Store', 'description' => 'Payment completed at physical location', 'color' => 'bg-blue-500'],
    ['name' => 'Paid on Phone', 'description' => 'Payment completed via phone order', 'color' => 'bg-indigo-500'],
    ['name' => 'Paid on Website', 'description' => 'Online payment processed', 'color' => 'bg-teal-500'],
    ['name' => 'Order Picked', 'description' => 'Items gathered for phone orders', 'color' => 'bg-pink-500'],
    ['name' => 'Order Packed', 'description' => 'Items packaged for shipping', 'color' => 'bg-orange-500'],
    ['name' => 'Picked Up', 'description' => 'Customer collected items', 'color' => 'bg-lime-500'],
    ['name' => 'Custom Order Production', 'description' => 'Custom manufacturing started', 'color' => 'bg-fuchsia-500'],
    ['name' => 'Order sent to Factory', 'description' => 'Production instructions dispatched', 'color' => 'bg-rose-500'],
    ['name' => 'Goods Received', 'description' => 'Finished products returned', 'color' => 'bg-emerald-500'],
    ['name' => 'Contacted Customer', 'description' => 'Notification sent to customer', 'color' => 'bg-sky-500'],
    ['name' => 'Collected', 'description' => 'Custom items retrieved', 'color' => 'bg-violet-500'],
    ['name' => 'Draft Order', 'description' => 'Order saved as draft for later completion', 'color' => 'bg-gray-400'],
    ['name' => 'In Production', 'description' => 'Order is currently being processed', 'color' => 'bg-yellow-500'],
    ['name' => 'Ready for Payment', 'description' => 'Order ready for customer payment', 'color' => 'bg-orange-500'],
    ['name' => 'Payment Pending', 'description' => 'Awaiting customer payment confirmation', 'color' => 'bg-red-500'],
    ['name' => 'Completed', 'description' => 'Final completion of standard orders', 'color' => 'bg-green-500'],
    ['name' => 'Logo Production', 'description' => 'Logo processing stage', 'color' => 'bg-purple-500'],
    ['name' => 'Dropped Off logo', 'description' => 'Items left for processing', 'color' => 'bg-yellow-500'],
    ['name' => 'Picked Up logo', 'description' => 'Items returned for processing', 'color' => 'bg-yellow-500'],
    ['name' => 'Ready for Delivery', 'description' => 'Prepared for customer delivery', 'color' => 'bg-yellow-500'],
    ['name' => 'Delivered', 'description' => 'Items successfully delivered', 'color' => 'bg-green-600'],
    ['name' => 'Ready for Pickup', 'description' => 'Awaiting customer collection', 'color' => 'bg-amber-500'],
];


    foreach($statuses as $s){

   
    DB::table('statuses')->insert($s);
}
}
}