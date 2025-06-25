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
    $group = 1;

    // Extract and normalize data
    $financial = $order['financial_status'] ?? '';
    $fulfillment = $order['fulfillment_status'] ?? '';
    $shippingLines = $order['shipping_lines'] ?? [];
    $source = $order['source_name'] ?? '';
    $items = $order['line_items'] ?? [];

    $hasLogoItem = collect($items)->contains(function ($item) {
        return isset($item['title']) && stripos($item['title'], 'logo') !== false;
    });

    // Determine the group based on order type
    if (
        $financial === 'paid' &&
        $fulfillment === 'fulfilled' &&
        empty($shippingLines) &&
        $source === 'pos'
    ) {
        $group = 1; // Simple order
    } elseif ($hasLogoItem && !empty($shippingLines)) {
        $group = 2; // Logo order with delivery
    } elseif ($hasLogoItem && empty($shippingLines)) {
        $group = 3; // Logo order with pickup
    } elseif ($source === 'pos' && $financial === 'paid' && !empty($shippingLines)) {
        $group = 4; // Phone order with delivery
    } elseif ($source === 'pos' && $financial === 'paid' && empty($shippingLines)) {
        $group = 5; // Phone order with pickup
    } elseif ($source === 'web' && $financial === 'paid') {
        $group = 6; // Website order with shipping
    } elseif ($source === 'custom') {
        $group = 7; // Custom order
    }

    // Build the final status_id: group * 10 + 1
    return $group * 10 + 1;

}
}
