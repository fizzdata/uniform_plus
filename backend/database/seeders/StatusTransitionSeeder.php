<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusTransitionSeeder extends Seeder
{
    public function run(): void
    {
        $transitions = [];

        // Define sources
        $sources = [
            1 => 'web',
            2 => 'pos',
            3 => 'shopify_draft_order',
            4 => 'custom',
            5 => 'phone',
            6 => 'in_store',
        ];

        // Paid statuses
        $paids = [
            1 => 'pending',
            2 => 'paid',
        ];

        // Logo
        $logos = [
            1 => 'Logo Needed',
            2 => 'No Logo',
        ];

        // Delivery methods
        $deliveries = [
            1 => 'Ship',
            3 => 'Pickup',
        ];

        // Workflow steps
        $workflowSteps = [
            1 => 'Order created',
            2 => 'Payment received',
            3 => 'Waiting for logo',
            4 => 'Logo received',
            5 => 'Waiting for engraving',
            6 => 'Engraving in progress',
            7 => 'Engraving completed',
            8 => 'Items being collected',
            9 => 'Packed, ready for shipping',
            10 => 'Packed, ready for pickup',
            11 => 'Out for delivery',
            12 => 'Delivered',
            13 => 'Ready for pickup (store)',
            14 => 'Picked up by customer',
        ];

        // Build combinations
        foreach ($sources as $sourceDigit => $sourceName) {
            foreach ($paids as $paidDigit => $paidName) {
                foreach ($logos as $logoDigit => $logoName) {
                    foreach ($deliveries as $deliveryDigit => $deliveryName) {

                        // Initial status: order created
                        $fromStatus = $sourceDigit * 10000 + $paidDigit * 1000 + $logoDigit * 100 + $deliveryDigit * 10 + 1;
                        $step = 1;

                        // 1 → 2 (Payment received)
                        $toStatus = $sourceDigit * 10000 + 2 * 1000 + $logoDigit * 100 + $deliveryDigit * 10 + 2;
                        $transitions[] = [
                            'from_status' => $fromStatus,
                            'to_status' => $toStatus,
                            'description' => 'Payment received'
                        ];
                        $fromStatus = $toStatus;
                        $step = 2;

                        // If logo needed: 2 → 3 → 4 → 5 → 6 → 7
                        if ($logoDigit === 1) {
                            $nextSteps = [3, 4, 5, 6, 7];
                            foreach ($nextSteps as $nextStep) {
                                $toStatus = $sourceDigit * 10000 + 2 * 1000 + $logoDigit * 100 + $deliveryDigit * 10 + $nextStep;
                                $transitions[] = [
                                    'from_status' => $fromStatus,
                                    'to_status' => $toStatus,
                                    'description' => "Move to {$workflowSteps[$nextStep]}"
                                ];
                                $fromStatus = $toStatus;
                            }
                        }

                        // Next: wait for inventory → collect → pack
                        $nextSteps = [8, 9];
                        if ($deliveryDigit === 3) {
                            $nextSteps = [8, 10];
                        }

                        foreach ($nextSteps as $nextStep) {
                            $toStatus = $sourceDigit * 10000 + 2 * 1000 + $logoDigit * 100 + $deliveryDigit * 10 + $nextStep;
                            $transitions[] = [
                                'from_status' => $fromStatus,
                                'to_status' => $toStatus,
                                'description' => "Move to {$workflowSteps[$nextStep]}"
                            ];
                            $fromStatus = $toStatus;
                        }

                        // Delivery steps
                        if ($deliveryDigit === 1) {
                            // Shipping flow
                            $toStatus = $sourceDigit * 10000 + 2 * 1000 + $logoDigit * 100 + $deliveryDigit * 10 + 11;
                            $transitions[] = [
                                'from_status' => $fromStatus,
                                'to_status' => $toStatus,
                                'description' => 'Out for delivery'
                            ];
                            $fromStatus = $toStatus;

                            $toStatus = $sourceDigit * 10000 + 2 * 1000 + $logoDigit * 100 + $deliveryDigit * 10 + 12;
                            $transitions[] = [
                                'from_status' => $fromStatus,
                                'to_status' => $toStatus,
                                'description' => 'Delivered'
                            ];
                        } elseif ($deliveryDigit === 3) {
                            // Pickup flow
                            $toStatus = $sourceDigit * 10000 + 2 * 1000 + $logoDigit * 100 + $deliveryDigit * 10 + 13;
                            $transitions[] = [
                                'from_status' => $fromStatus,
                                'to_status' => $toStatus,
                                'description' => 'Ready for pickup (store)'
                            ];
                            $fromStatus = $toStatus;

                            $toStatus = $sourceDigit * 10000 + 2 * 1000 + $logoDigit * 100 + $deliveryDigit * 10 + 14;
                            $transitions[] = [
                                'from_status' => $fromStatus,
                                'to_status' => $toStatus,
                                'description' => 'Picked up by customer'
                            ];
                        }
                    }
                }
            }
        }

        // Insert all
        DB::table('order_status_transitions')->insert($transitions);
    }
}
