<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    public static function assign($order)
{

    //1. simple order
    //2. logo order with delivery
    //3. logo order with pickup
    //4. phone order with delivery
    //5. phone order with pickup
    //6. website order with shipping 
    //7. custom order


    // Default fallback group

    $source = strtolower($order['source_name'] ?? '');
    $financial = strtolower($order['financial_status'] ?? '');
    $shippingLines = $order['shipping_lines'] ?? [];
    $items = $order['line_items'] ?? [];

    $hasShipping = !empty($shippingLines);
    $hasLogoItem = collect($items)->contains(function ($item) {
        return isset($item['title']) && stripos($item['title'], 'logo') !== false;
    });

$build_1 = DB::table('status_options')->where('digit_number', 1)->where('name', $source)->value('code_digit');

$build_2 = DB::table('status_options')->where('digit_number', 2)->where('name', $financial)->value('code_digit');

$build_3 = $hasLogoItem ? DB::table('status_options')->where('digit_number', 3)->where('name', 'Logo Needed')->value('code_digit') : DB::table('status_options')->where('digit_number', 3)->where('name', 'No Logo')->value('code_digit');

$build_4 = $hasShipping ? DB::table('status_options')->where('digit_number', 4)->where('name', 'Ship')->value('code_digit') : DB::table('status_options')->where('digit_number', 4)->where('name', 'Pickup')->value('code_digit');  


    // Compose full status_id
    $status = 1; // Initial status in the workflow
    return $build_1 * 10000 + $build_2 * 1000 + $build_3 * 100 + $build_4 * 10 + $status;
}

    public static function getNextStatus($statusId)
    {
        // Fetch the next status based on the current status ID
        $nextStatus = DB::table('to_status')
            ->where('code_digit', $statusId % 10 + 1) // Increment the last digit to get the next status
            ->value('id');

        return $nextStatus;
    }

}
