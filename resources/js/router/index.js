import { useAuthStore } from '../stores/auth'

// Import components
const Dashboard = () => import('../components/dashboard/Dashboard.vue')
const DeviceSearch = () => import('../components/search/DeviceSearch.vue')
const DeviceList = () => import('../components/devices/DeviceList.vue')
const DeviceForm = () => import('../components/devices/DeviceForm.vue')
const Login = () => import('../components/auth/Login.vue')
const Register = () => import('../components/auth/Register.vue')
const Profile = () => import('../components/profile/Profile.vue')

export const routes = [
  {
    path: '/',
    redirect: '/dashboard'
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/search',
    name: 'search',
    component: DeviceSearch
  },
  {
    path: '/devices',
    name: 'devices',
    component: DeviceList,
    meta: { requiresAuth: true }
  },
  {
    path: '/devices/create',
    name: 'device-create',
    component: DeviceForm,
    meta: { requiresAuth: true }
  },
  {
    path: '/devices/:id/edit',
    name: 'device-edit',
    component: DeviceForm,
    meta: { requiresAuth: true }
  },
  {
    path: '/profile',
    name: 'profile',
    component: Profile,
    meta: { requiresAuth: true }
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
    meta: { requiresGuest: true }
  },
  {
    path: '/register',
    name: 'register',
    component: Register,
    meta: { requiresGuest: true }
  },
]

// Router guards will be added in app.js
