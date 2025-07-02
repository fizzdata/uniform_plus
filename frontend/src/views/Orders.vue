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
          {{ loading ? "Loading..." : "Refresh Orders" }}
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
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Order ID
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Customer
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Date
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Status
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Amount
              </th>
              <th scope="col" class="relative px-6 py-3">
                <span class="sr-only">Actions</span>
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr
              v-for="order in orders"
              :key="order.id"
              class="hover:bg-gray-50"
            >
              <td
                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
              >
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
                    v-tooltip="
                      hasPreviousStatus(order)
                        ? getStatusDisplay(order.previous_status_id)?.name
                        : ''
                    "
                    @click="moveToPreviousStatus(order)"
                    class="text-gray-500 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                    :disabled="!hasPreviousStatus(order)"
                  >
                    <IconArrowLeft class="size-5" />
                  </button>

                  <span
                    v-tooltip="
                      getStatusDisplay(order.status_id)?.description ||
                      'No description'
                    "
                    :title="getStatusDisplay(order.status_id).description"
                    :class="`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                      getStatusDisplay(order.status_id)?.color
                    } text-white`"
                  >
                    {{ getStatusDisplay(order.status_id)?.name }}
                  </span>

                  <button
                    v-if="
                      Array.isArray(order.next_status) &&
                      order.next_status.length
                    "
                    v-for="status in order.next_status"
                    :key="status.id || status"
                    @click="moveToNextStatus(order, status)"
                    :disabled="
                      loadingOrderId &&
                      loadingOrderId !== order.shopify_order_id
                    "
                    class="text-xs inline-flex gap-2 items-center cursor-pointer px-2 py-1 bg-indigo-100 text-indigo-800 rounded-md hover:bg-indigo-200 transition"
                    v-tooltip="'Move to' + ' ' + getNextStatusName(status)"
                  >
                    <template v-if="loadingOrderId === order.shopify_order_id">
                      <svg
                        class="animate-spin h-4 w-4 text-indigo-800"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                      >
                        <circle
                          class="opacity-25"
                          cx="12"
                          cy="12"
                          r="10"
                          stroke="currentColor"
                          stroke-width="4"
                        />
                        <path
                          class="opacity-75"
                          fill="currentColor"
                          d="M4 12a8 8 0 018-8v8z"
                        />
                      </svg>
                    </template>
                    <template v-else>
                      <IconArrowRight class="size-5" />
                    </template>
                  </button>
                </div>
              </td>

              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ${{ order.amount }}
              </td>
              <td
                class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
              >
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
        Showing <span class="font-medium">1</span> to
        <span class="font-medium">{{ orders.length }}</span> of
        <span class="font-medium">{{ totalOrders }}</span> results
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
import { ref } from "vue";
import { useShopifyOrders } from "../composables/useShopifyOrders";
import { onMounted } from "vue";
import IconArrowLeft from "@/components/icons/IconArrowLeft.vue";
import IconArrowRight from "@/components/icons/IconArrowRight.vue";
import axios from "axios";
import { toast } from "vue3-toastify";

const apiUrl = import.meta.env.VITE_API_BASE_URL;
const shop = localStorage.getItem("shop_name");
const statuses = ref([]);
const loadingOrderId = ref(null); // track the current loading order
const {
  orders,
  fetchOrders,
  loading,
  updateOrderStatus: updateStatus,
  currentPage,
  totalOrders,
  hasNextPage,
  hasPreviousPage,
} = useShopifyOrders();

// const statusFlow = [
//   "pending",
//   "processing",
//   "shipped",
//   "delivered",
//   "cancelled",
// ];
// const isFirstStatus = (status) => {
//   return statusFlow.indexOf(status) <= 0;
// };

// const isLastStatus = (status) => {
//   return statusFlow.indexOf(status) >= statusFlow.length - 1;
// };

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString();
};

// **Notification function**
// const showMessage = (msg, type) => {
//   alert(msg); // Replace with your preferred notification system (Toast, Snackbar, etc.)
// };
// const getNextStatus = (currentStatusId) => {
//   const currentIndex = statuses.value.findIndex(
//     (s) => s.s_id === currentStatusId
//   );
//   const nextStatus = statuses.value[currentIndex + 1] || null; // Prevent errors if there's no next status

//   return nextStatus ? nextStatus.name : "Final status reached"; // Default message if at the last stage
// };

const fetchStatuses = async () => {
  try {
    const response = await axios.get(`${apiUrl}/api/statuses?shop=${shop}`);
    if (response?.data) {
      statuses.value = response.data;
    }
  } catch (error) {
    console.error("Error fetching statuses:", error);
  }
};

const getStatusDisplay = (status_id) => {
  const status = statuses.value.find((s) => s.id === Number(status_id));
  return status
    ? {
        name: status.name,
        color: status.color,
        description: status.description,
      }
    : { name: "Unknown", color: "bg-gray-500", description: "No description" };
};

const openOrderWindow = (url) => {
  window.open(
    url,
    "_blank",
    "width=1050,height=600,resizable=yes,scrollbars=yes"
  );
};

// const workflow = ref({}); // Stores all possible workflows
// const showNextOptions = ref({}); // Tracks dropdown visibility per order

// Fetch workflows on mount
// const fetchWorkflows = async () => {
//   try {
//     const response = await fetch(`${apiUrl}/api/workflows?shop=${shop}`);
//     workflow.value = await response.json();
//   } catch (error) {
//     console.error("Error fetching workflows:", error);
//   }
// };

// Initialize workflow path for new orders
// const initWorkflowPath = (order) => {
//   if (!order.workflow_path) {
//     order.workflow_path = [order.status_id];
//   }
// };

// Get available next statuses for an order
// const getNextStatuses = (order) => {
//   initWorkflowPath(order);
//   const currentStatus = order.status_id;
//   const possibleNext = workflow.value[currentStatus] || [];

//   return possibleNext
//     .map((id) => statuses.value.find((s) => s.s_id === id))
//     .filter(Boolean);
// };

// New hselper functions
const hasPreviousStatus = (order) => {
  const prevStatusId = order.previous_status_id;
  return (
    prevStatusId &&
    prevStatusId !== order.status_id && // ensure it's different
    statuses.value.some((s) => s.id === prevStatusId) // ensure it's a valid status
  );
};

// const hasNextStatus = (order) => {
//   const nextStatusId = order.status_id + 1;
//   return statuses.value.some((s) => s.s_id === nextStatusId);
// };

const getNextStatusName = (order) => {
  const status = statuses.value.find((s) => s.id === Number(order?.to_status));
  return status ? status.name : "";
};

const moveToNextStatus = async (order, status) => {
  const nextStatusId = status.to_status;
  if (!nextStatusId) return;

  loadingOrderId.value = order.shopify_order_id;

  await updateOrderStatus(order, nextStatusId, "next");

  loadingOrderId.value = null;
};

const moveToPreviousStatus = async (order) => {
  const prevStatusId = order.previous_status_id;
  if (prevStatusId) {
    await updateOrderStatus(order, prevStatusId, "previous");
  }
};

const updateOrderStatus = async (order, newStatusId, status) => {
  const originalStatus = order.status_id;

  try {
    const payload = {
      shop,
      currentStatus: originalStatus,
      nextStatus: Number(newStatusId),
      direction: status,
    };

    const response = await axios.post(
      `${apiUrl}/api/orders/update-status/${order.shopify_order_id}`,
      payload
    );

    if (response?.data?.success) {
      order.status_id = newStatusId;
      await fetchOrders(false);
      toast.success(response?.data?.message || "Status updated successfully");
    } else {
      toast.error(response?.data?.message || "Failed to update status");
    }
  } catch (error) {
    order.status_id = originalStatus; // revert
    toast.error(error?.response?.data?.error || "Failed to update status");
  }
};

onMounted(async () => {
  await fetchStatuses();
  await fetchOrders();
});
</script>
