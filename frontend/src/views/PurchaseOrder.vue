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
    <div v-if="loading" class="h-full flex items-center justify-center pt-10">
      <BaseSpinner :show-loader="loading" size="md" />
    </div>

    <!-- Orders Table -->
    <div v-else class="space-y-4">
      <div
        v-for="(items, supplier) in groupedOrders"
        :key="supplier"
        class="border rounded-lg overflow-auto"
      >
        <!-- Group Header -->
        <div
          class="bg-gray-100 px-4 py-2 cursor-pointer flex justify-between items-center rounded-2xl"
          @click="toggleGroup(supplier)"
        >
          <div class="inline-flex gap-4 items-end">
            <span class="font-semibold capitalize text-sm">
              Product: {{ selectedProduct(supplier)?.label }}
            </span>
            <div class="text-xs text-gray-600">
              <span class="mr-4">
                Ordered:

                {{ formatNumber(productGroupTotals[supplier]?.totalOrdered) }}
              </span>
              <span>
                Received:
                {{ formatNumber(productGroupTotals[supplier]?.totalReceived) }}
              </span>
            </div>
          </div>
          <div class="flex justify-between gap-3 items-center">
            <button
              @click.stop="openReceiveModal(items, false)"
              class="text-gray-600 hover:text-gray-900 cursor-pointer bg-blue-100 px-3 py-1 rounded-md"
            >
              Receive All
            </button>
            <span class="text-gray-500 text-2xl">
              {{ expandedGroups[supplier] ? "−" : "+" }}
            </span>
          </div>
        </div>

        <!-- Group Body -->
        <table
          v-if="expandedGroups[supplier]"
          class="min-w-full divide-y divide-gray-200 text-sm overflow-scroll"
        >
          <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
            <tr>
              <th class="px-4 py-2 text-left">Order ID</th>
              <th class="px-4 py-2 text-left">Supplier</th>
              <th class="px-4 py-2 text-left">Variant Name</th>
              <th class="px-4 py-2 text-left">Barcode</th>
              <th class="px-4 py-2 text-left">Qty Ordered</th>
              <th class="px-4 py-2 text-left">Qty Received</th>
              <th class="px-4 py-2 text-left">Status</th>
              <th class="px-4 py-2 text-left">Paid</th>
              <th scope="col" class="relative px-6 py-3">
                <span class="sr-only">Actions</span>
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 rounded-bl-2xl">
            <tr
              v-for="item in items?.items"
              :key="item.id"
              class="hover:bg-gray-50"
            >
              <td class="px-4 py-2">{{ item.id }}</td>
              <td class="px-4 py-2">{{ item.supplier_name }}</td>
              <td class="px-4 py-2">
                {{ selectedItem(item.inventory_item_id)?.variant_name }}
              </td>
              <td class="px-4 py-2">
                <span v-if="selectedItem(item.inventory_item_id)?.barcode">
                  #{{ selectedItem(item.inventory_item_id)?.barcode }}
                </span>
                <span v-else>N/a</span>
              </td>
              <td class="px-4 py-2">{{ item.quantity_ordered }}</td>
              <td class="px-4 py-2">{{ item.quantity_received }}</td>
              <td class="px-4 py-2 capitalize">
                <span
                  :class="[
                    'px-2 py-1 rounded-full text-xs font-medium capitalize',
                    statusBadgeClass(item.status),
                  ]"
                >
                  {{ item.status }}
                </span>
              </td>
              <td class="px-4 py-2">{{ item.paid ? "Yes" : "No" }}</td>
              <td
                class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium gap-4 flex"
              >
                <button
                  v-tooltip="'Undo Recieved'"
                  @click="onClickUndoRecieved(item)"
                  class="text-gray-600 hover:text-gray-900 cursor-pointer"
                >
                  <IconArrowUturn class="text-blue-600" />
                </button>
                <button
                  v-tooltip="'Add Recieve'"
                  @click="openReceiveModal(item, true)"
                  class="text-gray-600 hover:text-gray-900 cursor-pointer"
                >
                  <IconPlusCircle class="text-blue-600" />
                </button>
                <button
                  v-tooltip="'Edit'"
                  @click="openCreateProductModal(item)"
                  class="text-gray-600 hover:text-gray-900 cursor-pointer"
                >
                  <IconPensilSquare class="text-green-600" />
                </button>
                <button
                  v-tooltip="'Delete'"
                  @click="openDeleteProductModal(item)"
                  class="text-gray-600 hover:text-gray-900 cursor-pointer"
                >
                  <IconDelete class="text-red-600" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-if="!Object.keys(groupedOrders).length">
        <h4 class="text-center italic">
          No orders to show. Please create a new order.
        </h4>
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
          class="px-3 py-1 border rounded-md text-gray-700 bg-white hover:bg-gray-50 cursor-pointer disabled:pointer-events-none"
          :disabled="!hasPreviousPage"
          @click="fetchOrders(currentPage - 1)"
        >
          Previous
        </button>
        <button
          class="px-3 py-1 border rounded-md text-gray-700 bg-white hover:bg-gray-50 cursor-pointer disabled:pointer-events-none"
          :disabled="!hasNextPage"
          @click="fetchOrders(currentPage + 1)"
        >
          Next
        </button>
      </div>
    </div>

    <!-- Receive Quantity Modal -->
    <AppDialog
      v-model="showReceiveModal"
      title="Receive Inventory"
      width="max-w-3xl"
    >
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">
          Select Location
        </label>
        <select
          v-model="selectedLocation"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-2"
          :disabled="loadingLocations"
          @change="locationError = ''"
        >
          <option value="">Select a location</option>
          <option v-for="item in locations" :key="item.id" :value="item.value">
            {{ item.label }}
          </option>
        </select>
        <p v-if="loadingLocations" class="mt-1 text-sm text-gray-500">
          Loading locations...
        </p>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th
                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Variant
              </th>
              <th
                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Ordered
              </th>
              <th
                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Received
              </th>
              <th
                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                To Receive
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="item in selectedOrder.items" :key="item.id">
              <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                {{
                  selectedItem(item.inventory_item_id)?.variant_name || "N/A"
                }}
                <span
                  v-if="selectedItem(item.inventory_item_id)?.barcode"
                  class="text-gray-400 ml-2"
                >
                  #{{ selectedItem(item.inventory_item_id)?.barcode }}
                </span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                {{ item.quantity_ordered }}
              </td>
              <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                {{ item.quantity_received }}
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <input
                  v-model.number="item.quantity_to_receive"
                  type="number"
                  :min="0"
                  :max="item.quantity_ordered - item.quantity_received"
                  class="shadow-sm focus:ring-indigo-500 p-1 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                />
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <p v-if="locationError" class="mt-1 text-sm text-red-600">
        {{ locationError }}
      </p>

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
          class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 cursor-pointer disabled:bg-indigo-400 disabled:pointer-events-none"
        >
          {{ isSubmitting ? "Confirm Receive..." : "Confirm Receive" }}
        </button>
      </template>
    </AppDialog>

    <!-- Create New Order -->
    <AppDialog
      v-model="showCreateOrderModal"
      :title="isEdit ? 'Edit Order' : 'Create New Order'"
      width="max-w-3xl"
      @close="resetForm"
    >
      <div class="space-y-4">
        <!-- Supplier Name -->
        <div>
          <label class="block text-sm font-medium text-gray-700"
            >Supplier Name</label
          >
          <input
            v-model="newOrder.supplier"
            type="text"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-2"
            placeholder="Enter supplier name"
          />
        </div>

        <!-- Product Selection -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Product</label>
          <select
            v-model="newOrder.productId"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-2"
            :disabled="loadingItems || isEdit"
            @change="loadVariants"
          >
            <option value="">Select a product</option>
            <option
              v-for="product in products"
              :key="product.id"
              :value="product.id"
            >
              {{ product.title }}
            </option>
          </select>
          <p v-if="loadingItems" class="mt-1 text-sm text-gray-500">
            Loading products...
          </p>
        </div>

        <!-- Variants Table -->
        <div v-if="newOrder.variants.length > 0" class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th
                  class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Variant
                </th>
                <th
                  class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Order Quantity
                </th>
                <th
                  class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  <div v-if="!isEdit" class="flex items-center">
                    <span>Bulk Set</span>
                    <input
                      v-model.number="bulkQuantity"
                      type="number"
                      min="0"
                      class="ml-2 w-20 px-2 py-1 border rounded text-sm"
                      placeholder="Qty"
                    />
                    <button
                      @click="applyBulkQuantity"
                      class="ml-2 px-2 py-1 bg-indigo-100 text-indigo-700 text-xs rounded hover:bg-indigo-200 cursor-pointer"
                    >
                      Apply
                    </button>
                  </div>
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="variant in newOrder.variants" :key="variant.id">
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                  {{ variant.title }}
                  <span v-if="variant.barcode" class="text-gray-400 ml-2"
                    >#{{ variant.barcode }}
                  </span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                  <input
                    v-model.number="variant.quantity_ordered"
                    type="number"
                    min="0"
                    class="shadow-sm p-1 bg-gray-100 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                  />
                </td>
                <td class="px-4 py-3 text-center">
                  <button
                    @click="variant.quantity_ordered = bulkQuantity"
                    class="text-indigo-600 hover:text-indigo-900 cursor-pointer"
                  >
                    Apply
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Paid or unpaid toggle -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Paid </label>
          <div class="mt-1 relative flex items-center">
            <Switch
              v-model="newOrder.paid"
              :class="newOrder.paid ? 'bg-indigo-600' : 'bg-gray-200'"
              class="relative inline-flex h-6 w-11 items-center rounded-full"
            >
              <span class="sr-only">Toggle paid status</span>
              <span
                :class="newOrder.paid ? 'translate-x-6' : 'translate-x-1'"
                class="inline-block h-4 w-4 transform rounded-full bg-white transition"
              />
            </Switch>
          </div>
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
          :disabled="isSubmitting || !hasSelectedVariants"
          class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50 cursor-pointer"
        >
          {{
            isSubmitting
              ? isEdit
                ? "Editing..."
                : "Creating..."
              : isEdit
              ? "Edit"
              : "Create Order"
          }}
        </button>
      </template>
    </AppDialog>

    <!-- Confirm Delete Modal -->
    <AppDialog v-model="showDeleteModal" :title="'Confirm Delete'">
      <div class="space-y-4">
        <p>Are you sure you want to delete this order?</p>
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
          @click="confirmDelete"
          :disabled="isSubmitting"
          class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 disabled:opacity-50 cursor-pointer"
        >
          Delete Order
        </button>
      </template>
    </AppDialog>
  </div>
