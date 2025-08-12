import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth';
import routes from './routes';

const router = createRouter({
  history: createWebHistory(),
  routes,
});


router.beforeEach((to) => {
  const authStore = useAuthStore()

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    return { name: 'login' }
  } 
  
  if (to.meta.requiresGuest && authStore.isAuthenticated) {
    return { name: 'dashboard' }
  }
  return
})

// 3. REGISTER ERROR HANDLER (put this right after router creation)
router.onError((error) => {
  console.error('Router error:', error)
  // Optional: Send errors to error tracking service (Sentry, etc.)
})

export default router;

// Router guards will be added in app.js
