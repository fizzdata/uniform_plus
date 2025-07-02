<template>
  <div class="space-y-6">
    <span class="text-2xl font-bold text-gray-800">Statuses & Transitions</span>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
      <!-- Statuses List -->
      <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-semibold text-gray-800">All Statuses</h2>
        </div>

        <div v-if="loadingStatuses" class="text-sm text-gray-400 italic">
          Loading statuses...
        </div>

        <ul v-else-if="statuses.length" class="divide-y divide-gray-100">
          <li
            v-for="status in statuses"
            :key="status.id"
            class="py-3 px-4 hover:bg-gray-50 transition rounded-lg"
          >
            <span class="text-gray-700 font-medium">{{ status.name }}</span>
          </li>
        </ul>

        <p v-else class="text-sm text-gray-500">No statuses found.</p>
      </div>

      <!-- Transitions List -->
      <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-semibold text-gray-800">
            Status Transitions
          </h2>
        </div>

        <div v-if="loadingTransitions" class="text-sm text-gray-400 italic">
          Loading transitions...
        </div>

        <ul v-else-if="transitions.length" class="divide-y divide-gray-100">
          <li
            v-for="transition in transitions"
            :key="transition.id"
            class="py-3 px-4 hover:bg-gray-50 transition rounded-lg"
          >
            <span class="text-gray-700">
              {{ getStatusName(transition.from_status) }}
            </span>
            <span class="mx-2 text-gray-400">→</span>
            <span class="text-indigo-600 font-medium">
              {{ getStatusName(transition.to_status) }}
            </span>
          </li>
        </ul>

        <p v-else class="text-sm text-gray-500">No transitions found.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const apiUrl = import.meta.env.VITE_API_BASE_URL;
const shop = localStorage.getItem("shop_name");

const statuses = ref([]);
const transitions = ref([]);
const statusMap = ref({});

const loadingStatuses = ref(false);
const loadingTransitions = ref(false);

const fetchStatuses = async () => {
  loadingStatuses.value = true;
  try {
    const res = await axios.get(`${apiUrl}/api/statuses?shop=${shop}`);
    statuses.value = res.data;
    statusMap.value = Object.fromEntries(res.data.map((s) => [s.id, s.name]));
  } catch (error) {
    console.error("Failed to fetch statuses", error);
  } finally {
    loadingStatuses.value = false;
  }
};

const fetchTransitions = async () => {
  loadingTransitions.value = true;
  try {
    const res = await axios.get(`${apiUrl}/api/transitions?shop=${shop}`);
    transitions.value = res.data;
  } catch (error) {
    console.error("Failed to fetch transitions", error);
  } finally {
    loadingTransitions.value = false;
  }
};

const getStatusName = (id) => statusMap.value[id] || "Unknown";

onMounted(async () => {
  await fetchStatuses();
  await fetchTransitions();
});
</script>