</template>
<script setup>
import { ref, onMounted, computed, watch } from "vue";
import axios from "axios";
import AppDialog from "@/components/Dialog.vue";
import IconPensilSquare from "@/components/icons/IconPensilSquare.vue";
import { toast } from "vue3-toastify";
import IconDelete from "@/components/icons/IconDelete.vue";
import IconPlusCircle from "@/components/icons/IconPlusCircle.vue";
import BaseSpinner from "@/components/BaseSpinner.vue";
import { Switch } from "@headlessui/vue";
import IconArrowUturn from "@/components/icons/IconArrowUturn.vue";

// Reactive state
const orders = ref([]);
const loading = ref(false);
const errorMessage = ref("");
const errorItemMessage = ref("");
const currentPage = ref(1);
const totalOrders = ref(0);
const itemsPerPage = ref(500);
const showReceiveModal = ref(false);
const showCreateOrderModal = ref(false);
const products = ref([]); // Renamed from items
const bulkQuantity = ref(0);
const selectedOrder = ref({
  items: [], // Now stores multiple items
});
const isSubmitting = ref(false);
const loadingItems = ref(false);
const loadingLocations = ref(false);
const locations = ref([]);
const selectedLocation = ref("");
const isEdit = ref(false);
const apiUrl = import.meta.env.VITE_API_BASE_URL;
const shop = localStorage.getItem("shop_name") || "";
const variants = ref([]);
const allProductsItems = ref([]);
const showDeleteModal = ref(false);
const selectRecord = ref("");
const locationError = ref("");

