<template>
  <div>
    <h1>Install Shopify App</h1>
    <input v-model="shopName" placeholder="Enter Shopify Store URL" />
    <button @click="installApp">Install</button>
  </div>
</template>

<script>
import { ref } from 'vue';

export default {
  setup() {
    const shopName = ref('');
    const apiUrl = import.meta.env.VITE_API_BASE_URL;

    const installApp = async () => {
      if (!shopName.value) return;

      try {
        const response = await fetch(`${apiUrl}/api/shopify/install?shop=${shopName.value}`);
        const data = await response.json();

        if (data.success) {
          localStorage.setItem('shop_name', shopName.value); // Save shop name
          window.location.href = '/'; // Redirect to dashboard
        }
        else {
        
        // Redirect to Shopify authorization URL
        window.location.href = `${apiUrl}/auth/shopify?shop=${shopName.value}`;

        }
      } catch (error) {
        console.error('Failed to install app:', error);
      }
    };

    return { shopName, installApp };
  },
};
</script>