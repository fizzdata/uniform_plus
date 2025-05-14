import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'; // Import router
import '@shopify/polaris/build/esm/styles.css';

createApp(App).use(router).mount('#app')