// Handle group expand/collapse state
const expandedGroups = ref({});

function toggleGroup(key) {
  expandedGroups.value[key] = !expandedGroups.value[key];
}

const groupedOrders = computed(() => {
  const result = {};

  for (const order of orders.value) {
    const shopifyId = order.shopify_product_id || "unknown_id";
    const supplierName = order.supplier_name || "Unknown Supplier";

    if (!result[shopifyId]) {
      result[shopifyId] = {
        shopify_product_id: shopifyId,
        supplier_name: supplierName,
        items: [],
      };
    }

    result[shopifyId].items.push(order);
  }

  return result;
});

// Calculate qty order and recieved
const productGroupTotals = computed(() => {
  const totals = {};
  for (const [supplier, group] of Object.entries(groupedOrders.value)) {
    totals[supplier] = {
      totalOrdered: 0,
      totalReceived: 0,
    };

    if (group?.items) {
      group.items.forEach((item) => {
        totals[supplier].totalOrdered += Number(item.quantity_ordered) || 0;
        totals[supplier].totalReceived += Number(item.quantity_received) || 0;
      });
    }
  }
  return totals;
});

const formatNumber = (num) => {
  return Number(num || 0).toLocaleString();
};

// New order form
const newOrder = ref({
  supplier: "",
  productId: "",
  paid: false,
  variants: [], // Stores variants with quantities
});

