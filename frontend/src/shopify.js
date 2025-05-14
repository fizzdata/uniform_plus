import createApp from '@shopify/app-bridge';

const shopifyApp = createApp({
  apiKey: 'your_api_key',  // Replace with your Shopify app API key
  host: new URLSearchParams(window.location.search).get('host'),
});

export default shopifyApp;