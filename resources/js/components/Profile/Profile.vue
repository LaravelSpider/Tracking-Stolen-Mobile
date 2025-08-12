<template>
    <div class="max-w-4xl px-4 py-8 mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                {{ $t('profile.title') }}
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                {{ $t('profile.description') }}
            </p>
        </div>

        <div class="space-y-6">
            <!-- Profile Information Card -->
            <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center space-x-5" :class="{ 'space-x-reverse': isRTL }">
                        <!-- Avatar -->
                        <div class="flex-shrink-0">
                            <div class="relative">
                                <img class="object-cover w-20 h-20 rounded-full"
                                    :src="user?.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(user?.name || '')}&color=7F9CF5&background=EBF4FF`"
                                    :alt="user?.name" />
                                <button @click="showAvatarUpload = true"
                                    class="absolute bottom-0 right-0 p-1 text-white bg-blue-600 rounded-full hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <CameraIcon class="w-4 h-4" />
                                </button>
                            </div>
                        </div>

                        <!-- User Info -->
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ user?.name }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ user?.email }}
                            </p>
                            <div class="flex items-center mt-1 space-x-2" :class="{ 'space-x-reverse': isRTL }">
                                <span :class="roleClasses"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                    {{ $t(`roles.${userRole}`) }}
                                </span>
                                <span v-if="user?.is_active"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    {{ $t('profile.active') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900 dark:text-white">
                        {{ $t('profile.personal_info') }}
                    </h3>

                    <form @submit.prevent="updateProfile" class="space-y-4">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ $t('profile.name') }} *
                                </label>
                                <input id="name" v-model="profileForm.name" type="text" required
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm" />
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ $t('profile.email') }} *
                                </label>
                                <input id="email" v-model="profileForm.email" type="email" required
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm" />
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ $t('profile.phone') }}
                                </label>
                                <input id="phone" v-model="profileForm.phone" type="tel"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                                    placeholder="+966501234567" />
                            </div>

                            <div>
                                <label for="organization"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ $t('profile.organization') }}
                                </label>
                                <input id="organization" v-model="profileForm.organization" type="text"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm" />
                            </div>

                            <div>
                                <label for="locale" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ $t('profile.language') }}
                                </label>
                                <select id="locale" v-model="profileForm.locale"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm">
                                    <option value="ar">العربية</option>
                                    <option value="en">English</option>
                                </select>
                            </div>

                            <div>
                                <label for="timezone"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ $t('profile.timezone') }}
                                </label>
                                <select id="timezone" v-model="profileForm.timezone"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm">
                                    <option value="Asia/Riyadh">Asia/Riyadh (GMT+3)</option>
                                    <option value="UTC">UTC (GMT+0)</option>
                                    <option value="America/New_York">America/New_York (GMT-5)</option>
                                    <option value="Europe/London">Europe/London (GMT+0)</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" :disabled="profileLoading"
                                class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                                <span v-if="profileLoading" class="mr-2">
                                    <div class="w-4 h-4 border-b-2 border-white rounded-full animate-spin"></div>
                                </span>
                                {{ profileLoading ? $t('common.saving') : $t('common.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password -->
            <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900 dark:text-white">
                        {{ $t('profile.change_password') }}
                    </h3>

                    <form @submit.prevent="changePassword" class="space-y-4">
                        <div>
                            <label for="current_password"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $t('profile.current_password') }} *
                            </label>
                            <input id="current_password" v-model="passwordForm.current_password" type="password"
                                required
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm" />
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="new_password"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ $t('profile.new_password') }} *
                                </label>
                                <input id="new_password" v-model="passwordForm.new_password" type="password" required
                                    minlength="8"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm" />
                            </div>

                            <div>
                                <label for="new_password_confirmation"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ $t('profile.confirm_password') }} *
                                </label>
                                <input id="new_password_confirmation" v-model="passwordForm.new_password_confirmation"
                                    type="password" required minlength="8"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm" />
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" :disabled="passwordLoading"
                                class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50">
                                <span v-if="passwordLoading" class="mr-2">
                                    <div class="w-4 h-4 border-b-2 border-white rounded-full animate-spin"></div>
                                </span>
                                {{ passwordLoading ? $t('common.updating') : $t('profile.update_password') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Account Statistics -->
            <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900 dark:text-white">
                        {{ $t('profile.account_stats') }}
                    </h3>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                {{ stats.total_devices || 0 }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $t('profile.total_reports') }}
                            </div>
                        </div>

                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                {{ stats.found_devices || 0 }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $t('profile.found_devices') }}
                            </div>
                        </div>

                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-600 dark:text-gray-400">
                                {{ formatDate(user?.created_at) }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $t('profile.member_since') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="bg-white border border-red-200 rounded-lg shadow dark:bg-gray-800 dark:border-red-800">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="mb-4 text-lg font-medium leading-6 text-red-900 dark:text-red-400">
                        {{ $t('profile.danger_zone') }}
                    </h3>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $t('profile.delete_account') }}
                                </h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $t('profile.delete_account_description') }}
                                </p>
                            </div>
                            <button @click="showDeleteModal = true"
                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                {{ $t('profile.delete_account') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Avatar Upload Modal -->
        <AvatarUploadModal v-if="showAvatarUpload" @close="showAvatarUpload = false" @uploaded="handleAvatarUploaded" />

        <!-- Delete Account Confirmation -->
        <ConfirmModal v-if="showDeleteModal" :title="$t('profile.delete_account')"
            :message="$t('profile.delete_account_confirmation')" :confirm-text="$t('profile.delete_permanently')"
            :cancel-text="$t('common.cancel')" @confirm="deleteAccount" @cancel="showDeleteModal = false" />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { CameraIcon } from '@heroicons/vue/24/outline'
import { useAuthStore } from '../../stores/auth'
import { useI18nStore } from '../../stores/i18n'
import { showToast } from '../../utils/toast'
import { formatDate } from '../../utils/date'
import ConfirmModal from '../ui/ConfirmModal.vue'
import AvatarUploadModal from './AvatarUploadModal.vue'
import axios from 'axios'

const router = useRouter()
const authStore = useAuthStore()
const i18nStore = useI18nStore()

const user = computed(() => authStore.user)
const userRole = computed(() => authStore.userRole)
const isRTL = computed(() => i18nStore.currentLocale === 'ar')

const profileLoading = ref(false)
const passwordLoading = ref(false)
const showAvatarUpload = ref(false)
const showDeleteModal = ref(false)

const stats = ref({
    total_devices: 0,
    found_devices: 0,
})

const profileForm = ref({
    name: '',
    email: '',
    phone: '',
    organization: '',
    locale: 'ar',
    timezone: 'Asia/Riyadh',
})

const passwordForm = ref({
    current_password: '',
    new_password: '',
    new_password_confirmation: '',
})

const roleClasses = computed(() => {
    switch (userRole.value) {
        case 'admin':
            return 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200'
        case 'security_agency':
            return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
        case 'user':
            return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
    }
})

const loadProfile = () => {
    if (user.value) {
        profileForm.value = {
            name: user.value.name || '',
            email: user.value.email || '',
            phone: user.value.phone || '',
            organization: user.value.organization || '',
            locale: user.value.locale || 'ar',
            timezone: user.value.timezone || 'Asia/Riyadh',
        }
    }
}

const loadStats = async () => {
    try {
        const response = await axios.get('/profile/stats')
        stats.value = response.data
    } catch (error) {
        console.error('Error loading stats:', error)
    }
}

const updateProfile = async () => {
    profileLoading.value = true

    try {
        const response = await axios.put('/profile', profileForm.value)

        // Update auth store
        authStore.updateUser(response.data.user)

        // Update language if changed
        if (profileForm.value.locale !== i18nStore.currentLocale) {
            i18nStore.setLocale(profileForm.value.locale)
        }

        showToast.success('تم تحديث الملف الشخصي بنجاح')
    } catch (error) {
        console.error('Profile update error:', error)
        const message = error.response?.data?.message || 'فشل في تحديث الملف الشخصي'
        showToast.error(message)
    } finally {
        profileLoading.value = false
    }
}

const changePassword = async () => {
    if (passwordForm.value.new_password !== passwordForm.value.new_password_confirmation) {
        showToast.error('كلمات المرور الجديدة غير متطابقة')
        return
    }

    passwordLoading.value = true

    try {
        await axios.put('/profile/password', passwordForm.value)

        // Clear form
        passwordForm.value = {
            current_password: '',
            new_password: '',
            new_password_confirmation: '',
        }

        showToast.success('تم تغيير كلمة المرور بنجاح')
    } catch (error) {
        console.error('Password change error:', error)
        const message = error.response?.data?.message || 'فشل في تغيير كلمة المرور'
        showToast.error(message)
    } finally {
        passwordLoading.value = false
    }
}

const handleAvatarUploaded = (avatarUrl) => {
    authStore.updateUser({ avatar_url: avatarUrl })
    showToast.success('تم تحديث الصورة الشخصية بنجاح')
}

const deleteAccount = async () => {
    try {
        await axios.delete('/profile')
        showToast.success('تم حذف الحساب بنجاح')
        await authStore.logout()
        router.push('/')
    } catch (error) {
        console.error('Account deletion error:', error)
        const message = error.response?.data?.message || 'فشل في حذف الحساب'
        showToast.error(message)
    } finally {
        showDeleteModal.value = false
    }
}

onMounted(() => {
    loadProfile()
    loadStats()
})
</script>
