
<!-- resources/views/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Stages</title>
    <link rel="stylesheet" href="https://unpkg.com/@shopify/polaris@10.45.0/build/esm/styles.css" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://unpkg.com/@shopify/app-bridge@3.7.2"></script>
    <script>
        var AppBridge = window['app-bridge'];
        var actions = AppBridge.actions;
        var createApp = AppBridge.createApp;
        
        var app = createApp({
            apiKey: '{{ config('shopify.api_key') }}',
            host: '{{ request()->get('host') }}'
        });
    </script>
</head>
<body>
    <div id="app">
        @yield('content')
    </div>

    <script>
        // AJAX setup for CSRF token
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Set up global CSRF headers for AJAX requests
            window.fetch = ((originalFetch) => {
                return function(url, config = {}) {
                    config.headers = config.headers || {};
                    config.headers['X-CSRF-TOKEN'] = csrfToken;
                    return originalFetch(url, config);
                };
            })(window.fetch);
        });
    </script>
</body>
</html>

