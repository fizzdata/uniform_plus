<template>
  <div>
    <h1>Welcome to Your Shopify App</h1>

    <!-- Show input field if no token exists -->
    <div v-if="!accessToken">
      <input v-model="shopName" placeholder="Enter Shopify Store URL" />
      <button @click="fetchAccessToken">Get Access Token</button>
    </div>

    <!-- Show dashboard if token is available -->
    <div v-else>
      <button @click="openModal">Open Modal</button>
      <p>Access Token: {{ accessToken }}</p>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useShopifyAppBridge } from '../composables/useShopifyAppBridge';
import { useShopifyApi } from '../composables/useShopifyApi';
import { Modal } from '@shopify/app-bridge/actions';

export default {
  setup() {
    const { appBridge, initAppBridge } = useShopifyAppBridge();
    const accessToken = ref(null);
    const shopName = ref('');
    
    const fetchAccessToken = async () => {
      if (!shopName.value) return;

      try {
        const response = await fetch(`https://uniformplus.fizzdata.com/api/shopify/token?shop=${shopName.value}`);
        const data = await response.json();
        accessToken.value = data.access_token;
      } catch (error) {
        console.error('Failed to fetch Shopify token:', error);
      }
    };

    onMounted(() => {
      // Check local storage for an existing token
      const storedToken = localStorage.getItem('shopify_access_token');
      if (storedToken) {
        accessToken.value = storedToken;
      }
    });

    const openModal = () => {
      if (!appBridge.value) return;

      const modal = Modal.create(appBridge.value, {
        title: 'Custom Modal',
        message: 'Hello, Shopify!',
      });

      modal.dispatch(Modal.Action.OPEN);
    };

    return { accessToken, shopName, fetchAccessToken, openModal };
  },
};
</script>