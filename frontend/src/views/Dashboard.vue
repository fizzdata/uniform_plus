<template>
  <div
    class="flex flex-col items-center justify-center min-h-screen bg-gray-50"
  >
    <div v-if="missingParam" class="text-red-500 text-xl">
      Required parameter is missing.
    </div>
    <div v-else>
      <h1 class="text-3xl font-bold text-gray-800">Welcome to the Dashboard</h1>
      <p class="mt-4 text-lg text-gray-600">Manage your shop efficiently.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";

const route = useRoute();
const missingParam = ref(false);
const apiUrl = import.meta.env.VITE_API_BASE_URL;

const installApp = async (shopName) => {
  if (!shopName) {
    missingParam.value = true;
    return;
  }

  try {
    const response = await fetch(
      `${apiUrl}/api/shopify/install?shop=${shopName}`
    );

    if (!response.ok) {
      throw new Error("Network response was not ok");
    }

    const data = await response.json();

    localStorage.setItem("shop_name", shopName); // Save shop name

    if (data.success) {
      // Already on dashboard, no need to redirect again
      console.log("Shop verified successfully.");
    } else {
      // Redirect to Shopify authorization
      window.location.href = `${apiUrl}/auth/shopify?shop=${shopName}`;
    }
  } catch (error) {
    console.error("Failed to install app:", error);
    alert("Something went wrong. Please try again.");
  }
};

onMounted(() => {
  const shopFromQuery = route.query.shop;

  if (shopFromQuery) {
    installApp(shopFromQuery);
  } else {
    missingParam.value = true;
  }
});
</script>
