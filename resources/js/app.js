/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { VueQueryPlugin } from '@tanstack/vue-query';
import App from './App.vue';
import router from './router';
import Navbar from './components/layout/Navbar.vue'
import Toast from './components/ui/Toast.vue'
import MonthlyChart from './components/charts/MonthlyChart.vue'
import StatsCard from './components/ui/StatsCard.vue'
import StatusBadge from './components/ui/StatusBadge.vue'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'

import { useAuthStore } from './stores/auth'

const app = createApp(App);
const pinia = createPinia();

pinia.use(piniaPluginPersistedstate)


app.use(pinia);
app.use(router);
app.use(VueQueryPlugin);

const authStore = useAuthStore();
// console.log(authStore.isAdmin)
// console.log(authStore.userName)

app.component('Navbar', Navbar) // Register globally
app.component('Toast', Toast) // Register globally
app.component('MonthlyChart', MonthlyChart) // Register globally
app.component('StatsCard', StatsCard) // Register globally
app.component('StatusBadge', StatusBadge) // Register globally
app.mount('#app');

