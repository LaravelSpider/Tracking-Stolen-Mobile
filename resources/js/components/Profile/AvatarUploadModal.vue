<template>
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="$emit('close')"></div>
  
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
  
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                  {{ $t('profile.upload_avatar') }}
                </h3>
                
                <!-- Current Avatar -->
                <div class="flex justify-center mb-4">
                  <img
                    class="h-24 w-24 rounded-full object-cover"
                    :src="currentAvatar"
                    alt="Current avatar"
                  />
                </div>
  
                <!-- File Upload -->
                <div class="mt-4">
                  <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md">
                    <div class="space-y-1 text-center">
                      <PhotoIcon class="mx-auto h-12 w-12 text-gray-400" />
                      <div class="flex text-sm text-gray-600 dark:text-gray-400">
                        <label for="avatar-upload" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-blue-600 hover:text-blue-500">
                          <span>{{ $t('profile.select_image') }}</span>
                          <input
                            id="avatar-upload"
                            type="file"
                            accept="image/*"
                            class="sr-only"
                            @change="handleFileSelect"
                          />
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
                <div v-if="preview" class="mt-4 flex justify-center">
                  <img
                    class="h-24 w-24 rounded-full object-cover"
                    :src="preview"
                    alt="Preview"
                  />
                </div>
              </div>
            </div>
          </div>
          
          <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="button"
              @click="uploadAvatar"
              :disabled="!selectedFile || uploading"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
            >
              <span v-if="uploading" class="mr-2">
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
              </span>
              {{ uploading ? $t('profile.uploading') : $t('profile.upload') }}
            </button>
            <button
              type="button"
              @click="$emit('close')"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
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
  