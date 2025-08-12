<template>
    <div class="flex items-center justify-center min-h-screen px-4 py-12 bg-gray-50 dark:bg-gray-900 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8">
            <div>
                <div
                    class="flex items-center justify-center w-12 h-12 mx-auto bg-blue-100 rounded-full dark:bg-blue-900">
                    <DevicePhoneMobileIcon class="w-8 h-8 text-blue-600 dark:text-blue-400" />
                </div>
                <h2 class="mt-6 text-3xl font-extrabold text-center text-gray-900 dark:text-white">
                    {{ t('auth.create_account') }}
                </h2>
                <p class="mt-2 text-sm text-center text-gray-600 dark:text-gray-400">
                    {{ t('auth.register_description') }}
                </p>
            </div>

            <form class="mt-8 space-y-6" @submit.prevent="handleRegister">
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ t('auth.full_name') }} *
                        </label>
                        <input id="name" v-model="form.name" type="text" required
                            class="relative block w-full px-3 py-2 mt-1 text-gray-900 placeholder-gray-500 bg-white border border-gray-300 rounded-md appearance-none dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:bg-gray-800 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            :placeholder="t('auth.full_name')" />
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ t('auth.email') }} *
                        </label>
                        <input id="email" v-model="form.email" type="email" required
                            class="relative block w-full px-3 py-2 mt-1 text-gray-900 placeholder-gray-500 bg-white border border-gray-300 rounded-md appearance-none dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:bg-gray-800 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            :placeholder="t('auth.email')" />
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ t('auth.phone') }}
                        </label>
                        <input id="phone" v-model="form.phone" type="tel"
                            class="relative block w-full px-3 py-2 mt-1 text-gray-900 placeholder-gray-500 bg-white border border-gray-300 rounded-md appearance-none dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:bg-gray-800 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="+966501234567" />
                    </div>

                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ t('auth.account_type') }} *
                        </label>
                        <select id="role" v-model="form.role" required
                            class="block w-full px-3 py-2 mt-1 text-gray-900 bg-white border border-gray-300 rounded-md dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">{{ t('auth.select_account_type') }}</option>
                            <option value="user">{{ t('roles.user') }}</option>
                            <option value="security_agency">{{ t('roles.security_agency') }}</option>
                        </select>
                    </div>

                    <div v-if="form.role === 'security_agency'">
                        <label for="organization" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ t('auth.organization') }} *
                        </label>
                        <input id="organization" v-model="form.organization" type="text"
                            :required="form.role === 'security_agency'"
                            class="relative block w-full px-3 py-2 mt-1 text-gray-900 placeholder-gray-500 bg-white border border-gray-300 rounded-md appearance-none dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:bg-gray-800 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            :placeholder="t('auth.organization_placeholder')" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ t('auth.password') }} *
                        </label>
                        <input id="password" v-model="form.password" type="password" required minlength="8"
                            class="relative block w-full px-3 py-2 mt-1 text-gray-900 placeholder-gray-500 bg-white border border-gray-300 rounded-md appearance-none dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:bg-gray-800 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            :placeholder="t('auth.password')" />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            {{ t('auth.password_requirements') }}
                        </p>
                    </div>

                    <div>
                        <label for="password_confirmation"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ t('auth.confirm_password') }} *
                        </label>
                        <input id="password_confirmation" v-model="form.password_confirmation" type="password" required
                            minlength="8"
                            class="relative block w-full px-3 py-2 mt-1 text-gray-900 placeholder-gray-500 bg-white border border-gray-300 rounded-md appearance-none dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:bg-gray-800 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            :placeholder="t('auth.confirm_password')" />
                    </div>

                    <div>
                        <label for="locale" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ t('auth.preferred_language') }}
                        </label>
                        <select id="locale" v-model="form.locale"
                            class="block w-full px-3 py-2 mt-1 text-gray-900 bg-white border border-gray-300 rounded-md dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="ar">العربية</option>
                            <option value="en">English</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center">
                    <input id="terms" v-model="form.terms" type="checkbox" required
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" />
                    <label for="terms" class="block ml-2 text-sm text-gray-900 dark:text-gray-300">
                        {{ t('auth.agree_to') }}
                        <a href="#" class="text-blue-600 hover:text-blue-500 dark:text-blue-400">
                            {{ t('auth.terms_and_conditions') }}
                        </a>
                    </label>
                </div>

                <div>
                    <button type="submit" :disabled="loading"
                        class="relative flex justify-center w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md group hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span v-if="loading" class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <div class="w-5 h-5 border-b-2 border-white rounded-full animate-spin"></div>
                        </span>
                        {{ loading ? t('auth.creating_account') : t('auth.create_account') }}
                    </button>
                </div>

                <div class="text-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        {{ t('auth.have_account') }}
                        <router-link to="/login"
                            class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400">
                            {{ t('auth.login') }}
                        </router-link>
                    </span>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useI18nStore } from '@/stores/i18n'
import { useRouter } from 'vue-router'
import { DevicePhoneMobileIcon } from '@heroicons/vue/24/outline'
import { useAuthStore } from '@/stores/auth'
import { showToast } from '../../utils/toast' // Import showToast

const i18n = useI18nStore()
const t = i18n.t

const router = useRouter()
const authStore = useAuthStore()

const form = ref({
    name: '',
    email: '',
    phone: '',
    role: '',
    organization: '',
    password: '',
    password_confirmation: '',
    locale: 'ar',
    terms: false,
})

const loading = ref(false)

const handleRegister = async () => {
    if (form.value.password !== form.value.password_confirmation) {
        showToast.error('كلمات المرور غير متطابقة')
        return
    }

    loading.value = true

    try {
        await authStore.register(form.value)
        router.push('/dashboard')
    } catch (error) {
        console.error('Registration error:', error)
    } finally {
        loading.value = false
    }
}
</script>
