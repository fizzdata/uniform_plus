<template>
  <div>
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Orders</h2>
      <div class="flex items-center space-x-4">
        <button
          @click="fetchOrders()"
          class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition"
          :disabled="loading"
        >
          {{ loading ? "Loading..." : "Refresh Orders" }}
        </button>
      </div>
    </div>

    <!-- Loading State (only for initial load) -->
    <div v-if="loading && allOrders.length === 0" class="text-center py-8">
      <p>Loading orders...</p>
    </div>

    <!-- Orders Table -->
    <div
      v-if="allOrders.length > 0"
      class="bg-white shadow rounded-lg overflow-hidden"
    >

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
              v-for="order in allOrders"
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
                <div class="status-container flex items-center gap-1">
                  <button
                    @click="moveToPreviousStatus(order)"
                    class="status-button flex items-center justify-center gap-1 text-gray-500 hover:bg-gray-100 rounded-md p-1 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    :disabled="!hasPreviousStatus(order)"
                    v-tooltip="
                      hasPreviousStatus(order)
                        ? getStatusDisplay(
                            previousStatuses[order.shopify_order_id]
                          )?.name
                        : ''
                    "
                    :aria-label="
                      hasPreviousStatus(order)
                        ? 'Move to previous status: ' +
                          getStatusDisplay(
                            previousStatuses[order.shopify_order_id]
                          )?.name
                        : 'No previous status'
                    "
                  >
                    <template
                      v-if="
                        loadingOrderId ===
                        previousStatuses[order.shopify_order_id]
                      "
                    >
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
                      <IconArrowLeft class="size-5" />
                      <span
                        class="status-description text-xs text-gray-500 sm:hidden"
                      >
                        {{
                          hasPreviousStatus(order)
                            ? getStatusDisplay(
                                previousStatuses[order.shopify_order_id]
                              )?.name
                            : ""
                        }}
                      </span>
                    </template>
                  </button>

                  <div class="status-info flex flex-col items-center">
                    <span
                      v-tooltip="
                        getStatusDisplay(order.status_id)?.description ||
                        'No description'
                      "
                      :class="`px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${
                        getStatusDisplay(order.status_id)?.color
                      } text-white`"
                    >
                      {{ getStatusDisplay(order.status_id)?.name }}
                    </span>
                    <!-- <span
                      class="status-description text-xs text-gray-500 sm:hidden"
                    >
                      {{
                        getStatusDisplay(order.status_id)?.description ||
                        "No description"
                      }}
                    </span> -->
                  </div>

                  <template
                    v-if="
                      Array.isArray(order.next_status) &&
                      order.next_status.length
                    "
                  >
                    <button
                      v-for="status in order.next_status"
                      :key="status.id || status"
                      @click="moveToNextStatus(order, status)"
                      :disabled="
                        loadingOrderId &&
                        loadingOrderId !== order.shopify_order_id
                      "
                      v-tooltip="'Move to' + ' ' + getNextStatusName(status)"
                      class="status-button flex items-center justify-center gap-1 bg-indigo-100 text-indigo-800 rounded-md hover:bg-indigo-200 transition px-3 py-1"
                      :aria-label="'Move to' + ' ' + getNextStatusName(status)"
                    >
                      <template
                        v-if="loadingOrderId === order.shopify_order_id"
                      >
                        <svg
                          class="animate-spin h-4 w-4"
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
                        <IconArrowRight class="size-5 sm:ml-1" />
                        <span
                          class="status-description text-xs text-gray-500 sm:hidden"
                        >
                          {{ getNextStatusName(status) }}
                        </span>
                      </template>
                    </button>
                  </template>
                </div>
              </td>

              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ${{ order.amount }}
              </td>
              <td
                class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
              >
                <button
                  @click="openOrderModal(order)"
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
    <!-- //load more button -->
    <div class="flex justify-center mt-6">
      <button
        v-if="nextPageInfo"
        @click="fetchOrders(nextPageInfo, true)"
        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition"
        :disabled="loadingMore"
      >
        {{ loadingMore ? "Loading..." : "Load More Orders" }}
      </button>

    </div>
    <!-- Order Details Card Modal -->
    <div v-if="showOrderModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-0 relative border border-gray-200">
        <div class="sticky top-0 z-10 bg-white px-8 pt-8 flex justify-end">
          <button @click="closeOrderModal" class="text-gray-500 hover:text-gray-700 text-xl font-bold">&times;</button>
        </div>
        <div class="max-h-[80vh] overflow-y-auto px-8 pb-8">
        <button @click="closeOrderModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-xl">&times;</button>
          <h2 class="text-2xl font-extrabold mb-6 text-indigo-700 flex items-center gap-2">
          <svg class="w-7 h-7 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7M16 3v4M8 3v4m-5 4h18"/></svg>
          Order {{ orderCard.name || orderCard.id }}
        </h2>
          <div v-if="orderDetailsLoading" class="flex justify-center items-center py-8">
          <svg class="animate-spin h-6 w-6 text-indigo-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
          <span class="text-indigo-700 font-semibold">Loading...</span>
        </div>
          <div v-else-if="orderCard">
          <!-- Customer Section -->
          <section class="mb-6 pb-4 border-b border-gray-100">
            <h3 class="font-semibold mb-3 text-lg text-gray-700 flex items-center gap-2">
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.657 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              Customer
            </h3>
            <div class="flex flex-col gap-2">
              <div class="flex justify-between">
                <span class="text-gray-500">Name:</span>
                <span class="font-medium">{{ orderCard.customer?.first_name || '—' }} {{ orderCard.customer?.last_name || '' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500">Email:</span>
                <span>{{ orderCard.customer?.email || '—' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500">Phone:</span>
                <span>{{ orderCard.customer?.phone || '—' }}</span>
              </div>
              <div v-if="orderCard.shipping_address" class="flex flex-col gap-1 mt-2 p-2 bg-gray-50 rounded">
                <span class="font-semibold text-gray-700">Shipping Address:</span>
                <span>{{ orderCard.shipping_address.name }}</span>
                <span>{{ orderCard.shipping_address.address1 }} {{ orderCard.shipping_address.address2 }}</span>
                <span>{{ orderCard.shipping_address.city }}, {{ orderCard.shipping_address.province }} {{ orderCard.shipping_address.zip }}</span>
                <span>{{ orderCard.shipping_address.country }}</span>
                <span v-if="orderCard.shipping_address.phone">Phone: {{ orderCard.shipping_address.phone }}</span>
              </div>
            </div>
          </section>

          <!-- Order Info Section -->
          <section class="mb-6 pb-4 border-b border-gray-100">
            <div class="flex flex-col gap-2">
              <div class="flex justify-between">
                <span class="text-gray-500">Date:</span>
                <span class="font-medium">{{ formatDate(orderCard.created_at) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500">Total:</span>
                <span class="font-bold text-green-600">{{ orderCard.total_price }} {{ orderCard.currency }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500">Note:</span>
                <span>{{ orderCard.note || '—' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500">Tags:</span>
                <span>{{ orderCard.tags || '—' }}</span>
              </div>
            </div>
          </section>

          <!-- Items Section -->
          <section class="mb-6 pb-4 border-b border-gray-100">
            <h3 class="font-semibold mb-3 text-lg text-gray-700 flex items-center gap-2">
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6"/></svg>
              Items
            </h3>
            <ul class="divide-y divide-gray-100">
              <li v-for="item in orderCard.line_items" :key="item.id" class="py-3 flex flex-col gap-1">
                <div class="flex justify-between items-center">
                  <span class="font-semibold text-gray-800">{{ item.title }}</span>
                  <span class="text-xs text-gray-500" v-if="item.variant_title">({{ item.variant_title }})</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span>Qty: <span class="font-medium">{{ item.quantity }}</span></span>
                  <span>Price: <span class="font-medium">{{ item.price }} {{ orderCard.currency }}</span></span>
                </div>
                <div v-if="item.vendor" class="text-xs text-gray-400">Vendor: {{ item.vendor }}</div>
              </li>
            </ul>
          </section>

          <!-- Payment, Tax & Status Section -->
          <section class="mb-2">
            <h3 class="font-semibold mb-3 text-lg text-gray-700 flex items-center gap-2">
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a5 5 0 00-10 0v2a2 2 0 00-2 2v5a2 2 0 002 2h10a2 2 0 002-2v-5a2 2 0 00-2-2z"/></svg>
              Payment, Tax & Status
            </h3>
            <div class="flex flex-col gap-2">
              <div class="flex justify-between">
                <span class="text-gray-500">Status:</span>
                <span class="font-semibold text-indigo-700">{{ orderCard.status }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500">Payment Gateway:</span>
                <span>
                  <span v-if="orderCard.payment_gateway_names && orderCard.payment_gateway_names.length">
                    {{ orderCard.payment_gateway_names.join(', ') }}
                  </span>
                  <span v-else>—</span>
                </span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500">Financial Status:</span>
                <span>{{ orderCard.financial_status || '—' }}</span>
              </div>
              <div class="flex flex-col gap-1 mt-2" v-if="orderCard.tax_lines && orderCard.tax_lines.length">
                <span class="font-semibold text-gray-700">Taxes:</span>
                <ul class="ml-2">
                  <li v-for="tax in orderCard.tax_lines" :key="tax.title" class="text-sm text-gray-600">
                    {{ tax.title }}: {{ tax.price }} {{ orderCard.currency }} ({{ (tax.rate * 100).toFixed(2) }}%)
                  </li>
                </ul>
              </div>
            </div>
          </section>
        </div>
          <div v-else class="text-center py-8 text-red-500 font-bold">No details found.</div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <!-- <div class="flex justify-between items-center mt-4">
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
    </div> -->
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

const nextPageInfo = ref(null);
const allOrders = ref([]);
const loadingMore = ref(false);

const previousStatuses = ref({});

const {
  //orders,
  //fetchOrders,
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
  const previousStatusId = previousStatuses.value[order.shopify_order_id];

  return (
    previousStatusId &&
    previousStatusId !== order.status_id && // ensure it's different
    statuses.value.some((s) => s.id === previousStatusId) // ensure it's a valid status
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
  try {
    const nextStatusId = status.to_status;
    if (!nextStatusId) return;

    loadingOrderId.value = order.shopify_order_id;

    await updateOrderStatus(order, nextStatusId, "next");
  } catch (error) {
    console.error("Error moving to next status:", error);
  } finally {
    loadingOrderId.value = null;
  }
};

const moveToPreviousStatus = async (order) => {
  try {
    // If we have a previous status stored, use it to move back
    if (previousStatuses.value[order.shopify_order_id]) {
      const previousStatusId = previousStatuses.value[order.shopify_order_id];
      loadingOrderId.value = previousStatusId;
      await updateOrderStatus(order, previousStatusId, "previous");
      // Clear the previous status after moving back
      delete previousStatuses.value[order.shopify_order_id];
    }
  } catch (error) {
    console.error("Error moving to previous status:", error);
  } finally {
    loadingOrderId.value = null;
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
      // await fetchOrders(false);
      toast.success(response?.data?.message || "Status updated successfully");

      const previousStatus = originalStatus;

      // Update the previous status for this order

      previousStatuses.value[order.shopify_order_id] = previousStatus;
      order.next_status = response?.data?.new_next_status;
    } else {
      toast.error(response?.data?.message || "Failed to update status");
    }
  } catch (error) {
    order.status_id = originalStatus; // revert
    toast.error(error?.response?.data?.error || "Failed to update status");
  }
};

const fetchOrders = async (pageInfo = null, isLoadMore = false) => {
  if (isLoadMore) {
    loadingMore.value = true;
  } else {
    loading.value = true;
  }

  try {
    const response = await axios.get(
      `${apiUrl}/api/shopify/orders?shop=${shop}`,
      {
        params: {
          ...(pageInfo ? { page_info: pageInfo } : {}),
        },
      }
    );


    const fetchedOrders = response.data.orders || [];

    if (pageInfo) {
      // Append new orders
      allOrders.value = [...allOrders.value, ...fetchedOrders];
    } else {
      // First load — replace
      allOrders.value = fetchedOrders;
    }

    nextPageInfo.value = response.data.next_page_info || null;
  } catch (error) {
    console.error("Error fetching orders:", error);
    toast.error("Failed to load orders");
  } finally {
    if (isLoadMore) {
      loadingMore.value = false;
    } else {
      loading.value = false;
    }
  }
};
onMounted(async () => {
  await fetchStatuses();
  await fetchOrders();
});

const showOrderModal = ref(false);
const orderDetailsLoading = ref(false);
const orderCard = ref({
  id: null,
  name: '',
  status: '',
  created_at: '',
  total_price: '',
  currency: '',
  customer: null,
  line_items: [],
  note: '',
  tags: '',
  order_status_url: '',
});

function mapOrderData(rawOrder) {
  return {
    id: rawOrder.id,
    name: rawOrder.name,
    status: rawOrder.fulfillment_status || rawOrder.financial_status,
    financial_status: rawOrder.financial_status,
    created_at: rawOrder.created_at,
    total_price: rawOrder.total_price,
    currency: rawOrder.currency,
    customer: rawOrder.customer || {},
    line_items: rawOrder.line_items || [],
    note: rawOrder.note,
    tags: rawOrder.tags,
    order_status_url: rawOrder.order_status_url,
    payment_gateway_names: rawOrder.payment_gateway_names || [],
    shipping_address: rawOrder.shipping_address || null,
    tax_lines: rawOrder.tax_lines || [],
  };
}

const openOrderModal = async (order) => {
  showOrderModal.value = true;
  orderDetailsLoading.value = true;
  try {
    const response = await axios.get(`${apiUrl}/api/shopify/order/${order.shopify_order_id}?shop=${shop}`);
    orderCard.value = mapOrderData(response.data.order);
  } catch (error) {
    toast.error("Failed to fetch order details");
    orderCard.value = null;
  } finally {
    orderDetailsLoading.value = false;
  }
};

const closeOrderModal = () => {
  showOrderModal.value = false;
  orderCard.value = null;
};
</script>

<style scoped>
@media (max-width: 640px) {
  .status-container {
    @apply flex-wrap gap-2;
  }
  .status-button {
    @apply px-3 py-2 text-sm min-w-[120px];
  }
  .status-info {
    @apply flex-col items-start w-full mt-1;
  }
  .status-description {
    @apply text-xs text-gray-500 mt-1;
  }
}
</style>
