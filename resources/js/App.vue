<template>
  <div id="app" :class="{ 'rtl': isRTL }" class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <Navbar v-if="isAuthenticated" />
    <main class="transition-all duration-200">
      <router-view />
    </main>
    <Toast />
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { useAuthStore } from './stores/auth'
import { useI18nStore } from './stores/i18n'

const authStore = useAuthStore()
const i18nStore = useI18nStore()

const isAuthenticated = computed(() => authStore.isAuthenticated)
const isRTL = computed(() => i18nStore.currentLocale === 'ar')

onMounted(async () => {
  await authStore.checkAuth()
})
</script>

<style>
.rtl {
  direction: rtl;
  font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  .dark {
    color-scheme: dark;
  }
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  @apply bg-gray-100 dark:bg-gray-800;
}

::-webkit-scrollbar-thumb {
  @apply bg-gray-300 dark:bg-gray-600 rounded-full;
}

::-webkit-scrollbar-thumb:hover {
  @apply bg-gray-400 dark:bg-gray-500;
}

/* Transitions */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.slide-enter-active,
.slide-leave-active {
  transition: transform 0.3s ease;
}

.slide-enter-from {
  transform: translateX(-100%);
}

.slide-leave-to {
  transform: translateX(100%);
}
</style>
