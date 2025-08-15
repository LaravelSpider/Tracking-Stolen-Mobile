import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth';
import routes from './routes';

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Navigation guards
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  // Check if route requires authentication
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next("/login")
    return
  }

  // Check if route requires guest (not authenticated)
  if (to.meta.requiresGuest && authStore.isAuthenticated) {
    next("/dashboard")
    return
  }

  // Check role-based access
  if (to.meta.requiresRole && authStore.isAuthenticated) {
    const userRole = authStore.user?.role
    const requiredRoles = Array.isArray(to.meta.requiresRole) ? to.meta.requiresRole : [to.meta.requiresRole]

    if (!requiredRoles.includes(userRole)) {
      // Redirect to dashboard if user doesn't have required role
      next("/dashboard")
      return
    }
  }

  next()
})

// 3. REGISTER ERROR HANDLER (put this right after router creation)
router.onError((error) => {
  console.error('Router error:', error)
  // Optional: Send errors to error tracking service (Sentry, etc.)
})

export default router;

// Router guards will be added in app.js
