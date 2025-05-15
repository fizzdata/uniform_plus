<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'shopify_order_id',
        'status_id',
        'customer_name',
        'amount',
        'currency',
        'order_date'
    ];


    public static function demo(){
    $orderData = [
    [
        "id" => 5658988970018,
        "admin_graphql_api_id" => "gid://shopify/Order/5658988970018",
        "app_id" => 1354745,
        "browser_ip" => "200.12.168.26",
        "buyer_accepts_marketing" => false,
        "cancel_reason" => null,
        "cancelled_at" => null,
        "cart_token" => null,
        "checkout_id" => 37546122674210,
        "checkout_token" => "47fa1df5f0dceb5c88884daef068a725",
        "client_details" => [
            "accept_language" => null,
            "browser_height" => null,
            "browser_ip" => "200.12.168.26",
            "browser_width" => null,
            "session_hash" => null,
            "user_agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36",
        ],
        "closed_at" => "2025-05-08T21:54:46-04:00",
        "company" => null,
        "confirmation_number" => "W5APD08MT",
        "confirmed" => true,
        "contact_email" => null,
        "created_at" => "2025-05-08T21:54:18-04:00",
        "currency" => "USD",
        "current_subtotal_price" => "7.00",
        "current_subtotal_price_set" => [
            "shop_money" => [
                "amount" => "7.00",
                "currency_code" => "USD",
            ],
            "presentment_money" => [
                "amount" => "7.00",
                "currency_code" => "USD",
            ],
        ],
        "current_total_discounts" => "0.00",
        "current_total_discounts_set" => [
            "shop_money" => [
                "amount" => "0.00",
                "currency_code" => "USD",
            ],
            "presentment_money" => [
                "amount" => "0.00",
                "currency_code" => "USD",
            ],
        ],
        "current_total_price" => "7.00",
        "current_total_price_set" => [
            "shop_money" => [
                "amount" => "7.00",
                "currency_code" => "USD",
            ],
            "presentment_money" => [
                "amount" => "7.00",
                "currency_code" => "USD",
            ],
        ],
        "customer_locale" => "en",
        "discount_codes" => [],
        "duties_included" => false,
        "email" => "",
        "financial_status" => "paid",
        "fulfillment_status" => "fulfilled",
        "location_id" => 103018364962,
        "merchant_business_entity_id" => "MTc5OTYzNzgzMjAy",
        "name" => "#1001",
        "note_attributes" => [],
        "order_number" => 1001,
        "order_status_url" => "https://juyhgjkjiuh.myshopify.com/79963783202/orders/0ada96c7dc375832c95c3cd48a24a44a/authenticate?key=617b8842e359a515d468fa8b73b6c016",
        "payment_gateway_names" => ["manual"],
        "subtotal_price_set" => [
            "shop_money" => [
                "amount" => "7.00",
                "currency_code" => "USD",
            ],
            "presentment_money" => [
                "amount" => "7.00",
                "currency_code" => "USD",
            ],
        ],
        "fulfillments" => [
            [
                "id" => 5129427255330,
                "admin_graphql_api_id" => "gid://shopify/Fulfillment/5129427255330",
                "created_at" => "2025-05-08T21:54:45-04:00",
                "location_id" => 103018364962,
                "status" => "success",
                "line_items" => [
                    [
                        "id" => 14199533731874,
                        "admin_graphql_api_id" => "gid://shopify/LineItem/14199533731874",
                        "name" => "3456",
                        "price" => "7.00",
                        "price_set" => [
                            "shop_money" => [
                                "amount" => "7.00",
                                "currency_code" => "USD",
                            ],
                            "presentment_money" => [
                                "amount" => "7.00",
                                "currency_code" => "USD",
                            ],
                        ],
                        "quantity" => 1,
                        "taxable" => true,
                        "total_discount" => "0.00",
                    ],
                ],
            ],
        ],
    ],
];

return $orderData;
    }
}
