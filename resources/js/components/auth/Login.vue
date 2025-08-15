
<template>
  <div v-if="!isAuthenticated" class="login-container">
    <h1>{{ t('auth.login') }}</h1>
    <form @submit.prevent="handleLogin">
      <div>
        <label for="email">{{ t('auth.email') }}</label>
        <input type="email" v-model="email" required />
      </div>
      <div>
        <label for="password">{{ t('auth.password') }}</label>
        <input type="password" v-model="password" required />
      </div>
      <button type="submit">{{ t('auth.login') }}</button>
    </form>
    <router-link to="/register">{{ t('auth.register') }}</router-link>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useI18nStore } from '@/stores/i18n'
import { useAuthStore } from '@/stores/auth'
import { showToast } from '@/utils/toast'

const authStore = useAuthStore()
const router = useRouter()

const i18n = useI18nStore()
const t = i18n.t
const isAuthenticated = computed(() => authStore.isAuthenticated)

const email = ref('')
const password = ref('')

const handleLogin = async () => {
  // console.log('Login attempt:', email.value, password.value)
  // يمكنك هنا استخدام authStore لتسجيل الدخول
  try {
    const response = await authStore.login({
      email: email.value,
      password: password.value,
      remember: true
    })

    console.log('Login success:', response)
    // showToast.success(response.message || 'تم تسجيل الدخول بنجاح')
    router.push('/dashboard')
  } catch (error) {
    const message = error?.response?.data?.message || 'فشل تسجيل الدخول'
    // showToast.error(message)
    console.error('Login error:', error)
  }
}
</script>

<style scoped>
.login-container {
  max-width: 400px;
  margin: 40px auto;
  padding: 24px;
  border: 1px solid #eee;
  border-radius: 8px;
  background: #fff;
}

.login-container h1 {
  margin-bottom: 24px;
  text-align: center;
}

.login-container form>div {
  margin-bottom: 16px;
}

.login-container label {
  display: block;
  margin-bottom: 4px;
}

.login-container input {
  width: 100%;
  padding: 8px;
  box-sizing: border-box;
}

.login-container button {
  width: 100%;
  padding: 10px;
  background: #4f46e5;
  color: #fff;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.login-container button:hover {
  background: #3730a3;
}

.login-container a {
  display: block;
  margin-top: 16px;
  text-align: center;
  color: #4f46e5;
}
</style>