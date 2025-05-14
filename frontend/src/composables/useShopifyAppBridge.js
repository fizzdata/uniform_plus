import { ref } from 'vue';
import createApp from '@shopify/app-bridge';

export function useShopifyAppBridge() {
  const appBridge = ref(null);

  const initAppBridge = (apiKey, host) => {
    appBridge.value = createApp({
      apiKey,
      host: new URLSearchParams(window.location.search).get('host'),
    });
  };

  return { appBridge, initAppBridge };
}