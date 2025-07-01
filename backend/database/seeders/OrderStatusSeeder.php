<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusSeeder extends Seeder
{
    public function run()
    {

        /*
        Source mapping:
        1 => 'POS', 2 => 'Web', 3 => 'Draft', 4 => 'Phone', 5 => 'Custom', 9 => 'Unknown'
        
        Financial mapping:
        0 => 'pending', 1 => 'authorized', 2 => 'partially_paid', 
        3 => 'paid', 4 => 'partially_refunded', 5 => 'refunded', 6 => 'voided'
        
        Shipping mapping:
        0 => 'Pickup', 1 => 'Delivery', 2 => 'Shipping'
        
        Logo mapping:
        0 => 'No Logo', 1 => 'Logo Order'
        */

        // Define workflows for PAID orders (financial = 3)
        $paidWorkflows = [
            // POS Orders (source = 1)
            1 => [
                0 => [ // Pickup
                    0 => [ // No Logo
                        [1, 'Started', 'Order placed by customer in store', 'bg-gray-500'],
                        [2, 'Completed', 'Standard order completed', 'bg-green-500'],
                    ],
                    1 => [ // With Logo
                        [1, 'Paid', 'Payment made in store', 'bg-blue-500'],
                        [2, 'Logo Production', 'Logo design or processing stage', 'bg-purple-500'],
                        [3, 'Dropped Off Logo', 'Items left for logo processing', 'bg-yellow-500'],
                        [4, 'Picked Up Logo', 'Items returned after processing', 'bg-yellow-600'],
                        [5, 'Ready for Pickup', 'Waiting for customer pickup', 'bg-orange-500'],
                        [6, 'Picked Up', 'Picked up by customer', 'bg-lime-500'],
                    ],
                ],
                1 => [ // Delivery
                    0 => [ // No Logo
                        [1, 'Started', 'Order placed by customer in store', 'bg-gray-500'],
                        [2, 'Completed', 'Standard order completed', 'bg-green-500'],
                        [3, 'Ready for Delivery', 'Package is ready to go out', 'bg-amber-500'],
                        [4, 'Delivered', 'Package delivered successfully', 'bg-green-600'],
                    ],
                    1 => [ // With Logo
                        [1, 'Paid', 'Payment made in store', 'bg-blue-500'],
                        [2, 'Logo Production', 'Logo design or processing stage', 'bg-purple-500'],
                        [3, 'Dropped Off Logo', 'Items left for logo processing', 'bg-yellow-500'],
                        [4, 'Picked Up Logo', 'Items returned after processing', 'bg-yellow-600'],
                        [5, 'Ready for Delivery', 'Package is ready to go out', 'bg-amber-500'],
                        [6, 'Delivered', 'Package delivered successfully', 'bg-green-600'],
                    ],
                ],
                2 => [ // Shipping
                    0 => [ // No Logo
                        [1, 'Paid', 'Payment made in store', 'bg-blue-500'],
                        [2, 'Order Picked', 'Items selected for shipping', 'bg-pink-500'],
                        [3, 'Order Packed', 'Packaged for shipment', 'bg-orange-500'],
                        [4, 'Shipped', 'Items dispatched', 'bg-blue-600'],
                        [5, 'Delivered', 'Package delivered successfully', 'bg-green-600'],
                    ],
                    1 => [ // With Logo
                        [1, 'Paid', 'Payment made in store', 'bg-blue-500'],
                        [2, 'Logo Production', 'Logo design or processing stage', 'bg-purple-500'],
                        [3, 'Dropped Off Logo', 'Items left for logo processing', 'bg-yellow-500'],
                        [4, 'Picked Up Logo', 'Items returned after processing', 'bg-yellow-600'],
                        [5, 'Order Packed', 'Packaged for shipment', 'bg-orange-500'],
                        [6, 'Shipped', 'Items dispatched', 'bg-blue-600'],
                        [7, 'Delivered', 'Package delivered successfully', 'bg-green-600'],
                    ],
                ],
            ],
            
            // Web Orders (source = 2)
            2 => [
                0 => [ // Pickup
                    0 => [ // No Logo
                        [1, 'Paid', 'Payment done online', 'bg-teal-500'],
                        [2, 'Order Picked', 'Items selected for pickup', 'bg-pink-500'],
                        [3, 'Ready for Pickup', 'Ready for customer pickup', 'bg-orange-500'],
                        [4, 'Picked Up', 'Picked up by customer', 'bg-green-600'],
                    ],
                    1 => [ // With Logo
                        [1, 'Paid', 'Payment done online', 'bg-teal-500'],
                        [2, 'Logo Production', 'Logo design or processing stage', 'bg-purple-500'],
                        [3, 'Dropped Off Logo', 'Items left for logo processing', 'bg-yellow-500'],
                        [4, 'Picked Up Logo', 'Items returned after processing', 'bg-yellow-600'],
                        [5, 'Ready for Pickup', 'Ready for customer pickup', 'bg-orange-500'],
                        [6, 'Picked Up', 'Picked up by customer', 'bg-green-600'],
                    ],
                ],
                1 => [ // Delivery
                    0 => [ // No Logo
                        [1, 'Paid', 'Payment done online', 'bg-teal-500'],
                        [2, 'Order Picked', 'Items selected for delivery', 'bg-pink-500'],
                        [3, 'Ready for Delivery', 'Ready for delivery', 'bg-amber-500'],
                        [4, 'Delivered', 'Delivered to customer', 'bg-green-600'],
                    ],
                    1 => [ // With Logo
                        [1, 'Paid', 'Payment done online', 'bg-teal-500'],
                        [2, 'Logo Production', 'Logo design or processing stage', 'bg-purple-500'],
                        [3, 'Dropped Off Logo', 'Items left for logo processing', 'bg-yellow-500'],
                        [4, 'Picked Up Logo', 'Items returned after processing', 'bg-yellow-600'],
                        [5, 'Ready for Delivery', 'Package is ready to go out', 'bg-amber-500'],
                        [6, 'Delivered', 'Package delivered successfully', 'bg-green-600'],
                    ],
                ],
                2 => [ // Shipping
                    0 => [ // No Logo
                        [1, 'Paid', 'Payment done online', 'bg-teal-500'],
                        [2, 'Order Picked', 'Items selected for shipping', 'bg-pink-500'],
                        [3, 'Order Packed', 'Packaged for shipment', 'bg-orange-500'],
                        [4, 'Shipped', 'Items dispatched', 'bg-blue-600'],
                        [5, 'Delivered', 'Delivered (website order)', 'bg-green-600'],
                    ],
                    1 => [ // With Logo
                        [1, 'Paid', 'Payment done online', 'bg-teal-500'],
                        [2, 'Logo Production', 'Logo design or processing stage', 'bg-purple-500'],
                        [3, 'Dropped Off Logo', 'Items left for logo processing', 'bg-yellow-500'],
                        [4, 'Picked Up Logo', 'Items returned after processing', 'bg-yellow-600'],
                        [5, 'Order Packed', 'Packaged for shipment', 'bg-orange-500'],
                        [6, 'Shipped', 'Items dispatched', 'bg-blue-600'],
                        [7, 'Delivered', 'Package delivered successfully', 'bg-green-600'],
                    ],
                ],
            ],
            
            // Draft Orders (source = 3)
            3 => [
                0 => [ // Pickup
                    0 => [ // No Logo
                        [1, 'Draft Order Paid', 'Payment received for draft order', 'bg-blue-500'],
                        [2, 'Draft Order Completed', 'Draft order completed', 'bg-green-500'],
                    ],
                    1 => [ // With Logo
                        [1, 'Draft Order Paid', 'Payment received for draft order', 'bg-blue-500'],
                        [2, 'Logo Production', 'Logo design or processing stage', 'bg-purple-500'],
                        [3, 'Dropped Off Logo', 'Items left for logo processing', 'bg-yellow-500'],
                        [4, 'Picked Up Logo', 'Items returned after processing', 'bg-yellow-600'],
                        [5, 'Ready for Pickup', 'Ready for customer pickup', 'bg-orange-500'],
                        [6, 'Picked Up', 'Picked up by customer', 'bg-green-600'],
                    ],
                ],
                1 => [ // Delivery
                    0 => [ // No Logo
                        [1, 'Draft Order Paid', 'Payment received for draft order', 'bg-blue-500'],
                        [2, 'Ready for Delivery', 'Ready for delivery', 'bg-amber-500'],
                        [3, 'Delivered', 'Delivered (draft order)', 'bg-green-600'],
                    ],
                    1 => [ // With Logo
                        [1, 'Draft Order Paid', 'Payment received for draft order', 'bg-blue-500'],
                        [2, 'Logo Production', 'Logo design or processing stage', 'bg-purple-500'],
                        [3, 'Dropped Off Logo', 'Items left for logo processing', 'bg-yellow-500'],
                        [4, 'Picked Up Logo', 'Items returned after processing', 'bg-yellow-600'],
                        [5, 'Ready for Delivery', 'Package is ready to go out', 'bg-amber-500'],
                        [6, 'Delivered', 'Package delivered successfully', 'bg-green-600'],
                    ],
                ],
                2 => [ // Shipping
                    0 => [ // No Logo
                        [1, 'Draft Order Paid', 'Payment received for draft order', 'bg-blue-500'],
                        [2, 'Order Picked', 'Items selected for shipping', 'bg-pink-500'],
                        [3, 'Order Packed', 'Packaged for shipment', 'bg-orange-500'],
                        [4, 'Shipped', 'Items dispatched', 'bg-blue-600'],
                        [5, 'Delivered', 'Delivered (draft order)', 'bg-green-600'],
                    ],
                    1 => [ // With Logo
                        [1, 'Draft Order Paid', 'Payment received for draft order', 'bg-blue-500'],
                        [2, 'Logo Production', 'Logo design or processing stage', 'bg-purple-500'],
                        [3, 'Dropped Off Logo', 'Items left for logo processing', 'bg-yellow-500'],
                        [4, 'Picked Up Logo', 'Items returned after processing', 'bg-yellow-600'],
                        [5, 'Order Packed', 'Packaged for shipment', 'bg-orange-500'],
                        [6, 'Shipped', 'Items dispatched', 'bg-blue-600'],
                        [7, 'Delivered', 'Package delivered successfully', 'bg-green-600'],
                    ],
                ],
            ],
            
            // Phone Orders (source = 4)
            4 => [
                0 => [ // Pickup
                    0 => [ // No Logo
                        [1, 'Phone Order Started', 'Phone order created', 'bg-gray-500'],
                        [2, 'Order Picked', 'Items gathered for phone order', 'bg-pink-500'],
                        [3, 'Ready for Pickup', 'Awaiting pickup', 'bg-orange-500'],
                        [4, 'Picked Up', 'Picked up by customer', 'bg-lime-500'],
                    ],
                    1 => [ // With Logo
                        [1, 'Phone Order Started', 'Phone order created', 'bg-gray-500'],
                        [2, 'Logo Production', 'Logo design or processing stage', 'bg-purple-500'],
                        [3, 'Dropped Off Logo', 'Items left for logo processing', 'bg-yellow-500'],
                        [4, 'Picked Up Logo', 'Items returned after processing', 'bg-yellow-600'],
                        [5, 'Ready for Pickup', 'Awaiting pickup', 'bg-orange-500'],
                        [6, 'Picked Up', 'Picked up by customer', 'bg-lime-500'],
                    ],
                ],
                1 => [ // Delivery
                    0 => [ // No Logo
                        [1, 'Phone Order Started', 'Phone order created', 'bg-gray-500'],
                        [2, 'Order Picked', 'Items gathered for phone order', 'bg-pink-500'],
                        [3, 'Ready for Delivery', 'Ready for delivery', 'bg-amber-500'],
                        [4, 'Delivered', 'Delivered to customer', 'bg-green-600'],
                    ],
                    1 => [ // With Logo
                        [1, 'Phone Order Started', 'Phone order created', 'bg-gray-500'],
                        [2, 'Logo Production', 'Logo design or processing stage', 'bg-purple-500'],
                        [3, 'Dropped Off Logo', 'Items left for logo processing', 'bg-yellow-500'],
                        [4, 'Picked Up Logo', 'Items returned after processing', 'bg-yellow-600'],
                        [5, 'Ready for Delivery', 'Ready for delivery', 'bg-amber-500'],
                        [6, 'Delivered', 'Delivered to customer', 'bg-green-600'],
                    ],
                ],
                2 => [ // Shipping
                    0 => [ // No Logo
                        [1, 'Phone Order Started', 'Phone order created', 'bg-gray-500'],
                        [2, 'Order Picked', 'Items gathered for phone order', 'bg-pink-500'],
                        [3, 'Order Packed', 'Packaged for shipment', 'bg-orange-500'],
                        [4, 'Shipped', 'Items dispatched', 'bg-blue-600'],
                        [5, 'Delivered', 'Delivered to customer', 'bg-green-600'],
                    ],
                    1 => [ // With Logo
                        [1, 'Phone Order Started', 'Phone order created', 'bg-gray-500'],
                        [2, 'Logo Production', 'Logo design or processing stage', 'bg-purple-500'],
                        [3, 'Dropped Off Logo', 'Items left for logo processing', 'bg-yellow-500'],
                        [4, 'Picked Up Logo', 'Items returned after processing', 'bg-yellow-600'],
                        [5, 'Order Packed', 'Packaged for shipment', 'bg-orange-500'],
                        [6, 'Shipped', 'Items dispatched', 'bg-blue-600'],
                        [7, 'Delivered', 'Delivered to customer', 'bg-green-600'],
                    ],
                ],
            ],
            
            // Custom Orders (source = 5)
            5 => [
                0 => [ // Pickup
                    0 => [ // No Logo
                        [1, 'Custom Order Started', 'Custom order created', 'bg-purple-500'],
                        [2, 'Production Started', 'Manufacturing in progress', 'bg-fuchsia-500'],
                        [3, 'Order sent to Factory', 'Production request sent', 'bg-rose-500'],
                        [4, 'Goods Received', 'Production finished & returned', 'bg-emerald-500'],
                        [5, 'Contacted Customer', 'Customer has been contacted', 'bg-sky-500'],
                        [6, 'Collected', 'Custom goods collected', 'bg-violet-500'],
                    ],
                    1 => [ // With Logo
                        [1, 'Custom Order Started', 'Custom order created', 'bg-purple-500'],
                        [2, 'Production Started', 'Manufacturing in progress', 'bg-fuchsia-500'],
                        [3, 'Order sent to Factory', 'Production request sent', 'bg-rose-500'],
                        [4, 'Goods Received', 'Production finished & returned', 'bg-emerald-500'],
                        [5, 'Contacted Customer', 'Customer has been contacted', 'bg-sky-500'],
                        [6, 'Collected', 'Custom goods collected', 'bg-violet-500'],
                    ],
                ],
                1 => [ // Delivery
                    0 => [ // No Logo
                        [1, 'Custom Order Started', 'Custom order created', 'bg-purple-500'],
                        [2, 'Production Started', 'Manufacturing in progress', 'bg-fuchsia-500'],
                        [3, 'Order sent to Factory', 'Production request sent', 'bg-rose-500'],
                        [4, 'Goods Received', 'Production finished & returned', 'bg-emerald-500'],
                        [5, 'Contacted Customer', 'Customer has been contacted', 'bg-sky-500'],
                        [6, 'Delivered', 'Custom goods delivered', 'bg-violet-500'],
                    ],
                    1 => [ // With Logo
                        [1, 'Custom Order Started', 'Custom order created', 'bg-purple-500'],
                        [2, 'Production Started', 'Manufacturing in progress', 'bg-fuchsia-500'],
                        [3, 'Order sent to Factory', 'Production request sent', 'bg-rose-500'],
                        [4, 'Goods Received', 'Production finished & returned', 'bg-emerald-500'],
                        [5, 'Contacted Customer', 'Customer has been contacted', 'bg-sky-500'],
                        [6, 'Delivered', 'Custom goods delivered', 'bg-violet-500'],
                    ],
                ],
                2 => [ // Shipping
                    0 => [ // No Logo
                        [1, 'Custom Order Started', 'Custom order created', 'bg-purple-500'],
                        [2, 'Production Started', 'Manufacturing in progress', 'bg-fuchsia-500'],
                        [3, 'Order sent to Factory', 'Production request sent', 'bg-rose-500'],
                        [4, 'Goods Received', 'Production finished & returned', 'bg-emerald-500'],
                        [5, 'Order Packed', 'Packaged for shipment', 'bg-orange-500'],
                        [6, 'Shipped', 'Items dispatched', 'bg-blue-600'],
                        [7, 'Delivered', 'Custom goods delivered', 'bg-violet-500'],
                    ],
                    1 => [ // With Logo
                        [1, 'Custom Order Started', 'Custom order created', 'bg-purple-500'],
                        [2, 'Production Started', 'Manufacturing in progress', 'bg-fuchsia-500'],
                        [3, 'Order sent to Factory', 'Production request sent', 'bg-rose-500'],
                        [4, 'Goods Received', 'Production finished & returned', 'bg-emerald-500'],
                        [5, 'Order Packed', 'Packaged for shipment', 'bg-orange-500'],
                        [6, 'Shipped', 'Items dispatched', 'bg-blue-600'],
                        [7, 'Delivered', 'Custom goods delivered', 'bg-violet-500'],
                    ],
                ],
            ],
            
            // Unknown Orders (source = 9)
            9 => [
                0 => [ // Pickup
                    0 => [ // No Logo
                        [1, 'Started', 'Order placed', 'bg-gray-500'],
                        [2, 'Completed', 'Standard order completed', 'bg-green-500'],
                    ],
                    1 => [ // With Logo
                        [1, 'Paid', 'Payment made', 'bg-blue-500'],
                        [2, 'Logo Production', 'Logo design or processing stage', 'bg-purple-500'],
                        [3, 'Dropped Off Logo', 'Items left for logo processing', 'bg-yellow-500'],
                        [4, 'Picked Up Logo', 'Items returned after processing', 'bg-yellow-600'],
                        [5, 'Ready for Pickup', 'Waiting for customer pickup', 'bg-orange-500'],
                        [6, 'Picked Up', 'Picked up by customer', 'bg-lime-500'],
                    ],
                ],
                1 => [ // Delivery
                    0 => [ // No Logo
                        [1, 'Started', 'Order placed', 'bg-gray-500'],
                        [2, 'Completed', 'Standard order completed', 'bg-green-500'],
                        [3, 'Ready for Delivery', 'Package is ready to go out', 'bg-amber-500'],
                        [4, 'Delivered', 'Package delivered successfully', 'bg-green-600'],
                    ],
                    1 => [ // With Logo
                        [1, 'Paid', 'Payment made', 'bg-blue-500'],
                        [2, 'Logo Production', 'Logo design or processing stage', 'bg-purple-500'],
                        [3, 'Dropped Off Logo', 'Items left for logo processing', 'bg-yellow-500'],
                        [4, 'Picked Up Logo', 'Items returned after processing', 'bg-yellow-600'],
                        [5, 'Ready for Delivery', 'Package is ready to go out', 'bg-amber-500'],
                        [6, 'Delivered', 'Package delivered successfully', 'bg-green-600'],
                    ],
                ],
                2 => [ // Shipping
                    0 => [ // No Logo
                        [1, 'Started', 'Order placed', 'bg-gray-500'],
                        [2, 'Order Picked', 'Items selected for shipping', 'bg-pink-500'],
                        [3, 'Order Packed', 'Packaged for shipment', 'bg-orange-500'],
                        [4, 'Shipped', 'Items dispatched', 'bg-blue-600'],
                        [5, 'Delivered', 'Package delivered successfully', 'bg-green-600'],
                    ],
                    1 => [ // With Logo
                        [1, 'Paid', 'Payment made', 'bg-blue-500'],
                        [2, 'Logo Production', 'Logo design or processing stage', 'bg-purple-500'],
                        [3, 'Dropped Off Logo', 'Items left for logo processing', 'bg-yellow-500'],
                        [4, 'Picked Up Logo', 'Items returned after processing', 'bg-yellow-600'],
                        [5, 'Order Packed', 'Packaged for shipment', 'bg-orange-500'],
                        [6, 'Shipped', 'Items dispatched', 'bg-blue-600'],
                        [7, 'Delivered', 'Package delivered successfully', 'bg-green-600'],
                    ],
                ],
            ],
        ];

        $statuses = [];
        $sources = [1, 2, 3, 4, 5, 9]; // POS, Web, Draft, Phone, Custom, Unknown
        $financials = [0, 1, 2, 3, 4, 5, 6]; // pending, authorized, partially_paid, paid, partially_refunded, refunded, voided
        $shippings = [0, 1, 2]; // Pickup, Delivery, Shipping
        $logos = [0, 1]; // No Logo, Logo Order

        foreach ($sources as $src) {
            foreach ($financials as $fin) {
                foreach ($shippings as $ship) {
                    foreach ($logos as $logo) {
                        if ($fin == 3) {
                            // Financial status = PAID: Use workflow steps
                            if (isset($paidWorkflows[$src][$ship][$logo])) {
                                foreach ($paidWorkflows[$src][$ship][$logo] as $stepData) {
                                    [$step, $name, $desc, $color] = $stepData;
                                    $statuses[] = [$src, $fin, $ship, $logo, $step, $name, $desc, $color];
                                }
                            }
                        } else {
                            // Handle non-paid financial statuses
                            $step = 0;
                            
                            switch ($fin) {
                                case 0: // pending
                                    if ($src == 3) {
                                        $name = 'Draft Order Created';
                                        $desc = 'Draft order created in Shopify';
                                        $color = 'bg-gray-500';
                                    } else {
                                        $name = 'Pending Payment';
                                        $desc = 'Payment is pending';
                                        $color = 'bg-gray-500';
                                    }
                                    break;
                                    
                                case 1: // authorized
                                    $name = 'Payment Authorized';
                                    $desc = 'Payment has been authorized but not captured';
                                    $color = 'bg-blue-400';
                                    break;
                                    
                                case 2: // partially_paid
                                    $name = 'Partially Paid';
                                    $desc = 'Partial payment received';
                                    $color = 'bg-yellow-500';
                                    break;
                                    
                                case 4: // partially_refunded
                                    $name = 'Partially Refunded';
                                    $desc = 'Order has been partially refunded';
                                    $color = 'bg-orange-400';
                                    break;
                                    
                                case 5: // refunded
                                    $name = 'Refunded';
                                    $desc = 'Order has been fully refunded';
                                    $color = 'bg-red-500';
                                    break;
                                    
                                case 6: // voided
                                    $name = 'Voided';
                                    $desc = 'Order has been voided';
                                    $color = 'bg-gray-700';
                                    break;
                                    
                                default:
                                    $name = 'Unknown Status';
                                    $desc = 'Unknown financial status';
                                    $color = 'bg-gray-400';
                            }
                            
                            $statuses[] = [$src, $fin, $ship, $logo, $step, $name, $desc, $color];
                        }
                    }
                }
            }
        }

        // Insert all statuses into database
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

        $this->command->info('Order status seeder completed successfully!');
        $this->command->info('Total statuses created: ' . count($statuses));
        
        // Show breakdown by financial status
        $breakdown = [];
        foreach ($statuses as [$src, $fin, $ship, $logo, $step, $name, $desc, $color]) {
            $finName = ['pending', 'authorized', 'partially_paid', 'paid', 'partially_refunded', 'refunded', 'voided'][$fin];
            $breakdown[$finName] = ($breakdown[$finName] ?? 0) + 1;
        }
        
        $this->command->info('Breakdown by financial status:');
        foreach ($breakdown as $status => $count) {
            $this->command->info("  - {$status}: {$count} statuses");
        }
    }
}