<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class ShopifyInventoryHelper {
    public static function adjustInventory($shop, $accessToken, $itemId, $locationId, $quantity)
    {
        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $accessToken,
            'Content-Type' => 'application/json',
        ])->post("https://{$shop}/admin/api/2024-10/inventory_levels/set.json", [
            'location_id' => $locationId,
            'inventory_item_id' => $itemId,
            'available' => $quantity
        ]);

        return $response->json();
    }

    public static function moveInventory($shop, $accessToken, $itemId, $fromLocationId, $toLocationId, $quantity)
    {
        // First reduce from source
        self::adjustInventory($shop, $accessToken, $itemId, $fromLocationId, -$quantity);
        
        // Then add to destination
        return self::adjustInventory($shop, $accessToken, $itemId, $toLocationId, $quantity);
    }

    public static function getStockLevel($shop, $accessToken, $itemId, $locationId)
    {
        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $accessToken
        ])->get("https://{$shop}/admin/api/2024-10/inventory_levels.json", [
            'inventory_item_ids' => $itemId,
            'location_ids' => $locationId
        ]);

        $data = $response->json();
        return $data['inventory_levels'][0]['available'] ?? 0;
    }
}