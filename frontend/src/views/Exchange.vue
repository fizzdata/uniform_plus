<template>
  <div class="max-w-md mx-auto p-4 bg-white rounded shadow">
    <h2 class="text-xl font-bold mb-4">Exchange Items</h2>

    <label class="block mb-2">Item From:</label>
    <select v-model="itemFrom" class="w-full mb-4 border p-2 rounded">
      <option disabled value="">Select Item</option>
      <optgroup v-for="item in items" :key="item.id" :label="item.title">
        <option
          v-for="variant in item.variants"
          :key="variant.inventory_item_id"
          :value="variant.inventory_item_id"
        >
          {{ variant.title || 'Default' }}
        </option>
          </optgroup>
    </select>

    <label class="block mb-2">Item To:</label>
    <select v-model="itemTo" class="w-full mb-4 border p-2 rounded">
      <option disabled value="">Select Item</option>
      <optgroup v-for="item in items" :key="item.id" :label="item.title">
        <option
          v-for="variant in item.variants"
          :key="variant.inventory_item_id"
          :value="variant.inventory_item_id"
        >
          {{ variant.title || 'Default' }}
        </option>
      </optgroup>
    </select>

    <label class="block mb-2">Quantity:</label>
    <input
      type="number"
      v-model.number="quantity"
      min="1"
      class="w-full mb-4 border p-2 rounded"
    />

    <label class="block mb-2">Location:</label>
    <select v-model="location" class="w-full mb-4 border p-2 rounded">
      <option disabled value="">Select Location</option>
      <option v-for="loc in locations" :key="loc.id" :value="loc.id">{{ loc.name }}</option>
    </select>

    <button
      @click="submitExchange"
      :disabled="!itemFrom || !itemTo"
      class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition cursor-pointer"
    >
      Submit Exchange
    </button>

    <p v-if="message" class="mt-4 text-green-600">{{ message }}</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const apiUrl = import.meta.env.VITE_API_BASE_URL;
const shop = localStorage.getItem("shop_name") || "";

const items = ref([]);
const itemFrom = ref('');
const itemTo = ref('');
const message = ref('');
const quantity = ref('1');
const locations = ref([]);
const location = ref('');

const fetchProducts = async () => {
  try {
    const res = await fetch(`${apiUrl}/api/products?shop=${shop}`);
    const data = await res.json();
    items.value = data.data;
  } catch (error) {
    console.error('Failed to fetch products:', error);
  }
};


const fetchLocations = async () => {
  try {
    const res = await fetch(`${apiUrl}/api/inventory/locations?shop=${shop}`);
    const data = await res.json();
    locations.value = data.data;
  } catch (error) {
    console.error('Failed to fetch locations:', error);
  }
};


const submitExchange = async () => {
  try {
    const res = await fetch(`${apiUrl}/api/inventory/exchange?shop=${shop}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        item_from: itemFrom.value,
        item_to: itemTo.value,
        quantity: quantity.value,
        location_id: location.value
      })
    });
    const data = await res.json();
    message.value = data.message || 'Exchange submitted!';
    itemFrom.value = '';
    itemTo.value = '';
  } catch (error) {
    console.error('Exchange submission failed:', error);
  }
};

onMounted(() => {
  fetchProducts();
  fetchLocations();

});
</script>

<style scoped>
/* Tailwind already handles most of it, but tweak away! */
</style>