const hasSelectedVariants = computed(() => {
  return newOrder.value.variants.some((v) => v.quantity_ordered > 0);
});

const selectedProduct = (shopifyItemId) => {
  if (!products.value.length) return "Loading..."; // Prevent lookup on empty array

  // Find the matching product where its ID equals shopifyItemId
  const matchingItem = allProductsItems.value.find(
    (item) => Number(item.shopify_product_id) === Number(shopifyItemId)
  );

  return matchingItem || "N/a";
};

const selectedItem = (shopifyItemId) => {
  if (!products.value.length) return "Loading..."; // Prevent lookup on empty array

  // Find the matching product where its ID equals shopifyItemId
  const matchingItem = allProductsItems.value.find(
    (item) => Number(item.inventory_item_id) === Number(shopifyItemId)
  );

  return matchingItem || "N/a";
};

const confirmDelete = async () => {
  try {
    isSubmitting.value = true;
    const response = await axios.delete(
      `${apiUrl}/api/purchase-orders/${selectRecord.value.id}`,
      {
        params: {
          order_id: selectRecord.value.id,
          shop: shop,
        },
      }
    );

    if (response?.data?.success) {
      toast(response?.data?.message || "Purchase Order deleted successfully.", {
        type: "success",
      });
      await fetchOrders();
    }
    isSubmitting.value = false;
    showDeleteModal.value = false;
  } catch (error) {
    console.error("Error creating purchase order:", error);
    errorItemMessage.value =
      error.response?.data?.message ||
      "Failed to create order. Please try again.";
  } finally {
    isSubmitting.value = false;
  }
};

