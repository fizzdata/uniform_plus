<template>
  <div>
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Purchase Orders</h2>
      <div class="flex items-center space-x-4">
        <button
          @click="onClickNewOrderBtn"
          class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition cursor-pointer"
        >
          Create New Order
        </button>
        <button
          @click="fetchOrders"
          class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition cursor-pointer"
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
                Suplier
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                item ID
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                item Name
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Qty Ordered
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Qty Received
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Status
              </th>
              <th scope="col" class="relative px-6 py-3">
                <span class="sr-only">Actions</span>
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="item in orders" :key="item.id" class="hover:bg-gray-50">
              <td
                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
              >
                {{ item.supplier_name }}
              </td>
              <td
                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
              >
                #{{ item.shopify_product_id }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ getItemName(item.shopify_product_id) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ item.quantity_ordered }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ item.quantity_received }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="[
                    'px-2 py-1 rounded-full text-xs font-medium',
                    statusBadgeClass(item.status),
                  ]"
                >
                  {{ item.status }}
                </span>
              </td>
              <td
                class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium gap-4 flex"
              >
                <button
                  @click="openCreateProductModal(item)"
                  class="text-gray-600 hover:text-gray-900 cursor-pointer"
                >
                  <IconPensilSquare class="text-green-600" />
                </button>
                <button
                  @click="openReceiveModal(item)"
                  class="text-gray-600 hover:text-gray-900 cursor-pointer"
                >
                  Add Received
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

    <!-- Receive Quantity Modal -->
    <AppDialog v-model="showReceiveModal" title="Receive Inventory">
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">
          Quantity to Receive
        </label>
        <input
          type="number"
          v-model.number="receiveQuantity"
          :max="
            selectedOrder.quantity_ordered - selectedOrder.quantity_received
          "
          min="1"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-2"
        />
        <p class="mt-2 text-sm text-gray-500">
          Max receivable:
          {{ selectedOrder.quantity_ordered - selectedOrder.quantity_received }}
        </p>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">
          Select Location
        </label>
        <select
          v-model="selectedLocation"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-2"
          :disabled="loadingLocations"
        >
          <option value="">Select an item</option>
          <option v-for="item in locations" :key="item.id" :value="item.value">
            {{ item.label }}
          </option>
        </select>
        <p v-if="loadingLocations" class="mt-1 text-sm text-gray-500">
          Loading locations...
        </p>
      </div>

      <template #actions="{ close }">
        <button
          @click="close"
          class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md cursor-pointer"
        >
          Cancel
        </button>
        <button
          @click="submitReceive"
          :disabled="isSubmitting"
          class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 cursor-pointer"
        >
          {{ isSubmitting ? "Confirm Receive..." : "Confirm Receive" }}
        </button>
      </template>
    </AppDialog>

    <!-- Create New Order -->
    <AppDialog
      v-model="showCreateOrderModal"
      :title="isEdit ? 'Edit Order' : 'Create New Order'"
    >
      <div class="space-y-4">
        <!-- Supplier Name -->
        <div>
          <label class="block text-sm font-medium text-gray-700"
            >Supplier Name
          </label>
          <input
            v-model="newOrder.supplier"
            type="text"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-2"
            placeholder="Enter supplier name"
          />
        </div>

        <!-- Item Selection -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Item</label>
          <select
            v-model="newOrder.itemId"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-2"
            :disabled="loadingItems"
          >
            <option value="">Select an item</option>
            <option v-for="item in items" :key="item.id" :value="item.value">
              {{ item.label }}
            </option>
          </select>
          <p v-if="loadingItems" class="mt-1 text-sm text-gray-500">
            Loading items...
          </p>
        </div>

        <!-- Quantity -->
        <div>
          <label class="block text-sm font-medium text-gray-700"
            >Quantity
          </label>
          <input
            v-model.number="newOrder.quantity"
            type="number"
            min="1"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-2"
            placeholder="Enter quantity"
          />
        </div>

        <!-- Error Message -->
        <div v-if="errorItemMessage" class="text-red-500 text-sm">
          {{ errorItemMessage }}
        </div>
      </div>

      <!-- Actions -->
      <template #actions="{ close }">
        <button
          type="button"
          @click="close"
          class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md cursor-pointer"
        >
          Cancel
        </button>
        <button
          type="button"
          @click="createPurchaseOrder"
          :disabled="isSubmitting"
          class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50 cursor-pointer"
        >
          {{ isSubmitting ? "Creating..." : isEdit ? "Edit" : "Create Order" }}
        </button>
      </template>
    </AppDialog>
  </div>
