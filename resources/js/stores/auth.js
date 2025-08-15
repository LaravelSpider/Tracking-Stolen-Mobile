import { defineStore } from 'pinia'
import axios from 'axios'
import { toast } from 'vue3-toastify'

axios.defaults.baseURL = 'http://localhost:8000/api'
axios.defaults.withCredentials = true
axios.defaults.headers.common['Accept'] = 'application/json'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: null,  //localStorage.getItem('token')
    loading: false,
    error: null,
  }),
  persist: {
    enabled: true,
    strategies: [
      {
        key: 'auth',
        storage: localStorage,
        paths: ['token', 'user'],
      },
    ],
  },

  getters: {
    isAuthenticated: (state) => !!state.token && !!state.user,
    isAdmin: (state) => state.user?.roles?.some(role => role.name === 'admin') ?? false,
    isSecurityAgency: (state) => state.user?.roles?.some(role => role.name === 'security_agency') ?? false,
    isUser: (state) => state.user?.roles?.some(role => role.name === 'user') ?? false,
    userRole: (state) => state.user?.roles?.[0]?.name ?? 'guest',
    userName: (state) => state.user?.name ?? '',
    userEmail: (state) => state.user?.email ?? '',
  },
  // getters: {
  //   isAuthenticated: (state) => !!state.token && !!state.user,
  //   isAdmin: (state) => state.user?.role === 'admin',
  //   isSecurityAgency: (state) => state.user?.role === 'security_agency',
  //   isUser: (state) => state.user?.role === 'user',
  //   userRole: (state) => state.user?.role ?? 'guest',
  //   userName: (state) => state.user?.name ?? '',
  //   userEmail: (state) => state.user?.email ?? '',
  // },
  actions: {
    async login(credentials) {
      this.loading = true
      this.error = null      
      try {
        await axios.get('/sanctum/csrf-cookie')
        const response = await axios.post('/auth/login', credentials)
        
        this.token = response.data.token
        this.user = response.data.user
        
        localStorage.setItem('token', this.token)
        // localStorage.setItem('user', this.user)
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        
        toast.success('تم تسجيل الدخول بنجاح!', {
          position: 'top-right',
          autoClose: 3000,
        })
        
        return response.data
      } catch (error) {
        const message = error.response?.data?.message || 'فشل في تسجيل الدخول'
        toast.error(message, {
          position: 'top-right',
          autoClose: 5000,
        })
        throw error
      } finally {
        this.loading = false
      }
    },

    async register(userData) {
      this.loading = true
      
      try {
        const response = await axios.post('/auth/register', userData)
        
        this.token = response.data.token
        this.user = response.data.user
        
        localStorage.setItem('token', this.token)
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        
        toast.success('تم إنشاء الحساب بنجاح!', {
          position: 'top-right',
          autoClose: 3000,
        })
        
        return response.data
      } catch (error) {
        const message = error.response?.data?.message || 'فشل في إنشاء الحساب'
        toast.error(message, {
          position: 'top-right',
          autoClose: 5000,
        })
        throw error
      } finally {
        this.loading = false
      }
    },

    async logout() {
      try {
        if (this.token) {
          await axios.post('/auth/logout')
        }
        toast.success('تم تسجيل الخروج بنجاح', {
          position: 'top-right',
          autoClose: 3000,
        })
      } catch (error) {
        console.error('خطأ في تسجيل الخروج:', error)
      } finally {
        this.clearAuth()
      }
    },

    async checkAuth() {
      if (!this.token) return false

      try {
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        const response = await axios.get('/auth/user')
        this.user = response.data
        return true
      } catch (error) {
        this.clearAuth()
        return false
      }
    },

    clearAuth() {
      this.token = null
      this.user = null
      localStorage.removeItem('token')
      delete axios.defaults.headers.common['Authorization']
    },

    updateUser(userData) {
      if (this.user) {
        this.user = { ...this.user, ...userData }
      }
    },
    async fetchUser() {
      try {
        const res = await axios.get('/auth/user')
        this.user = res.data
      } catch (err) {
        this.user = null
      }
    }
  },
})
