<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusSeeder extends Seeder
{
    public function run()
    {

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
        
        $workflows = [
            1 => [  // POS
                0 => [ // shipping = 0 pickup
                    0 => [ // logo = 0 no logo
                        [1, 'Started', 'Order placed by customer in store', 'bg-gray-500'],
                        [2, 'Completed', 'Standard order completed', 'bg-green-500'],
                    ],
                    1 => [ // logo = 1
                        [1, 'Paid', 'Payment made in store', 'bg-blue-500'],
                        [2, 'Logo Production', 'Logo design or processing stage', 'bg-purple-500'],
                        [3, 'Dropped Off Logo', 'Items left for logo processing', 'bg-yellow-500'],
                        [4, 'Picked Up Logo', 'Items returned after processing', 'bg-yellow-500'],
                        [5, 'Ready for Pickup', 'Waiting for customer pickup', 'bg-orange-500'],
                        [6, 'Picked Up', 'Picked up by customer', 'bg-lime-500'],
                    ],
                ],
                1 => [ // shipping = 1
                    0 => [ // logo = 0
                        [1, 'Started', 'Order placed by customer in store', 'bg-gray-500'],
                        [2, 'Completed', 'Standard order completed', 'bg-green-500'],
                        [3, 'Ready for Delivery', 'Package is ready to go out', 'bg-amber-500'],
                        [4, 'Delivered', 'Package delivered successfully', 'bg-green-600'],
                    ],
                    1 => [ // logo = 1
                        [1, 'Paid', 'Payment made in store', 'bg-blue-500'],
                        [2, 'Logo Production', 'Logo design or processing stage', 'bg-purple-500'],
                        [3, 'Dropped Off Logo', 'Items left for logo processing', 'bg-yellow-500'],
                        [4, 'Picked Up Logo', 'Items returned after processing', 'bg-yellow-500'],
                        [5, 'Ready for Delivery', 'Package is ready to go out', 'bg-amber-500'],
                        [6, 'Delivered', 'Package delivered successfully', 'bg-green-600'],
                    ],
                ],
            ],
            2 => [  // Web
                0 => [ // shipping = 0
                    0 => [ // logo = 0
                        [1, 'Paid', 'Payment done online', 'bg-teal-500'],
                        [2, 'Order Picked', 'Items selected for pickup', 'bg-pink-500'],
                        [3, 'Ready for Pickup', 'Ready for customer pickup', 'bg-orange-500'],
                        [4, 'Picked Up', 'Picked up by customer', 'bg-green-600'],
                    ],
                    1 => [ // logo = 1
                        [1, 'Paid', 'Payment done online', 'bg-teal-500'],
                        [2, 'Logo Production', 'Logo design or processing stage', 'bg-purple-500'],
                        [3, 'Dropped Off Logo', 'Items left for logo processing', 'bg-yellow-500'],
                        [4, 'Picked Up Logo', 'Items returned after processing', 'bg-yellow-500'],
                        [5, 'Ready for Pickup', 'Ready for customer pickup', 'bg-orange-500'],
                        [6, 'Picked Up', 'Picked up by customer', 'bg-green-600'],
                    ],
                ],
                1 => [ // shipping = 1
                    0 => [ // logo = 0
                        [1, 'Paid', 'Payment done online', 'bg-teal-500'],
                        [2, 'Order Picked', 'Items selected for shipping', 'bg-pink-500'],
                        [3, 'Order Packed', 'Packaged for shipment', 'bg-orange-500'],
                        [4, 'Shipped', 'Items dispatched', 'bg-blue-500'],
                        [5, 'Delivered', 'Delivered (website order)', 'bg-green-600'],
                    ],
                    1 => [ // logo = 1
                        [1, 'Paid', 'Payment done online', 'bg-teal-500'],
                        [2, 'Logo Production', 'Logo design or processing stage', 'bg-purple-500'],
                        [3, 'Dropped Off Logo', 'Items left for logo processing', 'bg-yellow-500'],
                        [4, 'Picked Up Logo', 'Items returned after processing', 'bg-yellow-500'],
                        [5, 'Ready for Delivery', 'Package is ready to go out', 'bg-amber-500'],
                        [6, 'Delivered', 'Package delivered successfully', 'bg-green-600'],
                    ],
                ],
            ],
            3 => [  // Draft
                0 => [ // shipping = 0
                    0 => [ // logo = 0
                        [1, 'Draft Order Paid', 'Payment received for draft order', 'bg-blue-500'],
                        [2, 'Draft Order Completed', 'Draft order completed', 'bg-green-500'],
                    ],
                    1 => [ // logo = 1
                        [1, 'Draft Order Paid', 'Payment received for draft order', 'bg-blue-500'],
                        [2, 'Logo Production', 'Logo design or processing stage', 'bg-purple-500'],
                        [3, 'Dropped Off Logo', 'Items left for logo processing', 'bg-yellow-500'],
                        [4, 'Picked Up Logo', 'Items returned after processing', 'bg-yellow-500'],
                        [5, 'Ready for Pickup', 'Ready for customer pickup', 'bg-orange-500'],
                        [6, 'Picked Up', 'Picked up by customer', 'bg-green-600'],
                    ],
                ],
                1 => [ // shipping = 1
                    0 => [ // logo = 0
                        [1, 'Draft Order Paid', 'Payment received for draft order', 'bg-blue-500'],
                        [2, 'Order Picked', 'Items selected for shipping', 'bg-pink-500'],
                        [3, 'Order Packed', 'Packaged for shipment', 'bg-orange-500'],
                        [4, 'Shipped', 'Items dispatched', 'bg-blue-500'],
                        [5, 'Delivered', 'Delivered (draft order)', 'bg-green-600'],
                    ],
                    1 => [ // logo = 1
                        [1, 'Draft Order Paid', 'Payment received for draft order', 'bg-blue-500'],
                        [2, 'Logo Production', 'Logo design or processing stage', 'bg-purple-500'],
                        [3, 'Dropped Off Logo', 'Items left for logo processing', 'bg-yellow-500'],
                        [4, 'Picked Up Logo', 'Items returned after processing', 'bg-yellow-500'],
                        [5, 'Ready for Delivery', 'Package is ready to go out', 'bg-amber-500'],
                        [6, 'Delivered', 'Package delivered successfully', 'bg-green-600'],
                    ],
                ],
            ],
            4 => [  // Phone
                0 => [ // shipping = 0
                    0 => [ // logo = 0
                        [1, 'Phone Order Started', 'Phone order created', 'bg-gray-500'],
                        [2, 'Order Picked', 'Items gathered for phone order', 'bg-pink-500'],
                        [3, 'Ready for Pickup', 'Awaiting pickup', 'bg-orange-500'],
                        [4, 'Picked Up', 'Picked up by customer', 'bg-lime-500'],
                    ],
                    1 => [ // logo = 1
                        [1, 'Phone Order Started', 'Phone order created', 'bg-gray-500'],
                        [2, 'Logo Production', 'Logo design or processing stage', 'bg-purple-500'],
                        [3, 'Dropped Off Logo', 'Items left for logo processing', 'bg-yellow-500'],
                        [4, 'Picked Up Logo', 'Items returned after processing', 'bg-yellow-500'],
                        [5, 'Ready for Pickup', 'Awaiting pickup', 'bg-orange-500'],
                        [6, 'Picked Up', 'Picked up by customer', 'bg-lime-500'],
                    ],
                ],
                1 => [ // shipping = 1
                    0 => [ // logo = 0
                        [1, 'Phone Order Started', 'Phone order created', 'bg-gray-500'],
                        [2, 'Order Picked', 'Items gathered for phone order', 'bg-pink-500'],
                        [3, 'Ready for Delivery', 'Ready for delivery', 'bg-amber-500'],
                        [4, 'Delivered', 'Delivered to customer', 'bg-green-600'],
                    ],
                    1 => [ // logo = 1
                        [1, 'Phone Order Started', 'Phone order created', 'bg-gray-500'],
                        [2, 'Logo Production', 'Logo design or processing stage', 'bg-purple-500'],
                        [3, 'Dropped Off Logo', 'Items left for logo processing', 'bg-yellow-500'],
                        [4, 'Picked Up Logo', 'Items returned after processing', 'bg-yellow-500'],
                        [5, 'Ready for Delivery', 'Ready for delivery', 'bg-amber-500'],
                        [6, 'Delivered', 'Delivered to customer', 'bg-green-600'],
                    ],
                ],
            ],
            5 => [  // Custom
                0 => [ // shipping = 0
                    0 => [ // logo = 0
                        [1, 'Custom Order Started', 'Custom order created', 'bg-purple-500'],
                        [2, 'Production Started', 'Manufacturing in progress', 'bg-fuchsia-500'],
                        [3, 'Order sent to Factory', 'Production request sent', 'bg-rose-500'],
                        [4, 'Goods Received', 'Production finished & returned', 'bg-emerald-500'],
                        [5, 'Contacted Customer', 'Customer has been contacted', 'bg-sky-500'],
                        [6, 'Collected', 'Custom goods collected', 'bg-violet-500'],
                    ],
                    1 => [ // logo = 1
                        [1, 'Custom Order Started', 'Custom order created', 'bg-purple-500'],
                        [2, 'Production Started', 'Manufacturing in progress', 'bg-fuchsia-500'],
                        [3, 'Order sent to Factory', 'Production request sent', 'bg-rose-500'],
                        [4, 'Goods Received', 'Production finished & returned', 'bg-emerald-500'],
                        [5, 'Contacted Customer', 'Customer has been contacted', 'bg-sky-500'],
                        [6, 'Collected', 'Custom goods collected', 'bg-violet-500'],
                    ],
                ],
                1 => [ // shipping = 1
                    0 => [ // logo = 0
                        [1, 'Custom Order Started', 'Custom order created', 'bg-purple-500'],
                        [2, 'Production Started', 'Manufacturing in progress', 'bg-fuchsia-500'],
                        [3, 'Order sent to Factory', 'Production request sent', 'bg-rose-500'],
                        [4, 'Goods Received', 'Production finished & returned', 'bg-emerald-500'],
                        [5, 'Contacted Customer', 'Customer has been contacted', 'bg-sky-500'],
                        [6, 'Delivered', 'Custom goods delivered', 'bg-violet-500'],
                    ],
                    1 => [ // logo = 1
                        [1, 'Custom Order Started', 'Custom order created', 'bg-purple-500'],
                        [2, 'Production Started', 'Manufacturing in progress', 'bg-fuchsia-500'],
                        [3, 'Order sent to Factory', 'Production request sent', 'bg-rose-500'],
                        [4, 'Goods Received', 'Production finished & returned', 'bg-emerald-500'],
                        [5, 'Contacted Customer', 'Customer has been contacted', 'bg-sky-500'],
                        [6, 'Delivered', 'Custom goods delivered', 'bg-violet-500'],
                    ],
                ],
            ],
            9 => [  // Unknown
                0 => [ // shipping = 0
                    0 => [ // logo = 0
                        [1, 'Started', 'Order placed', 'bg-gray-500'],
                        [2, 'Completed', 'Standard order completed', 'bg-green-500'],
                    ],
                    1 => [ // logo = 1
                        [1, 'Paid', 'Payment made', 'bg-blue-500'],
                        [2, 'Logo Production', 'Logo design or processing stage', 'bg-purple-500'],
                        [3, 'Dropped Off Logo', 'Items left for logo processing', 'bg-yellow-500'],
                        [4, 'Picked Up Logo', 'Items returned after processing', 'bg-yellow-500'],
                        [5, 'Ready for Pickup', 'Waiting for customer pickup', 'bg-orange-500'],
                        [6, 'Picked Up', 'Picked up by customer', 'bg-lime-500'],
                    ],
                ],
                1 => [ // shipping = 1
                    0 => [ // logo = 0
                        [1, 'Started', 'Order placed', 'bg-gray-500'],
                        [2, 'Completed', 'Standard order completed', 'bg-green-500'],
                        [3, 'Ready for Delivery', 'Package is ready to go out', 'bg-amber-500'],
                        [4, 'Delivered', 'Package delivered successfully', 'bg-green-600'],
                    ],
                    1 => [ // logo = 1
                        [1, 'Paid', 'Payment made', 'bg-blue-500'],
                        [2, 'Logo Production', 'Logo design or processing stage', 'bg-purple-500'],
                        [3, 'Dropped Off Logo', 'Items left for logo processing', 'bg-yellow-500'],
                        [4, 'Picked Up Logo', 'Items returned after processing', 'bg-yellow-500'],
                        [5, 'Ready for Delivery', 'Package is ready to go out', 'bg-amber-500'],
                        [6, 'Delivered', 'Package delivered successfully', 'bg-green-600'],
                    ],
                ],
            ],
        ];

        $statuses = [];
        $sources = [1, 2, 3, 4, 5, 9];
        $financials = [0, 1, 2, 3];
        $shippings = [0, 1];
        $logos = [0, 1];

        foreach ($sources as $src) {
            foreach ($financials as $fin) {
                foreach ($shippings as $ship) {
                    foreach ($logos as $logo) {
                        if ($fin == 1) {
                            // Financial status = Paid: Use workflow steps
                            if (isset($workflows[$src][$ship][$logo])) {
                                foreach ($workflows[$src][$ship][$logo] as $stepData) {
                                    [$step, $name, $desc, $color] = $stepData;
                                    $statuses[] = [$src, $fin, $ship, $logo, $step, $name, $desc, $color];
                                }
                            }
                        } else {
                            // Non-paid statuses (Unpaid, Partially Paid, Refunded)
                            if ($src == 3 && $fin == 0) {
                                // Draft order creation
                                $statuses[] = [
                                    $src, $fin, $ship, $logo, 1,
                                    'Draft Order Created',
                                    'Draft order created in Shopify',
                                    'bg-gray-500'
                                ];
                            } else {
                                $step = 0;
                                if ($fin == 0) {
                                    $name = 'Unpaid / Pending';
                                    $desc = 'Payment is pending';
                                    $color = 'bg-gray-500';
                                } elseif ($fin == 2) {
                                    $name = 'Partially Paid';
                                    $desc = 'Partial payment received';
                                    $color = 'bg-yellow-500';
                                } else { // $fin == 3
                                    $name = 'Refunded';
                                    $desc = 'Order has been refunded';
                                    $color = 'bg-red-500';
                                }
                                $statuses[] = [$src, $fin, $ship, $logo, $step, $name, $desc, $color];
                            }
                        }
                    }
                }
            }
        }

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