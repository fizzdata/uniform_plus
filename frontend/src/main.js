import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import "@shopify/polaris/build/esm/styles.css";
import "./assets/main.css";
import Vue3Toastify from "vue3-toastify";
import "vue3-toastify/dist/index.css"; // Make sure Toastify styles are loaded
import {
  // Directives
  vTooltip,
} from "floating-vue";
import "floating-vue/dist/style.css";

const app = createApp(App); // ✅ Create the app instance first
app.directive("tooltip", vTooltip);

app.use(router); // ✅ Use router
app.use(Vue3Toastify, {
  // ✅ Use Toastify with options
  autoClose: 2000,
});

app.mount("#app"); // ✅ Mount the app
