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
                {{ order.id }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ order.customer_name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(order.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center space-x-2">
                  <button 
                    @click="updateOrderStatus(order, 'previous')"
                    class="text-gray-500 hover:text-gray-700"
                    :disabled="isFirstStatus(order.status_id)"
                  >
                    &larr;
                  </button>
                  <span 
                  :title="getStatusDisplay(order.status_id).description"
                  :class="`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusDisplay(order.status_id)?.color} text-white`">
                      {{ getStatusDisplay(order.status_id)?.name }}
                  </span>
                  <button 
                    v-if="getNextStatus(order.status_id) !== 'Final status reached'" 
                    @click="updateOrderStatus(order, 'next')" 
                    style="font-size: 8px; padding: 2px; border-radius: 0.375rem; line-height:8px; border: 1px solid grey;" 
                >
                    Move to →<br>{{ getNextStatus(order.status_id) }}?
                </button>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ${{ order.amount }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                          <button 
                @click="openOrderWindow(order.link)" 
                class="text-gray-600 hover:text-gray-900"
            >
                View
            </button>
            
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
import { onMounted } from 'vue';

const apiUrl = import.meta.env.VITE_API_BASE_URL;
const shop = localStorage.getItem('shop_name');
const status = ref('');
const statuses = ref([]);
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

const statusFlow = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
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
    const currentIndex = statuses.value.findIndex(s => s.id === order.status_id);
    const nextIndex = direction === 'next' ? currentIndex + 1 : currentIndex - 1;
    const nextStatus = statuses.value[nextIndex] || statuses.value[currentIndex]; // Prevent invalid updates

    try {
        const response = await fetch(`${apiUrl}/api/orders/update-status/${order.id}?shop=${shop}`, {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
            // body: JSON.stringify({ status_id: nextStatus.id }), // If needed for update
        });

        if (response.ok) {
            // **Update local state instantly**
            order.status_id = nextStatus.id; 
            showMessage(`Status updated to "${nextStatus.name}"`, 'success'); 
        } else {
            console.error('Error updating status:', await response.json());
            showMessage('Failed to update order status. Please try again.', 'error');
        }
    } catch (error) {
        console.error('Request failed:', error);
        showMessage('Network error. Please check your connection.', 'error');
    }
};

// **Notification function**
const showMessage = (msg, type) => {
    alert(msg); // Replace with your preferred notification system (Toast, Snackbar, etc.)
};
const getNextStatus = (currentStatusId) => {
    const currentIndex = statuses.value.findIndex(s => s.id === currentStatusId);
    const nextStatus = statuses.value[currentIndex + 1] || null; // Prevent errors if there's no next status

    return nextStatus ? nextStatus.name : "Final status reached"; // Default message if at the last stage
};

const fetchStatuses = async () => {
    try {
        const response = await fetch(`${apiUrl}/api/statuses?shop=${shop}`, {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        });
        statuses.value = await response.json();
    } catch (error) {
        console.error('Error fetching statuses:', error);
    }
};

const getStatusDisplay = (status_id) => {
    const status = statuses.value.find(s => s.id === Number(status_id));
    return status ? { name: status.name, color: status.color } : { name: 'Unknown', color: 'bg-gray-500' };
};

onMounted(() => {
    fetchStatuses();
});

const openOrderWindow = (url) => {
    window.open(url, "_blank", "width=1050,height=600,resizable=yes,scrollbars=yes");
};


</script>

