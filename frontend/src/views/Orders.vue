<template>
  <div>
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Orders</h2>
      <div class="flex items-center space-x-4">
        
        <button 
          @click="fetchOrders"
          class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition"
          :disabled="loading"
        >
          {{ loading ? 'Loading...' : 'Refresh Orders' }}
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-8">
      <p>Loading orders...</p>
    </div>

    <!-- Orders Table -->
    <div v-else class="bg-white shadow rounded-lg overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Order ID
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Customer
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Date
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Amount
              </th>
              <th scope="col" class="relative px-6 py-3">
                <span class="sr-only">Actions</span>
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                #{{ order.id }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ order.customer?.name || 'N/A' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(order.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center space-x-2">
                  <button 
                    @click="updateOrderStatus(order, 'previous')"
                    class="text-gray-500 hover:text-gray-700"
                    :disabled="isFirstStatus(order.status)"
                  >
                    &larr;
                  </button>
                  <span :class="statusClasses(order.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                    {{ order.status }}
                  </span>
                  <button 
                    @click="updateOrderStatus(order, 'next')"
                    class="text-gray-500 hover:text-gray-700"
                    :disabled="isLastStatus(order.status)"
                  >
                    &rarr;
                  </button>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ${{ order.total_price }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button class="text-indigo-600 hover:text-indigo-900 mr-3">View</button>
                <button class="text-gray-600 hover:text-gray-900">Edit</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Pagination -->
    <div class="flex justify-between items-center mt-4">
      <div class="text-sm text-gray-500">
        Showing <span class="font-medium">1</span> to <span class="font-medium">{{ orders.length }}</span> of <span class="font-medium">{{ totalOrders }}</span> results
      </div>
      <div class="flex space-x-2">
        <button 
          class="px-3 py-1 border rounded-md text-gray-700 bg-white hover:bg-gray-50"
          :disabled="!hasPreviousPage"
          @click="fetchOrders(currentPage - 1)"
        >
          Previous
        </button>
        <button 
          class="px-3 py-1 border rounded-md text-gray-700 bg-white hover:bg-gray-50"
          :disabled="!hasNextPage"
          @click="fetchOrders(currentPage + 1)"
        >
          Next
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useShopifyOrders } from '../composables/useShopifyOrders';

const shopName = ref('');
const { 
  orders, 
  fetchOrders, 
  loading, 
  updateOrderStatus: updateStatus,
  currentPage,
  totalOrders,
  hasNextPage,
  hasPreviousPage
} = useShopifyOrders();

// Define the order status flow
const statusFlow = ['Pending', 'Processing', 'Shipped', 'Completed', 'Cancelled'];

const statusClasses = (status) => {
  switch(status) {
    case 'Completed':
      return 'bg-green-100 text-green-800';
    case 'Processing':
      return 'bg-blue-100 text-blue-800';
    case 'Shipped':
      return 'bg-yellow-100 text-yellow-800';
    case 'Pending':
      return 'bg-gray-100 text-gray-800';
    case 'Cancelled':
      return 'bg-red-100 text-red-800';
    default:
      return 'bg-gray-100 text-gray-800';
  }
};

const isFirstStatus = (status) => {
  return statusFlow.indexOf(status) <= 0;
};

const isLastStatus = (status) => {
  return statusFlow.indexOf(status) >= statusFlow.length - 1;
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString();
};

const updateOrderStatus = async (order, direction) => {
  const currentIndex = statusFlow.indexOf(order.status);
  let newStatus;
  
  if (direction === 'next' && currentIndex < statusFlow.length - 1) {
    newStatus = statusFlow[currentIndex + 1];
  } else if (direction === 'previous' && currentIndex > 0) {
    newStatus = statusFlow[currentIndex - 1];
  } else {
    return;
  }

  try {
    await updateStatus(order.id, newStatus);
    // Refresh orders after update
    await fetchOrders(currentPage.value);
  } catch (error) {
    console.error('Failed to update order status:', error);
  }
};
</script>
And here's the corresponding useShopifyOrders.js composable:

javascript
// src/composables/useShopifyOrders.js
import { ref } from 'vue';
import axios from 'axios';

export function useShopifyOrders() {
  const orders = ref([]);
  const loading = ref(false);
  const currentPage = ref(1);
  const totalOrders = ref(0);
  const hasNextPage = ref(false);
  const hasPreviousPage = ref(false);

  const fetchOrders = async (page = 1) => {
    const shopName = localStorage.getItem('shop_name');    
    loading.value = true;
    try {
      const response = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/shopify/orders`, {
        params: {
          shop: shopName.value,
          page,
          limit: 10
        }
      });
      
      orders.value = response.data.orders;
      currentPage.value = page;
      totalOrders.value = response.data.total;
      hasNextPage.value = response.data.has_next;
      hasPreviousPage.value = page > 1;
    } catch (error) {
      console.error('Error fetching orders:', error);
    } finally {
      loading.value = false;
    }
  };

  const updateOrderStatus = async (orderId, newStatus) => {
    try {
      await axios.put(`${import.meta.env.VITE_API_BASE_URL}/shopify/orders/${orderId}`, {
        status: newStatus
      });
    } catch (error) {
      console.error('Error updating order status:', error);
      throw error;
    }
  };

  return {
    shopName,
    orders,
    fetchOrders,
    loading,
    updateOrderStatus,
    currentPage,
    totalOrders,
    hasNextPage,
    hasPreviousPage
  };
}