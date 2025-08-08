import { defineStore } from 'pinia'
import axios from 'axios'
import { toast } from 'vue3-toastify'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token'),
    loading: false,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token && !!state.user,
    isAdmin: (state) => state.user?.roles?.some(role => role.name === 'admin') ?? false,
    isSecurityAgency: (state) => state.user?.roles?.some(role => role.name === 'security_agency') ?? false,
    isUser: (state) => state.user?.roles?.some(role => role.name === 'user') ?? false,
    userRole: (state) => state.user?.roles?.[0]?.name ?? 'guest',
    userName: (state) => state.user?.name ?? '',
    userEmail: (state) => state.user?.email ?? '',
  },

  actions: {
    async login(credentials) {
      this.loading = true
      
      try {
        const response = await axios.post('/api/auth/login', credentials)
        
        this.token = response.data.token
        this.user = response.data.user
        
        localStorage.setItem('token', this.token)
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
        const response = await axios.post('/api/auth/register', userData)
        
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
          await axios.post('/api/auth/logout')
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
        const response = await axios.get('/api/auth/user')
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
  },
})
