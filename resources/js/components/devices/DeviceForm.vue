<template>
    <div class="max-w-4xl px-4 py-8 mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow dark:bg-gray-800">
            <div class="px-4 py-5 sm:p-6">
                <div class="mb-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">
                        {{ isEditing ? $t('devices.edit_device') : $t('devices.report_device') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ $t('devices.form_description') }}
                    </p>
                </div>

                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <!-- Device Information -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="imei" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $t('devices.imei') }} *
                            </label>
                            <input id="imei" v-model="form.imei" type="text" maxlength="15" pattern="[0-9]{15}" required
                                :disabled="isEditing"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                                :placeholder="$t('devices.imei_placeholder')" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ $t('devices.imei_help') }}
                            </p>
                        </div>

                        <div>
                            <label for="serial_number"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $t('devices.serial_number') }}
                            </label>
                            <input id="serial_number" v-model="form.serial_number" type="text" maxlength="50"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                                :placeholder="$t('devices.serial_placeholder')" />
                        </div>

                        <div>
                            <label for="brand" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $t('devices.brand') }} *
                            </label>
                            <select id="brand" v-model="form.brand" required
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm">
                                <option value="">{{ $t('devices.select_brand') }}</option>
                                <option v-for="brand in deviceBrands" :key="brand" :value="brand">
                                    {{ brand }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label for="model" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $t('devices.model') }} *
                            </label>
                            <input id="model" v-model="form.model" type="text" maxlength="100" required
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                                :placeholder="$t('devices.model_placeholder')" />
                        </div>

                        <div>
                            <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $t('devices.color') }}
                            </label>
                            <select id="color" v-model="form.color"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm">
                                <option value="">{{ $t('devices.select_color') }}</option>
                                <option v-for="color in deviceColors" :key="color.value" :value="color.value">
                                    {{ color.label }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label for="lost_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $t('devices.lost_date') }} *
                            </label>
                            <input id="lost_date" v-model="form.lost_date" type="date" :max="today" required
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm" />
                        </div>
                    </div>

                    <!-- Location Information -->
                    <div class="space-y-4">
                        <h4 class="font-medium text-gray-900 text-md dark:text-white">
                            {{ $t('devices.location_info') }}
                        </h4>

                        <div>
                            <label for="loss_location"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $t('devices.loss_location') }}
                            </label>
                            <input id="loss_location" v-model="form.loss_location" type="text" maxlength="255"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                                :placeholder="$t('devices.location_placeholder')" />
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="latitude"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ $t('devices.latitude') }}
                                </label>
                                <input id="latitude" v-model="form.latitude" type="number" step="any" min="-90" max="90"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                                    placeholder="24.7136" />
                            </div>

                            <div>
                                <label for="longitude"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ $t('devices.longitude') }}
                                </label>
                                <input id="longitude" v-model="form.longitude" type="number" step="any" min="-180"
                                    max="180"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                                    placeholder="46.6753" />
                            </div>
                        </div>

                        <div class="flex items-center">
                            <button type="button" @click="getCurrentLocation" :disabled="gettingLocation"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:text-gray-300 dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                                <MapPinIcon class="w-4 h-4 mr-2" />
                                {{ gettingLocation ? $t('devices.getting_location') : $t('devices.get_current_location')
                                }}
                            </button>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="space-y-4">
                        <h4 class="font-medium text-gray-900 text-md dark:text-white">
                            {{ $t('devices.additional_info') }}
                        </h4>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $t('devices.description') }}
                            </label>
                            <textarea id="description" v-model="form.description" rows="4" maxlength="1000"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                                :placeholder="$t('devices.description_placeholder')"></textarea>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ form.description?.length || 0 }}/1000
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="reward_amount"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ $t('devices.reward_amount') }}
                                </label>
                                <div class="relative mt-1 rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <span class="text-gray-500 dark:text-gray-400 sm:text-sm">{{
                                            $t('devices.currency') }}</span>
                                    </div>
                                    <input id="reward_amount" v-model="form.reward_amount" type="number" min="0"
                                        step="0.01"
                                        class="block w-full pl-12 pr-12 border-gray-300 rounded-md dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                                        :placeholder="$t('devices.reward_placeholder')" />
                                </div>
                            </div>

                            <div>
                                <label for="priority"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ $t('devices.priority') }}
                                </label>
                                <select id="priority" v-model="form.priority"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm">
                                    <option value="1">{{ $t('devices.priority_low') }}</option>
                                    <option value="2">{{ $t('devices.priority_medium') }}</option>
                                    <option value="3">{{ $t('devices.priority_high') }}</option>
                                    <option value="4">{{ $t('devices.priority_critical') }}</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="contact_info"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $t('devices.contact_info') }}
                            </label>
                            <textarea id="contact_info" v-model="form.contact_info" rows="3" maxlength="500"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                                :placeholder="$t('devices.contact_placeholder')"></textarea>
                        </div>

                        <!-- Image Upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $t('devices.images') }}
                            </label>
                            <div
                                class="flex justify-center px-6 pt-5 pb-6 mt-1 border-2 border-gray-300 border-dashed rounded-md dark:border-gray-600">
                                <div class="space-y-1 text-center">
                                    <PhotoIcon class="w-12 h-12 mx-auto text-gray-400" />
                                    <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                        <label for="images"
                                            class="relative font-medium text-blue-600 bg-white rounded-md cursor-pointer dark:bg-gray-800 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>{{ $t('devices.upload_images') }}</span>
                                            <input id="images" type="file" multiple accept="image/*" class="sr-only"
                                                @change="handleImageUpload" />
                                        </label>
                                        <p class="pl-1">{{ $t('devices.or_drag_drop') }}</p>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $t('devices.image_formats') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Image Preview -->
                            <div v-if="imagePreview.length > 0"
                                class="grid grid-cols-2 gap-4 mt-4 sm:grid-cols-3 lg:grid-cols-4">
                                <div v-for="(image, index) in imagePreview" :key="index" class="relative">
                                    <img :src="image" alt="Preview" class="object-cover w-24 h-24 rounded-lg" />
                                    <button type="button" @click="removeImage(index)"
                                        class="absolute p-1 text-white bg-red-500 rounded-full -top-2 -right-2 hover:bg-red-600">
                                        <XMarkIcon class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="$router.go(-1)"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            {{ $t('common.cancel') }}
                        </button>
                        <button type="submit" :disabled="loading"
                            class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                            <span v-if="loading" class="mr-2">
                                <div class="w-4 h-4 border-b-2 border-white rounded-full animate-spin"></div>
                            </span>
                            {{ loading ? $t('common.saving') : (isEditing ? $t('common.update') :
                                $t('devices.report_device')) }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import {
    MapPinIcon,
    PhotoIcon,
    XMarkIcon
} from '@heroicons/vue/24/outline'
import { devicesApi } from '../../services/api'
import { showToast } from '../../utils/toast'

const route = useRoute()
const router = useRouter()

const isEditing = computed(() => !!route.params.id)
const loading = ref(false)
const gettingLocation = ref(false)
const imagePreview = ref([])
const selectedImages = ref([])

const today = new Date().toISOString().split('T')[0]

const form = ref({
    imei: '',
    serial_number: '',
    brand: '',
    model: '',
    color: '',
    lost_date: '',
    description: '',
    loss_location: '',
    latitude: '',
    longitude: '',
    reward_amount: '',
    contact_info: '',
    priority: 2,
})

const deviceBrands = [
    'Samsung', 'Apple', 'Huawei', 'Xiaomi', 'Oppo', 'Vivo',
    'OnePlus', 'Google', 'Sony', 'LG', 'Nokia', 'Motorola', 'Other'
]

const deviceColors = [
    { value: 'black', label: 'أسود / Black' },
    { value: 'white', label: 'أبيض / White' },
    { value: 'silver', label: 'فضي / Silver' },
    { value: 'gold', label: 'ذهبي / Gold' },
    { value: 'blue', label: 'أزرق / Blue' },
    { value: 'red', label: 'أحمر / Red' },
    { value: 'green', label: 'أخضر / Green' },
    { value: 'purple', label: 'بنفسجي / Purple' },
    { value: 'pink', label: 'وردي / Pink' },
    { value: 'other', label: 'أخرى / Other' },
]

const getCurrentLocation = () => {
    if (!navigator.geolocation) {
        showToast.error('Geolocation is not supported by this browser')
        return
    }

    gettingLocation.value = true

    navigator.geolocation.getCurrentPosition(
        (position) => {
            form.value.latitude = position.coords.latitude.toFixed(6)
            form.value.longitude = position.coords.longitude.toFixed(6)
            showToast.success('Location obtained successfully')
            gettingLocation.value = false
        },
        (error) => {
            console.error('Error getting location:', error)
            showToast.error('Failed to get current location')
            gettingLocation.value = false
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    )
}

const handleImageUpload = (event) => {
    const files = Array.from(event.target.files)

    if (files.length + selectedImages.value.length > 5) {
        showToast.warning('Maximum 5 images allowed')
        return
    }

    files.forEach(file => {
        if (file.size > 2 * 1024 * 1024) { // 2MB limit
            showToast.warning(`File ${file.name} is too large. Maximum size is 2MB`)
            return
        }

        selectedImages.value.push(file)

        const reader = new FileReader()
        reader.onload = (e) => {
            imagePreview.value.push(e.target.result)
        }
        reader.readAsDataURL(file)
    })
}

const removeImage = (index) => {
    selectedImages.value.splice(index, 1)
    imagePreview.value.splice(index, 1)
}

const handleSubmit = async () => {
    loading.value = true

    try {
        const formData = new FormData()

        // Add form fields
        Object.keys(form.value).forEach(key => {
            if (form.value[key] !== '' && form.value[key] !== null) {
                formData.append(key, form.value[key])
            }
        })

        // Add images
        selectedImages.value.forEach((image, index) => {
            formData.append(`images[${index}]`, image)
        })

        let response
        if (isEditing.value) {
            response = await devicesApi.update(route.params.id, formData)
            showToast.success('Device updated successfully')
        } else {
            response = await devicesApi.create(formData)
            showToast.success('Device reported successfully')
        }

        router.push('/devices')
    } catch (error) {
        console.error('Form submission error:', error)
        const message = error.response?.data?.message || 'An error occurred'
        showToast.error(message)
    } finally {
        loading.value = false
    }
}

const loadDevice = async () => {
    if (!isEditing.value) return

    try {
        const device = await devicesApi.getById(route.params.id)

        // Populate form with device data
        Object.keys(form.value).forEach(key => {
            if (device[key] !== undefined) {
                form.value[key] = device[key]
            }
        })

        // Handle date formatting
        if (device.lost_date) {
            form.value.lost_date = device.lost_date.split('T')[0]
        }

        // Load existing images if any
        if (device.images && device.images.length > 0) {
            imagePreview.value = device.images.map(img => `/storage/${img}`)
        }
    } catch (error) {
        console.error('Error loading device:', error)
        showToast.error('Failed to load device data')
        router.push('/devices')
    }
}

onMounted(() => {
    loadDevice()
})
</script>
