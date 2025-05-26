import "./assets/main.css";

import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import "@shopify/polaris/build/esm/styles.css";
import Vue3Toastify from "vue3-toastify";
import "vue3-toastify/dist/index.css"; // Make sure Toastify styles are loaded

const app = createApp(App); // ✅ Create the app instance first

app.use(router); // ✅ Use router
app.use(Vue3Toastify, {
  // ✅ Use Toastify with options
  autoClose: 2000,
});

app.mount("#app"); // ✅ Mount the app