</template>
<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import AppDialog from "@/components/Dialog.vue";
import IconPensilSquare from "@/components/icons/IconPensilSquare.vue";
import { toast } from "vue3-toastify";

// Reactive state
const orders = ref([]);
const loading = ref(false);
const errorMessage = ref("");
const errorItemMessage = ref("");
const currentPage = ref(1);
const totalOrders = ref(0);
const itemsPerPage = ref(20);
const selectedOrder = ref(null);
const receiveQuantity = ref(0);
const showReceiveModal = ref(false);
const showCreateOrderModal = ref(false);
const items = ref([]);
const isSubmitting = ref(false);
const loadingItems = ref(false);
const loadingLocations = ref(false);
const locations = ref([]);
const selectedLocation = ref("");
const isEdit = ref(false);
const apiUrl = import.meta.env.VITE_API_BASE_URL;
const shop = localStorage.getItem("shop_name") || "";

// New order form
const newOrder = ref({
  supplier: "",
  itemId: "",
  quantity: 1,
});

//Fetch items from API
const fetchItems = async () => {
  try {
    loadingItems.value = true;
    const response = await axios.get(`${apiUrl}/api/products?shop=${shop}`); // Adjust the endpoint as needed

    items.value =
      response?.data?.data?.flatMap(product =>
        product.variants.map(variant => ({
          label: `${product.title}`,
          value: variant.id,
          inventory_item_id: variant.inventory_item_id,
        }))
      ) || [];

  } catch (error) {
    console.error("Error fetching items:", error);
    errorItemMessage.value = "Failed to load items. Please try again.";
  } finally {
    loadingItems.value = false;
  }
};

const getItemName = (shopifyItemId) => {
  if (!items.value.length) return "Loading..."; // Prevent lookup on empty array

  // Find the matching product where its ID equals shopifyItemId
  const matchingItem = items.value.find(item => Number(item.value) === Number(shopifyItemId));

  return matchingItem ? matchingItem.label : "N/a";
};
const createPurchaseOrder = async () => {
  try {
    isSubmitting.value = true;
    errorItemMessage.value = "";

    // Basic validation
    if (
      !newOrder.value.supplier ||
      !newOrder.value.itemId ||
      !newOrder.value.quantity
    ) {
      errorItemMessage.value = "Please fill in all fields";
      return;
    }

const selectedVariant = items.value.find(item => item.value === newOrder.value.itemId);

    const payload = {
      supplier: newOrder.value.supplier,
      shopify_product_id: newOrder.value.itemId,
      quantity_ordered: Number(newOrder.value.quantity),
      shop: shop,
      inventory_item_id: selectedVariant ? selectedVariant.inventory_item_id : null,    };

    if (isEdit.value) {
      // TODO: Replace with your actual edit API endpoint
      const response = await axios.put(
        `${apiUrl}/purchase-order/${newOrder.value.id}`,
        payload
      );
    } else {
      const response = await axios.post(`${apiUrl}/api/purchase-order`, payload);
      if (response?.data?.success) {
        toast(response.data.message || "Order created successfully!", {
          type: "success",
        });
      }
    }

    // Optionally refresh list or add new item to list
    // orders.value.unshift(response.data);
    // await fetchOrders();

    // Reset form and close modal
    resetForm();
    showCreateOrderModal.value = false;
  } catch (error) {
    console.error("Error creating purchase order:", error);
    errorItemMessage.value =
      error.response?.data?.message ||
      "Failed to create order. Please try again.";
  } finally {
    isSubmitting.value = false;
  }
};

