<template>
  <div>
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Inventory</h2>
      <div class="flex items-center space-x-4">
        <button
          @click="fetchInventory"
          class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition"
          :disabled="loading"
        >
          {{ loading ? "Loading..." : "Refresh Inventory" }}
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="h-full flex items-center justify-center pt-10">
      <BaseSpinner :show-loader="loading" size="md" />
    </div>

    <!-- Inventory Table -->
    <div v-else class="bg-white shadow rounded-lg overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Item ID
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Item Name
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Quantity
              </th>
              <th scope="col" class="relative px-6 py-3">
                <span class="sr-only">Actions</span>
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="item in items" :key="item.id" class="hover:bg-gray-50">
              <td
                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
              >
                #{{ item.inventory_item_id }}
              </td>
              <td
                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize"
              >
                {{
                  selectedRow(item.inventory_item_id)
                    ? (selectedRow(item.inventory_item_id).label || "") +
                      (selectedRow(item.inventory_item_id).variant_name
                        ? " (" +
                          selectedRow(item.inventory_item_id).variant_name +
                          ")"
                        : "")
                    : "N/A"
                }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ item.available }}
              </td>

              <td
                class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
              >
                <button
                  @click="openMoveModel(item)"
                  class="text-gray-600 hover:text-gray-900"
                >
                  Move
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
        <span class="font-medium">{{ totalItems?.length }}</span> of
        <span class="font-medium">{{ totalInventory }}</span> results
      </div>
      <div class="flex space-x-2">
        <button
          class="px-3 py-1 border rounded-md text-gray-700 bg-white hover:bg-gray-50"
          :disabled="!hasPreviousPage"
          @click="fetchInventory(currentPage - 1)"
        >
          Previous
        </button>
        <button
          class="px-3 py-1 border rounded-md text-gray-700 bg-white hover:bg-gray-50"
          :disabled="!hasNextPage"
          @click="fetchInventory(currentPage + 1)"
        >
          Next
        </button>
      </div>
    </div>

    <!-- Move Inventory Modal -->
    <div
      v-if="showMoveModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center"
    >
      <div class="bg-white p-6 rounded-lg w-96">
        <h3 class="text-lg font-bold mb-4">Move Inventory</h3>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700"
            >From Location</label
          >
          <select
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
          >
            <option value="warehouse">Warehouse</option>
            <option value="store">Store</option>
          </select>
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700"
            >To Location</label
          >
          <select
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
          >
            <option value="store">Store</option>
            <option value="warehouse">Warehouse</option>
          </select>
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700"
            >Quantity</label
          >
          <input
            type="number"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
            :max="selectedItem.quantity"
            min="1"
          />
        </div>

        <div class="flex justify-end space-x-3">
          <button
            @click="showMoveModal = false"
            class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md"
          >
            Cancel
          </button>
          <button
            @click="handleTransfer"
            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
          >
            Confirm Move
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import BaseSpinner from "@/components/BaseSpinner.vue";
import axios from "axios";
import { ref, onMounted, computed } from "vue";

const apiUrl = import.meta.env.VITE_API_BASE_URL;

// Reactive state
const items = ref([]);
const loading = ref(false);
const errorMessage = ref("");
const currentPage = ref(1);
const totalItems = ref(0);
const itemsPerPage = ref(25);
const showMoveModal = ref(false);
const selectedItem = ref(null);
const shop = localStorage.getItem("shop_name");
const errorItemMessage = ref("");
const allProductsItems = ref([]);
const loadingItems = ref(false);

// Computed properties
const hasNextPage = computed(() => {
  return currentPage.value * itemsPerPage.value < totalItems.value;
});

const hasPreviousPage = computed(() => {
  return currentPage.value > 1;
});

// Methods
const fetchInventory = async (page = 1) => {
  try {
    loading.value = true;
    errorMessage.value = "";

    const response = await axios.get(
      `${apiUrl}/api/inventory?page=${page}&per_page=${itemsPerPage.value}&shop=${shop}`
    );

    if (response?.data?.success) {
      items.value = response.data.data;

      totalItems.value = response.data.total;
      currentPage.value = response.data.current_page;
      loading.value = false;
    } else {
      console.error("Error updating status:", await response.json());
    }
  } catch (error) {
    console.error("Request failed:", error);
    loading.value = false;
  }
};

const openMoveModal = (item) => {
  selectedItem.value = item;
  showMoveModal.value = true;
};

const selectedRow = (shopifyItemId) => {
  if (!items.value.length) return "Loading..."; // Prevent lookup on empty array

  // Find the matching product where its ID equals shopifyItemId
  const matchingItem = allProductsItems.value.find(
    (item) => Number(item.inventory_item_id) === Number(shopifyItemId)
  );

  return matchingItem || "N/a";
};

const handleTransfer = async (transferData) => {
  try {
    await axios.post("/api/inventory/transfers", {
      item_id: selectedItem.value.id,
      shop: shop,
      ...transferData,
    });

    await fetchInventory(currentPage.value);
    showMoveModal.value = false;
  } catch (error) {
    errorMessage.value =
      "Transfer failed: " + error.response?.data?.message || error.message;
  }
};

//Fetch items from API
const fetchItems = async () => {
  try {
    loadingItems.value = true;
    const response = await axios.get(`${apiUrl}/api/products?shop=${shop}`); // Adjust the endpoint as needed

    allProductsItems.value =
      response?.data?.data?.flatMap((product) =>
        product.variants.map((variant) => ({
          label: `${product.title}`,
          value: variant.id,
          inventory_item_id: variant.inventory_item_id,
          barcode: variant.barcode,
          variant_name: variant.title,
        }))
      ) || [];
  } catch (error) {
    console.error("Error fetching items:", error);
    errorItemMessage.value = "Failed to load items. Please try again.";
  } finally {
    loadingItems.value = false;
  }
};

// Lifecycle hooks
onMounted(() => {
  fetchInventory();

  fetchItems();
});
</script>
