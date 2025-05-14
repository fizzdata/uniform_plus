import { ref } from 'vue';

export function useShopifyOrders() {
    const orders = ref([]);
    const loading = ref(false);
    const apiUrl = import.meta.env.VITE_API_BASE_URL;
    const shop = localStorage.getItem('shop_name') || '';

    const fetchOrders = async () => {
        loading.value = true;
        try {
            const response = await fetch(`${apiUrl}/api/shopify/orders?shop=${shop}`);
            const data = await response.json();
            orders.value = data.orders || [];
        } catch (error) {
            console.error('Failed to fetch orders:', error);
        } finally {
            loading.value = false;
        }
    };

    return { orders, fetchOrders, loading };
}