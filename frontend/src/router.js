import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from './views/Dashboard.vue';
import Orders from './views/Orders.vue';
import install from './views/Install.vue';
import Inventory from './views/Inventory.vue';
import PurchaseOrder from './views/PurchaseOrder.vue';


const routes = [
  { path: '/', component: Dashboard },
  { path: '/orders', component: Orders },
  { path: '/install', component: install },
  { path: '/inventory', component: Inventory },
  { path: '/purchase-order', component: PurchaseOrder },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});


// Redirect to install if shop isn't set
router.beforeEach((to, from, next) => {
  const shopName = localStorage.getItem('shop_name');
  if (!shopName && to.path !== '/install') {
    next('/install');
  } else {
    next();
  }
});
export default router;