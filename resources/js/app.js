import '../css/app.css';
import { createApp } from 'vue';
import router from './router';
import App from './App.vue';

const app = createApp(App);
app.use(router);
app.mount('#logic-as-data-admin-app');
