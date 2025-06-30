<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Top Navigation -->
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <router-link
              v-if="shopName"
              :to="`/?shop=${shopName}`"
              class="flex-shrink-0 flex items-center"
            >
              <h1 class="text-xl font-bold text-gray-800">Uniform Plus</h1>
            </router-link>
            <div v-else class="flex-shrink-0 flex items-center">
              <h1 class="text-xl font-bold text-gray-800">Uniform Plus</h1>
            </div>
          </div>
          <div class="flex space-x-4">
            <router-link
              to="/purchase-orders"
              class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
              active-class="border-indigo-500 text-gray-900"
            >
              Purchase Orders </router-link
            ><router-link
              to="/orders"
              class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
              active-class="border-indigo-500 text-gray-900"
            >
              Orders
            </router-link>
            <router-link
              to="/inventory"
              class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
              active-class="border-indigo-500 text-gray-900"
            >
              Inventory
            </router-link>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { RouterLink, RouterView } from "vue-router";
const shopName = ref(localStorage.getItem("shop_name") || null);
</script>
