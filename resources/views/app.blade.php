<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Laravel Shopify App') }}</title>
    
    <!-- Polaris Styles -->
    <link rel="stylesheet" href="https://unpkg.com/@shopify/polaris@10.49.1/build/esm/styles.css" />
    
    <!-- Custom Styles -->
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "San Francisco", "Segoe UI", Roboto, "Helvetica Neue", sans-serif;
        }
        
        .app-container {
            padding: 20px;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="app-container">
        @yield('content')
    </div>
    
    <!-- Scripts -->
    @if(session('shopify_domain') && session('shopify_token'))
    <script src="https://unpkg.com/@shopify/app-bridge@3"></script>
    <script>
        var AppBridge = window['app-bridge'];
        var actions = AppBridge.actions;
        var createApp = AppBridge.createApp;
        
        var app = createApp({
            apiKey: '{{ env('SHOPIFY_API_KEY') }}',
            shopOrigin: '{{ session('shopify_domain') }}',
            forceRedirect: true
        });
    </script>
    @endif
    
    @stack('scripts')
</body>
</html>