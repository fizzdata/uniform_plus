<template>
  <div
    class="flex flex-col items-center justify-center min-h-screen bg-gray-50"
  >
    <h1 class="text-3xl font-bold text-gray-800">Welcome to the Dashboard</h1>
    <p class="mt-4 text-lg text-gray-600">Manage your shop efficiently.</p>
  </div>
</template>

<script setup>
import { onMounted } from "vue";
import { useRoute } from "vue-router";

const route = useRoute();

onMounted(() => {
  const shopFromQuery = route.query.shop;

  if (shopFromQuery) {
    // Always update the local storage with the new shop
    localStorage.setItem("shop_name", shopFromQuery);
  }
});
</script>
