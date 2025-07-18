import { createRouter, createWebHistory } from "vue-router";
import Dashboard from "./views/Dashboard.vue";
import Orders from "./views/Orders.vue";
import install from "./views/Install.vue";
import Inventory from "./views/Inventory.vue";
import PurchaseOrder from "./views/PurchaseOrder.vue";
import StatusPage from "./views/Status.vue";
import Exchange from "./views/Exchange.vue";


const routes = [
  { path: "/", component: Dashboard },
  { path: "/orders", component: Orders },
  { path: "/install", component: install },
  { path: "/inventory", component: Inventory },
  { path: "/purchase-orders", component: PurchaseOrder },
  { path: "/status", component: StatusPage },
  { path: "/exchange", component: Exchange },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Redirect to install if shop isn't set
// router.beforeEach((to, from, next) => {
//   const shopName = localStorage.getItem('shop_name');
//   if (!shopName && to.path !== '/install') {
//     next('/install');
//   } else {
//     next();
//   }
// });
export default router;
