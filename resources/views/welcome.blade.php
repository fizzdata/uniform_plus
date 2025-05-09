@extends('shopify-app::layouts.default')

@section('content')
    <!-- You are: (shop domain name) -->
    <p>You are: {{ $shopDomain ?? Auth::user()->name }}</p>

<ui-title-bar title="Products">
    <a href="{{ route('orders.index') }}" class="Polaris-Button">
        Secondary action
    </a>
    <a href="" class="Polaris-Button Polaris-Button--primary">
        orders
    </a>
</ui-title-bar>

@endsection