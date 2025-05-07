@extends('app')

@section('content')
<div class="Polaris-Page">
    <div class="Polaris-Page__Header">
        <div class="Polaris-Page__Title">
            <h1 class="Polaris-Header-Title">Install Shopify App</h1>
        </div>
    </div>
    
    <div class="Polaris-Page__Content">
        <div class="Polaris-Card">
            <div class="Polaris-Card__Section">
                <div class="Polaris-FormLayout">
                    <div class="Polaris-FormLayout__Item">
                        @if(session('error'))
                            <div class="Polaris-Banner Polaris-Banner--statusCritical" tabindex="0" role="status" aria-live="polite">
                                <div class="Polaris-Banner__Content">
                                    <p>{{ session('error') }}</p>
                                </div>
                            </div>
                        @endif
                        
                        <form method="GET" action="{{ route('shopify.install') }}">
                            <div class="Polaris-FormLayout">
                                <div class="Polaris-FormLayout__Item">
                                    <div class="Polaris-TextField">
                                        <input id="shop" name="shop" class="Polaris-TextField__Input" type="text" placeholder="example.myshopify.com" aria-labelledby="ShopDomainLabel" aria-invalid="false" value="">
                                        <div class="Polaris-TextField__Backdrop"></div>
                                    </div>
                                    <div class="Polaris-Label" id="ShopDomainLabel">
                                        <label for="shop">Shop Domain</label>
                                    </div>
                                </div>
                                
                                <div class="Polaris-FormLayout__Item">
                                    <button type="submit" class="Polaris-Button Polaris-Button--primary">
                                        <span class="Polaris-Button__Content">Connect</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection