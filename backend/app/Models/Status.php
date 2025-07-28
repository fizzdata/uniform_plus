<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{

    protected $fillable = ['name', 'color', 'description'];

    public static function assign($order)
{

    $source = strtolower($order['source_name'] ?? '');
    $financial = strtolower($order['financial_status'] ?? '');
    // $shippingLines = $order['shipping_lines'] ?? [];
    $items = $order['line_items'] ?? [];

    // $hasShipping = !empty($shippingLines);
     $hasLogoItem = collect($items)->contains(function ($item) {
         return isset($item['title']) && stripos($item['title'], 'logo') !== false;
     });

    $has_phone_in_notes = isset($order['note']) && stripos($order['note'], 'phone') !== false;
    
    $sourceKey = $source . '_' . ($financial === 'paid' ? 'paid' : 'unpaid')  . ($hasLogoItem ? '_logo' : '') . ($has_phone_in_notes ? '_phone' : '');


switch ($sourceKey) {
    // POS
    case 'pos_paid':
        $statusName = 'Paid in Store';
        break;
    case 'pos_paid_phone':
        $statusName = 'Paid in Store (Phone)';
        break;
    case 'pos_paid_logo':
        $statusName = 'Paid in Store with Logo';
        break;
    case 'pos_paid_logo_phone':
        $statusName = 'Paid in Store with Logo (Phone)';
        break;
    case 'pos_unpaid':
        $statusName = 'POS order Started';
        break;
    case 'pos_unpaid_phone':
        $statusName = 'POS order Started (Phone)';
        break;
    case 'pos_unpaid_logo':
        $statusName = 'POS order Started with Logo';
        break;
    case 'pos_unpaid_logo_phone':
        $statusName = 'POS order Started with Logo (Phone)';
        break;

    // WEB
    case 'web_paid':
        $statusName = 'Paid on Website';
        break;
    case 'web_paid_phone':
        $statusName = 'Paid on Phone';
        break;
    case 'web_paid_logo':
        $statusName = 'Paid on Website with Logo';
        break;
    case 'web_paid_logo_phone':
        $statusName = 'Paid on Phone with Logo';
        break;
    case 'web_unpaid':
        $statusName = 'Web order Started';
        break;
    case 'web_unpaid_phone':
        $statusName = 'Phone order Started';
        break;
    case 'web_unpaid_logo':
        $statusName = 'Web order Started with Logo';
        break;
    case 'web_unpaid_logo_phone':
        $statusName = 'Phone order Started with Logo';
        break;

    // SHOPIFY DRAFT ORDER
    case 'shopify_draft_order_paid':
        $statusName = 'Paid on Phone';
        break;
    case 'shopify_draft_order_paid_phone':
        $statusName = 'Paid on Phone (Draft)';
        break;
    case 'shopify_draft_order_paid_logo':
        $statusName = 'Draft Order with Logo';
        break;
    case 'shopify_draft_order_paid_logo_phone':
        $statusName = 'Draft Order with Logo (Phone)';
        break;
    case 'shopify_draft_order_unpaid':
        $statusName = 'Draft Order';
        break;
    case 'shopify_draft_order_unpaid_phone':
        $statusName = 'Draft Order (Phone)';
        break;
    case 'shopify_draft_order_unpaid_logo':
        $statusName = 'Draft Order with Logo';
        break;
    case 'shopify_draft_order_unpaid_logo_phone':
        $statusName = 'Draft Order with Logo (Phone)';
        break;

    default:
        $statusName = 'Custom order Started';
        break;
}


    $status = status::firstOrCreate(
        ['name' => $statusName],
    ['color' => 'bg-teal-500', 'description' => 'Order status for ' . $statusName]
);


    // Fetch the status ID
    
    $statusId = $status->id;

    // If status ID is found, return it

if($statusId):

    return $statusId;
    
else:
    return new \Exception("Status not found: $statusName");
endif;


}

    public static function getNextStatus($statusId)
    {
        // Fetch the next status based on the current status ID
        $nextStatus = DB::table('order_status_transitions')
            ->where('from_status', $statusId) 
            ->get('id');

        return $nextStatus;
    }

}
