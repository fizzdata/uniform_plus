use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

Route::get('/shopify/orders', function (Request $request) {
    $shopifyUrl = "https://your-shop.myshopify.com/admin/api/2023-07/orders.json";
    $response = Http::withHeaders([
        'X-Shopify-Access-Token' => env('SHOPIFY_API_KEY'),
    ])->get($shopifyUrl);

    return $response->json();
});