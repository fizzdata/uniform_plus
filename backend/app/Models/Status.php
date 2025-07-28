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

    $has_phone_in_notes = isset($order['note']) && preg_match('/\bphone\b/i', $order['note']);

    if($has_phone_in_notes):
        $source = 'phone';
    endif;
    
    $sourceKey = $source . '_' . ($financial === 'paid' ? 'paid' : 'unpaid')  . ($hasLogoItem ? '_logo' : '');


switch ($sourceKey) {
    // POS
    case 'pos_paid':
        $statusName = 'Paid in Store';
        break;
    case 'pos_paid_logo':
        $statusName = 'Paid in Store with Logo';
        break;
    case 'pos_unpaid':
        $statusName = 'POS order Started';
        break;
    case 'pos_unpaid_logo':
        $statusName = 'POS order Started with Logo';
        break;

    // WEB
    case 'web_paid':
        $statusName = 'Paid on Website';
        break;
    case 'web_paid_logo':
        $statusName = 'Paid on Website with Logo';
        break;
    case 'web_unpaid':
        $statusName = 'Web order Started';
        break;
    case 'web_unpaid_logo':
        $statusName = 'Web order Started with Logo';
        break;

    // SHOPIFY DRAFT ORDER
    case 'shopify_draft_order_paid':
        $statusName = 'Draft Order Paid';
        break;
    case 'shopify_draft_order_paid_logo':
        $statusName = 'Draft Order Paid with Logo';
        break;
    case 'shopify_draft_order_unpaid':
        $statusName = 'Draft Order';
        break;
    case 'shopify_draft_order_unpaid_logo':
        $statusName = 'Draft Order with Logo';
        break;

    // PHONE (note overrides all other sources)
    case 'phone_paid':
        $statusName = 'Paid on Phone';
        break;
    case 'phone_paid_logo':
        $statusName = 'Paid on Phone with Logo';
        break;
    case 'phone_unpaid':
        $statusName = 'Phone order Started';
        break;
    case 'phone_unpaid_logo':
        $statusName = 'Phone order Started with Logo';
        break;

    default:
        $statusName = 'Custom order Started';
        break;
}


$tailwindColors = [
    'bg-red-500', 'bg-orange-500', 'bg-amber-500', 'bg-yellow-500',
    'bg-lime-500', 'bg-green-500', 'bg-emerald-500', 'bg-teal-500',
    'bg-cyan-500', 'bg-sky-500', 'bg-blue-500', 'bg-indigo-500',
    'bg-violet-500', 'bg-purple-500', 'bg-fuchsia-500', 'bg-pink-500',
    'bg-rose-500', 'bg-gray-500', 'bg-slate-500', 'bg-zinc-500'
];
$randomColor = $tailwindColors[array_rand($tailwindColors)];

$status = status::firstOrCreate(
    ['name' => $statusName],
    ['color' => $randomColor, 'description' => 'Order status for ' . $statusName]
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
