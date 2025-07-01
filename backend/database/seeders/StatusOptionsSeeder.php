<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusOptionsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('status_options')->insert([
            // Digit 1: Source
            ['digit_number' => 1, 'code_digit' => 1, 'name' => 'web'],
            ['digit_number' => 1, 'code_digit' => 2, 'name' => 'pos'],
            ['digit_number' => 1, 'code_digit' => 3, 'name' => 'shopify_draft_order'],
            ['digit_number' => 1, 'code_digit' => 4, 'name' => 'custom'],
            ['digit_number' => 1, 'code_digit' => 5, 'name' => 'phone'],
            ['digit_number' => 1, 'code_digit' => 6, 'name' => 'in_store'],
            ['digit_number' => 1, 'code_digit' => 9, 'name' => 'unknown'],

            // Digit 2: Paid status
            ['digit_number' => 2, 'code_digit' => 1, 'name' => 'pending'],
            ['digit_number' => 2, 'code_digit' => 2, 'name' => 'paid'],
            ['digit_number' => 2, 'code_digit' => 3, 'name' => 'partially_paid'],
            ['digit_number' => 2, 'code_digit' => 4, 'name' => 'refunded'],
            ['digit_number' => 2, 'code_digit' => 5, 'name' => 'partial_refund'],
            ['digit_number' => 2, 'code_digit' => 6, 'name' => 'authorized'],
            ['digit_number' => 2, 'code_digit' => 7, 'name' => 'voided'],
            ['digit_number' => 2, 'code_digit' => 9, 'name' => 'unknown'],

            // Digit 3: Logo
            ['digit_number' => 3, 'code_digit' => 1, 'name' => 'Logo Needed'],
            ['digit_number' => 3, 'code_digit' => 2, 'name' => 'No Logo'],

            // Digit 4: Delivery method
            ['digit_number' => 4, 'code_digit' => 1, 'name' => 'Ship'],
            ['digit_number' => 4, 'code_digit' => 2, 'name' => 'delivery'],
            ['digit_number' => 4, 'code_digit' => 3, 'name' => 'pickup'],
            ['digit_number' => 4, 'code_digit' => 4, 'name' => 'in_store'],
            ['digit_number' => 4, 'code_digit' => 5, 'name' => 'Unknown'],

            // Digit 5: Workflow step
            ['digit_number' => 5, 'code_digit' => 1, 'name' => 'Order created'],
            ['digit_number' => 5, 'code_digit' => 2, 'name' => 'Payment received'],
            ['digit_number' => 5, 'code_digit' => 3, 'name' => 'Waiting for logo'],
            ['digit_number' => 5, 'code_digit' => 4, 'name' => 'Logo received'],
            ['digit_number' => 5, 'code_digit' => 5, 'name' => 'Waiting for engraving'],
            ['digit_number' => 5, 'code_digit' => 6, 'name' => 'Engraving in progress'],
            ['digit_number' => 5, 'code_digit' => 7, 'name' => 'Engraving completed'],
            ['digit_number' => 5, 'code_digit' => 8, 'name' => 'Items being collected'],
            ['digit_number' => 5, 'code_digit' => 9, 'name' => 'Packed, ready for shipping'],
            ['digit_number' => 5, 'code_digit' => 10, 'name' => 'Packed, ready for pickup'],
            ['digit_number' => 5, 'code_digit' => 11, 'name' => 'Out for delivery'],
            ['digit_number' => 5, 'code_digit' => 12, 'name' => 'Delivered'],
            ['digit_number' => 5, 'code_digit' => 13, 'name' => 'Ready for pickup (store)'],
            ['digit_number' => 5, 'code_digit' => 14, 'name' => 'Picked up by customer'],
            ['digit_number' => 5, 'code_digit' => 15, 'name' => 'Waiting for customer confirmation'],
            ['digit_number' => 5, 'code_digit' => 16, 'name' => 'Called customer, waiting for response'],
            ['digit_number' => 5, 'code_digit' => 17, 'name' => 'Customer confirmed'],
            ['digit_number' => 5, 'code_digit' => 18, 'name' => 'Waiting for item to arrive to inventory'],
            ['digit_number' => 5, 'code_digit' => 19, 'name' => 'On hold'],
            ['digit_number' => 5, 'code_digit' => 20, 'name' => 'Cancelled by customer'],
            ['digit_number' => 5, 'code_digit' => 21, 'name' => 'Returned by customer'],
            ['digit_number' => 5, 'code_digit' => 22, 'name' => 'Unknown'],
                ]);
    }
}