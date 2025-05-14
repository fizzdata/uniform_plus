import { ref } from 'vue';

export function useShopifyApi(shop) {
    const accessToken = ref(null);

    const fetchAccessToken = async () => {
        try {
            const response = await fetch(`/api/shopify/token?shop=${shop}`);
            const data = await response.json();
            accessToken.value = data.access_token;
        } catch (error) {
            console.error('Failed to fetch Shopify token:', error);
        }
    };

    return { accessToken, fetchAccessToken };
}