// Reset form
const resetForm = () => {
  newOrder.value = {
    supplier: "",
    itemId: "",
    quantity: 1,
  };
  errorMessage.value = "";
};

// Open create order modal
const onClickNewOrderBtn = async () => {
  isEdit.value = false;
  showCreateOrderModal.value = true;
  resetForm();
  if (items.value.length === 0) {
    await fetchItems();
  }
};

// Computed properties
const hasNextPage = computed(() => {
  return currentPage.value * itemsPerPage.value < totalOrders.value;
});

const hasPreviousPage = computed(() => {
  return currentPage.value > 1;
});

const statusBadgeClass = computed(() => {
  return (status) => {
    switch (status.toLowerCase()) {
      case "completed":
        return "bg-green-100 text-green-800";
      case "partial":
        return "bg-yellow-100 text-yellow-800";
      default: // pending
        return "bg-red-100 text-red-800";
    }
  };
});

// Methods
const fetchInventoryLocation = async () => {
  try {
    loadingLocations.value = true;
    const response = await axios.get(
      `${apiUrl}/api/inventory/locations?shop=${shop}`
    );

    if (response.data.success) {
      locations.value = locations.value =
        response?.data?.data?.map((item) => ({
          label: `${item.name} - ${[
            item.address1,
            item.city,
            item.province,
            item.country,
          ]
            .filter(Boolean)
            .join(" ")}`,
          value: item.id,
        })) || [];
    }
  } catch (error) {
    errorMessage.value = "Failed to load orders: " + error.message;
    console.error("Order fetch error:", error);
  } finally {
    loadingLocations.value = false;
  }
};

// Methods
const fetchOrders = async (page = 1) => {
  try {
    loading.value = true;
    const response = await axios.get(
      `${apiUrl}/api/purchase-orders?shop=${shop}`,
      {
        params: {
          page: page,
          per_page: itemsPerPage.value,
        },
      }
    );

    orders.value = response.data.data;
    totalOrders.value = response.data.total;
    currentPage.value = response.data.current_page;
  } catch (error) {
    errorMessage.value = "Failed to load orders: " + error.message;
    console.error("Order fetch error:", error);
  } finally {
    loading.value = false;
  }
};

const openCreateProductModal = (order) => {
  if (!order) return;

  isEdit.value = true;

  // Create a new object with the updated values
  newOrder.value = {
    ...newOrder.value, // Keep existing properties
    supplier: order.supplier_name || "",
    itemId: order.shopify_product_id || "",
    quantity: order.quantity_ordered || 0,
  };

  showCreateOrderModal.value = true;
};

const openReceiveModal = async (order) => {
  selectedOrder.value = order;
  receiveQuantity.value = order.quantity_ordered - order.quantity_received;
  showReceiveModal.value = true;

  if (locations.value.length === 0) {
    await fetchInventoryLocation();
  }
};

const submitReceive = async () => {
  try {
    if (receiveQuantity.value <= 0) return;

    isSubmitting.value = true;
    const response = await axios.post(
      `${apiUrl}/api/purchase-orders/${selectedOrder.value.id}/receive?shop=${shop}`,
      {
        quantity: receiveQuantity.value,
        location_id: selectedLocation.value,
      }
    );

    if (response.data.success) {
      isSubmitting.value = false;

      toast(response.data.message || "Order received successfully!", {
        type: "success",
      });
      // Update local state
      const updatedOrder = orders.value.find(
        (o) => o.id === selectedOrder.value.id
      );
      updatedOrder.received += receiveQuantity.value;
      updatedOrder.status = calculateStatus(updatedOrder);

      showReceiveModal.value = false;
    }
  } catch (error) {
    errorMessage.value = "Failed to update order: " + error.message;
    console.error("Receive error:", error);
    isSubmitting.value = false;
  }
};

const calculateStatus = (order) => {
  if (order.received >= order.ordered) return "Completed";
  if (order.received > 0) return "Partial";
  return "Pending";
};

// Lifecycle hooks
onMounted(() => {
  fetchOrders();
  fetchItems();
});
</script>
