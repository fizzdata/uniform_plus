<template>
  <div>
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Orders</h2>
      <button class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">
        Create Order
      </button>
    </div>

    <!-- Orders Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
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
                {{ order.customer }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ order.date }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="statusClasses(order.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ order.status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ${{ order.amount }}
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
        Showing <span class="font-medium">1</span> to <span class="font-medium">10</span> of <span class="font-medium">24</span> results
      </div>
      <div class="flex space-x-2">
        <button class="px-3 py-1 border rounded-md text-gray-700 bg-white hover:bg-gray-50">
          Previous
        </button>
        <button class="px-3 py-1 border rounded-md text-gray-700 bg-white hover:bg-gray-50">
          Next
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useShopifyOrders } from '../composables/useShopifyOrders';

const orders = [
  {
    id: '1001',
    customer: 'John Smith',
    date: '2025-05-14',
    status: 'Completed',
    amount: '125.99'
  },
  {
    id: '1002',
    customer: 'Sarah Johnson',
    date: '2025-05-13',
    status: 'Processing',
    amount: '89.50'
  },
  {
    id: '1003',
    customer: 'Michael Brown',
    date: '2025-05-12',
    status: 'Shipped',
    amount: '234.75'
  },
  {
    id: '1004',
    customer: 'Emily Davis',
    date: '2025-05-11',
    status: 'Pending',
    amount: '56.20'
  },
  {
    id: '1005',
    customer: 'Robert Wilson',
    date: '2025-05-10',
    status: 'Cancelled',
    amount: '178.30'
  }
]

const statusClasses = (status) => {
  switch(status) {
    case 'Completed':
      return 'bg-green-100 text-green-800'
    case 'Processing':
      return 'bg-blue-100 text-blue-800'
    case 'Shipped':
      return 'bg-yellow-100 text-yellow-800'
    case 'Pending':
      return 'bg-gray-100 text-gray-800'
    case 'Cancelled':
      return 'bg-red-100 text-red-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }




    const shopName = ref('123');
    const { orders, fetchOrders, loading } = useShopifyOrders(shopName.value);
    const apiUrl = import.meta.env.VITE_API_BASE_URL;

    return { shopName, orders, fetchOrders, loading };


  };
</script>