// Open create order modal
const onClickNewOrderBtn = async () => {
  isEdit.value = false;
  showCreateOrderModal.value = true;
  resetForm();
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
      case "received":
        return "bg-green-100 text-green-800";
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

const openDeleteProductModal = (order) => {
  selectRecord.value = order;
  showDeleteModal.value = true;
};
const openCreateProductModal = (order) => {
  if (!order) return;

  selectRecord.value = order;
  isEdit.value = true;

  const product = products.value.find((p) => p.id === order.shopify_product_id);

  const variants = [];

  const variant = product.variants.find(
    (item) => item.inventory_item_id === order.inventory_item_id
  );

  if (variant) {
    variants.push({
      ...variant,
      quantity_ordered: order.quantity_ordered,
    });
  }

  // Create a new object with the updated values
  newOrder.value = {
    ...newOrder.value, // Keep existing properties
    supplier: order.supplier_name || "",
    productId: order.shopify_product_id || "",
    variants: variants,
    paid: order.paid,
  };

  showCreateOrderModal.value = true;
};

// Watch for changes to reload orders
watch([() => showReceiveModal, () => showCreateOrderModal], () => {
  if (!showReceiveModal.value && !showCreateOrderModal.value) {
    fetchOrders();
  }
});
const loadVariants = () => {
  const product = products.value.find((p) => p.id === newOrder.value.productId);
  if (product) {
    newOrder.value.variants = product.variants.map((variant) => ({
      ...variant,
      quantity_ordered: 0,
    }));
  }
};

const applyBulkQuantity = () => {
  newOrder.value.variants.forEach((variant) => {
    variant.quantity_ordered = bulkQuantity.value;
  });
};

const createPurchaseOrder = async () => {
  try {
    isSubmitting.value = true;
    errorItemMessage.value = "";

    // Validate form
    if (!newOrder.value.supplier || !newOrder.value.productId) {
      errorItemMessage.value = "Supplier and product are required";
      return;
    }

    if (!hasSelectedVariants.value) {
      errorItemMessage.value =
        "Please enter quantities for at least one variant";
      return;
    }

    if (isEdit.value) {
      const payload = {
        supplier: newOrder.value.supplier,
        quantity_ordered: Number(newOrder.value.variants[0]?.quantity_ordered),
        paid: newOrder.value.paid, // will be true only if explicitly 'true'
        shop: shop,
      };

      // TODO: Replace with your actual edit API endpoint
      const response = await axios.post(
        `${apiUrl}/api/purchase-orders/${selectRecord.value.id}/update`,
        payload
      );

      if (response?.data?.success) {
        toast(response?.data?.message || "Order updated successfully.", {
          type: "success",
        });
        await fetchOrders();
        isEdit.value = false;
        showCreateOrderModal.value = false;
      }
    } else {
      const payload = {
        supplier: newOrder.value.supplier,
        shopify_product_id: newOrder.value.productId,
        paid: newOrder.value.paid,
        shop: shop,
        items: newOrder.value.variants
          .filter((v) => v.quantity_ordered > 0)
          .map((variant) => ({
            inventory_item_id: variant.inventory_item_id,
            quantity_ordered: variant.quantity_ordered,
          })),
      };

      const response = await axios.post(
        `${apiUrl}/api/purchase-order`,
        payload
      );

      if (response?.data?.success) {
        toast.success(response?.data?.message || "Order created successfully");
        await fetchOrders();
        showCreateOrderModal.value = false;
      }
    }
  } catch (error) {
    console.error("Error creating purchase order:", error);
    errorItemMessage.value =
      error.response?.data?.message ||
      "Failed to create order. Please try again.";

    setTimeout(() => {
      errorItemMessage.value = "";
    }, 3000);
  } finally {
    isSubmitting.value = false;
  }
};

const openReceiveModal = async (productGroup, isSingle) => {
  if (isSingle) {
    const productOrders = orders.value.filter(
      (order) => order.id === productGroup.id
    );

    selectedOrder.value = {
      productId: productGroup.shopify_product_id,
      productName: selectedProduct(productGroup.shopify_product_id)?.label,
      items: productOrders.map((order) => ({
        ...order,
        quantity_to_receive: Math.max(
          0,
          order.quantity_ordered - order.quantity_received
        ),
        variant_name:
          selectedItem(order.inventory_item_id)?.variant_name || "Default",
      })),
    };
  } else {
    // Get all orders for this product
    const productOrders = orders.value.filter(
      (order) => order.shopify_product_id === productGroup.shopify_product_id
    );

    selectedOrder.value = {
      productId: productGroup.shopify_product_id,
      productName: selectedProduct(productGroup.shopify_product_id)?.label,
      items: productOrders.map((order) => ({
        ...order,
        quantity_to_receive: Math.max(
          0,
          order.quantity_ordered - order.quantity_received
        ),
        variant_name:
          selectedItem(order.inventory_item_id)?.variant_name || "Default",
      })),
    };
  }

  showReceiveModal.value = true;

  if (locations.value.length === 0) {
    await fetchInventoryLocation();
  }
};

const submitReceive = async () => {
  try {
    isSubmitting.value = true;

    locationError.value = ""; // Reset error message

    // Validate location
    if (!selectedLocation.value) {
      locationError.value = "Please select a location";
      isSubmitting.value = false;
      return;
    }

    // Validate quantities
    const invalidItems = selectedOrder.value.items.filter((item) => {
      const maxAllowed = item.quantity_ordered - item.quantity_received;
      return (
        item.quantity_to_receive > 0 && item.quantity_to_receive > maxAllowed
      );
    });

    if (invalidItems.length > 0) {
      locationError.value(
        "One or more items have invalid quantities. Please check the values and try again."
      );
      isSubmitting.value = false;
      return;
    }

    const payload = {
      location_id: selectedLocation.value,
      items: selectedOrder.value.items
        .filter((item) => item.quantity_to_receive > 0)
        .map((item) => ({
          inventory_item_id: item.inventory_item_id,
          quantity: item.quantity_to_receive,
        })),
    };

    const response = await axios.post(
      `${apiUrl}/api/purchase-orders/receive?shop=${shop}`,
      payload
    );

    if (response.data.success) {
      toast.success(response.data.message || "Inventory received successfully");
      await fetchOrders();
      showReceiveModal.value = false;
      isSubmitting.value = false;
      locationError.value = ""; // Reset error message
    }
  } catch (error) {
    console.error("Error receiving inventory:", error);
    toast.error("Failed to receive inventory. Please try again.");
  } finally {
    isSubmitting.value = false;
    locationError.value = ""; // Reset error message
  }
};

// Fetch products instead of items
const fetchProducts = async () => {
  try {
    loadingItems.value = true;
    const response = await axios.get(`${apiUrl}/api/products?shop=${shop}`);

    products.value = response?.data?.data || [];

    allProductsItems.value = products.value.flatMap((product) =>
      product.variants.map((variant) => ({
        label: product.title,
        value: variant.id,
        inventory_item_id: variant.inventory_item_id,
        barcode: variant.barcode,
        variant_name: variant.title,
        shopify_product_id: product.id,
      }))
    );
  } catch (error) {
    console.error("Error fetching products:", error);
    errorItemMessage.value = "Failed to load products. Please try again.";
  } finally {
    loadingItems.value = false;
  }
};

const onClickUndoRecieved = async (data) => {
  try {
    const response = await axios.post(
      `${apiUrl}/api/purchase-orders/${data.id}/undo?shop=${shop}`
    );

    if (response?.data?.success) {
      toast(
        response?.data?.message ||
          "Order received quantity reset successfully.",
        {
          type: "success",
        }
      );
      await fetchOrders();
    }
  } catch (error) {
    console.log("🚀 ~ onClickUndoRecieved ~ err:", error);
    toast(
      error?.response?.data?.error ||
        error?.response?.data?.message ||
        "Failed to create order. Please try again.",
      {
        type: "error",
      }
    );
  }
};

// Reset form
const resetForm = () => {
  newOrder.value = {
    supplier: "",
    productId: "",
    paid: false,
    variants: [],
  };
  bulkQuantity.value = 0;
  errorMessage.value = "";
};

// Update onMounted to fetch products
onMounted(() => {
  fetchOrders();
  fetchProducts(); // Renamed from fetchItems
});
</script>
