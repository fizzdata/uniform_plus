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

    // Encode components
    $sourceCode = match ($source) {
        'pos' => 1,
        'web' => 2,
        'shopify_draft_order' => 3,
        'custom' => 4,
        default => 9, // Unknown source
    };

    $financialCode = $financial === 'paid' ? 1 : 9;
    $shippingCode = $hasShipping ? 1 : 0;
    $logoCode = $hasLogoItem ? 1 : 0;

    // Compose full status_id
    $status = 1; // Initial status in the workflow
    return $sourceCode * 10000 + $financialCode * 1000 + $shippingCode * 100 + $logoCode * 10 + $status;
}

public static function decodeOrderStatus(int $statusId): array
{
    $statusString = str_pad((string)$statusId, 5, '0', STR_PAD_LEFT); // Ensure 5 digits

    $components = [
        'source'    => (int)substr($statusString, 0, 1),
        'financial' => (int)substr($statusString, 1, 1),
        'shipping'  => (int)substr($statusString, 2, 1),
        'logo'      => (int)substr($statusString, 3, 1),
        'step'      => (int)substr($statusString, 4, 1),
    ];

    // Map readable meanings
    $lookup = [
        'source' => [
            1 => 'POS',
            2 => 'Web',
            3 => 'Draft',
            4 => 'Custom',
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
    ];

    return [
        'source'    => $lookup['source'][$components['source']] ?? 'Unknown',
        'financial' => $lookup['financial'][$components['financial']] ?? 'Unknown',
        'shipping'  => $lookup['shipping'][$components['shipping']] ?? 'Unknown',
        'logo'      => $lookup['logo'][$components['logo']] ?? 'Unknown',
        'status_step' => $components['step'], // could link to actual status table if needed
    ];
}
}
