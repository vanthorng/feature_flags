import '../css/app.css'; // make sure CSS is imported
import './bootstrap';

import { createApp } from 'vue';
import App from './App.vue';
import api from './axios';

const app = createApp(App);
app.config.globalProperties.$api = api;
app.mount('#app');
