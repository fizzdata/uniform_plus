<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $fillable = [
        'shopify_order_id',
        'status_id',
        'customer_name',
        'amount',
        'currency',
        'order_date'
    ];
}
