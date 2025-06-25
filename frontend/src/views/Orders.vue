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
        @click="moveToPreviousStatus(order)"
        class="text-gray-500 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
        :disabled="!hasPreviousStatus(order)"
      >
        &larr;
      </button>
      
      <span 
        :title="getStatusDisplay(order.status_id).description"
        :class="`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusDisplay(order.status_id)?.color} text-white`"
      >
        {{ getStatusDisplay(order.status_id)?.name }}
      </span>

      <button 
        v-if="hasNextStatus(order)"
        @click="moveToNextStatus(order)"
        class="text-xs px-2 py-1 bg-indigo-100 text-indigo-800 rounded-md hover:bg-indigo-200 transition"
      >
        Move to {{ getNextStatusName(order) }} →
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



// **Notification function**
const showMessage = (msg, type) => {
    alert(msg); // Replace with your preferred notification system (Toast, Snackbar, etc.)
};
const getNextStatus = (currentStatusId) => {
    const currentIndex = statuses.value.findIndex(s => s.s_id === currentStatusId);
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
    const status = statuses.value.find(s => s.s_id === Number(status_id));
    return status ? { name: status.name, color: status.color } : { name: 'Unknown', color: 'bg-gray-500' };
};

onMounted(() => {
    fetchStatuses();
});

const openOrderWindow = (url) => {
    window.open(url, "_blank", "width=1050,height=600,resizable=yes,scrollbars=yes");
};
onMounted(() => {
  fetchOrders();
});

const workflow = ref({}); // Stores all possible workflows
const showNextOptions = ref({}); // Tracks dropdown visibility per order

// Fetch workflows on mount
const fetchWorkflows = async () => {
  try {
    const response = await fetch(`${apiUrl}/api/workflows?shop=${shop}`);
    workflow.value = await response.json();
  } catch (error) {
    console.error('Error fetching workflows:', error);
  }
};

// Initialize workflow path for new orders
const initWorkflowPath = (order) => {
  if (!order.workflow_path) {
    order.workflow_path = [order.status_id];
  }
};

// Get available next statuses for an order
const getNextStatuses = (order) => {
  initWorkflowPath(order);
  const currentStatus = order.status_id;
  const possibleNext = workflow.value[currentStatus] || [];
  
  return possibleNext
    .map(id => statuses.value.find(s => s.s_id === id))
    .filter(Boolean);
};


// Update status (now handles previous moves)


// New helper functions
const hasPreviousStatus = (order) => {
  const prevStatusId = order.status_id - 1;
  return statuses.value.some(s => s.s_id === prevStatusId);
};

const hasNextStatus = (order) => {
  const nextStatusId = order.status_id + 1;
  return statuses.value.some(s => s.s_id === nextStatusId);
};

const getNextStatusName = (order) => {
  const nextStatusId = order.status_id + 1;
  const status = statuses.value.find(s => s.s_id === nextStatusId);
  return status ? status.name : '';
};

const moveToNextStatus = async (order) => {
  const nextStatusId = order.status_id + 1;
  await updateOrderStatus(order, nextStatusId);
};

const moveToPreviousStatus = async (order) => {
  const prevStatusId = order.status_id - 1;
  await updateOrderStatus(order, prevStatusId);
};

const updateOrderStatus = async (order, newStatusId) => {
  const originalStatus = order.status_id;
  
  // Optimistic UI update
  order.status_id = newStatusId;
  
  try {
    await fetch(`${apiUrl}/api/orders/update-status/${order.id}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        shop,
        direction: newStatusId > originalStatus ? 'next' : 'previous',
        new_status_id: newStatusId
      })
    });
    showMessage('Status updated successfully!', 'success');
  } catch (error) {
    // Revert on error
    order.status_id = originalStatus;
    showMessage('Failed to update status: ' + error.message, 'error');
  }
};

onMounted(() => {
  fetchStatuses();
  fetchOrders();
});

</script>

