<template>
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="$emit('close')"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div
                class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl dark:bg-gray-800 sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="px-4 pt-5 pb-4 bg-white dark:bg-gray-800 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="w-full mt-3 text-center sm:mt-0 sm:text-left">
                            <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900 dark:text-white">
                                {{ $t('profile.upload_avatar') }}
                            </h3>

                            <!-- Current Avatar -->
                            <div class="flex justify-center mb-4">
                                <img class="object-cover w-24 h-24 rounded-full" :src="currentAvatar"
                                    alt="Current avatar" />
                            </div>

                            <!-- File Upload -->
                            <div class="mt-4">
                                <div
                                    class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md dark:border-gray-600">
                                    <div class="space-y-1 text-center">
                                        <PhotoIcon class="w-12 h-12 mx-auto text-gray-400" />
                                        <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                            <label for="avatar-upload"
                                                class="relative font-medium text-blue-600 bg-white rounded-md cursor-pointer dark:bg-gray-800 hover:text-blue-500">
                                                <span>{{ $t('profile.select_image') }}</span>
                                                <input id="avatar-upload" type="file" accept="image/*" class="sr-only"
                                                    @change="handleFileSelect" />
                                            </label>
                                            <p class="pl-1">{{ $t('profile.or_drag_drop') }}</p>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            PNG, JPG, GIF {{ $t('profile.up_to') }} 2MB
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Preview -->
                            <div v-if="preview" class="flex justify-center mt-4">
                                <img class="object-cover w-24 h-24 rounded-full" :src="preview" alt="Preview" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="uploadAvatar" :disabled="!selectedFile || uploading"
                        class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                        <span v-if="uploading" class="mr-2">
                            <div class="w-4 h-4 border-b-2 border-white rounded-full animate-spin"></div>
                        </span>
                        {{ uploading ? $t('profile.uploading') : $t('profile.upload') }}
                    </button>
                    <button type="button" @click="$emit('close')"
                        class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        {{ $t('common.cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { PhotoIcon } from '@heroicons/vue/24/outline'
import { useAuthStore } from '../../stores/auth'
import { showToast } from '../../utils/toast'
import axios from 'axios'

const emit = defineEmits(['close', 'uploaded'])

const authStore = useAuthStore()
const selectedFile = ref(null)
const preview = ref(null)
const uploading = ref(false)

const currentAvatar = computed(() => {
    return authStore.user?.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(authStore.userName)}&color=7F9CF5&background=EBF4FF`
})

const handleFileSelect = (event) => {
    const file = event.target.files[0]

    if (!file) return

    // Validate file size (2MB)
    if (file.size > 2 * 1024 * 1024) {
        showToast.error('حجم الملف كبير جداً. الحد الأقصى 2MB')
        return
    }

    // Validate file type
    if (!file.type.startsWith('image/')) {
        showToast.error('يرجى اختيار ملف صورة صالح')
        return
    }

    selectedFile.value = file

    // Create preview
    const reader = new FileReader()
    reader.onload = (e) => {
        preview.value = e.target.result
    }
    reader.readAsDataURL(file)
}

const uploadAvatar = async () => {
    if (!selectedFile.value) return

    uploading.value = true

    try {
        const formData = new FormData()
        formData.append('avatar', selectedFile.value)

        const response = await axios.post('/profile/avatar', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        })

        emit('uploaded', response.data.avatar_url)
        emit('close')
    } catch (error) {
        console.error('Avatar upload error:', error)
        const message = error.response?.data?.message || 'فشل في رفع الصورة'
        showToast.error(message)
    } finally {
        uploading.value = false
    }
}
</script>
