import '../../css/app.css'
import { createApp } from "vue";
import router from "./router";
import App from './App.vue';
import { createPinia } from 'pinia';
import AuthLayout from './layouts/auth/index.vue';
import ManagerLayout from './layouts/manager/index.vue';

const app = createApp(App);
// LAYAOUTS
app.component('auth-layout', AuthLayout);
app.component('manager-layout', ManagerLayout);
// Plugins
// STORE (Pinia)
const pinia = createPinia();

app.use(router)
    .use(pinia)
    .mount('#app');