<template>
  <div>
    <h1>Welcome to Your Shopify App</h1>
    <button @click="openModal">Open Modal</button>
  </div>
</template>

<script>
import { onMounted } from 'vue';
import { useShopifyAppBridge } from '../composables/useShopifyAppBridge';
import { Modal } from '@shopify/app-bridge/actions';

export default {
  setup() {
    const { appBridge, initAppBridge } = useShopifyAppBridge();

    onMounted(() => {
      initAppBridge('your_api_key', new URLSearchParams(window.location.search).get('host'));
    });

    const openModal = () => {
      if (!appBridge.value) return;

      const modal = Modal.create(appBridge.value, {
        title: 'Custom Modal',
        message: 'Hello, Shopify!',
      });

      modal.dispatch(Modal.Action.OPEN);
    };

    return { openModal };
  },
};
</script>