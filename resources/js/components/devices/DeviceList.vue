<template>
    <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
        Header
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ $t('devices.my_devices') }}
                </h1>
                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                    {{ $t('devices.list_description') }}
                </p>
            </div>
            <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                <router-link to="/devices/create"
                    class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <PlusIcon class="w-4 h-4 mr-2" />
                    {{ $t('devices.add_device') }}
                </router-link>
            </div>
        </div>

        Filters
        <div class="p-4 mt-6 bg-white rounded-lg shadow dark:bg-gray-800">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label for="status-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ $t('devices.status') }}
                    </label>
                    <select id="status-filter" v-model="filters.status" @change="loadDevices"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm">
                        <option value="">{{ $t('devices.all_statuses') }}</option>
                        <option value="missing">{{ $t('status.missing') }}</option>
                        <option value="reported">{{ $t('status.reported') }}</option>
                        <option value="investigating">{{ $t('status.investigating') }}</option>
                        <option value="found">{{ $t('status.found') }}</option>
                    </select>
                </div>

                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ $t('devices.search') }}
                    </label>
                    <input id="search" v-model="filters.search" @input="debounceSearch" type="text"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                        :placeholder="$t('devices.search_placeholder')" />
                </div>

                <div>
                    <label for="date-from" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ $t('devices.date_from') }}
                    </label>
                    <input id="date-from" v-model="filters.date_from" @change="loadDevices" type="date"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm" />
                </div>

                <div>
                    <label for="date-to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ $t('devices.date_to') }}
                    </label>
                    <input id="date-to" v-model="filters.date_to" @change="loadDevices" type="date"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm" />
                </div>
            </div>
        </div>

        Loading State
        <div v-if="loading" class="mt-6 text-center">
            <div
                class="inline-flex items-center px-4 py-2 text-sm font-semibold leading-6 text-gray-500 transition duration-150 ease-in-out bg-white rounded-md shadow dark:bg-gray-800">
                <div class="w-5 h-5 mr-3 -ml-1 text-blue-500 animate-spin">
                    <div class="w-5 h-5 border-b-2 border-blue-500 rounded-full"></div>
                </div>
                {{ $t('common.loading') }}...
            </div>
        </div>

        Devices List
        <div v-else-if="devices.data && devices.data.length > 0" class="mt-6">
            <div class="overflow-hidden bg-white shadow dark:bg-gray-800 sm:rounded-md">
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    <li v-for="device in devices.data" :key="device.id">
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <DevicePhoneMobileIcon class="w-10 h-10 text-gray-400" />
                                    </div>
                                    <div class="ml-4">
                                        <div class="flex items-center">
                                            <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                                {{ device.brand }} {{ device.model }}
                                            </p>
                                            <StatusBadge :status="device.status" class="ml-2" />
                                        </div>
                                        <div class="flex items-center mt-2 text-sm text-gray-500 dark:text-gray-400">
                                            <p>
                                                {{ $t('devices.imei') }}: {{ device.imei }}
                                            </p>
                                            <span class="mx-2">•</span>
                                            <p>
                                                {{ $t('devices.reported_on') }}: {{ formatDate(device.created_at) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <router-link :to="`/devices/${device.id}`"
                                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                        <EyeIcon class="w-5 h-5" />
                                    </router-link>
                                    <router-link :to="`/devices/${device.id}/edit`"
                                        class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300">
                                        <PencilIcon class="w-5 h-5" />
                                    </router-link>
                                    <button @click="confirmDelete(device)"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        <TrashIcon class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>

                            <div v-if="device.description" class="mt-2">
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ device.description }}
                                </p>
                            </div>

                            <div v-if="device.loss_location"
                                class="flex items-center mt-2 text-sm text-gray-500 dark:text-gray-400">
                                <MapPinIcon class="w-4 h-4 mr-1" />
                                {{ device.loss_location }}
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

            Pagination
            <Pagination v-if="devices.last_page > 1" :current-page="devices.current_page" :last-page="devices.last_page"
                :total="devices.total" @page-changed="changePage" class="mt-6" />
        </div>

        Empty State
        <div v-else class="mt-6 text-center">
            <DevicePhoneMobileIcon class="w-12 h-12 mx-auto text-gray-400" />
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                {{ $t('devices.no_devices') }}
            </h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ $t('devices.no_devices_description') }}
            </p>
            <div class="mt-6">
                <router-link to="/devices/create"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <PlusIcon class="w-4 h-4 mr-2" />
                    {{ $t('devices.add_first_device') }}
                </router-link>
            </div>
        </div>

        Delete Confirmation Modal
        <ConfirmModal v-if="showDeleteModal" :title="$t('devices.delete_device')"
            :message="$t('devices.delete_confirmation')" :confirm-text="$t('common.delete')"
            :cancel-text="$t('common.cancel')" @confirm="deleteDevice" @cancel="showDeleteModal = false" />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import {
    DevicePhoneMobileIcon,
    PlusIcon,
    EyeIcon,
    PencilIcon,
    TrashIcon,
    MapPinIcon,
} from '@heroicons/vue/24/outline'
import { devicesApi } from '../../services/api'
import { showToast } from '../../utils/toast'
import { formatDate } from '../../utils/date'
import StatusBadge from '../ui/StatusBadge.vue'
import Pagination from '../ui/Pagination.vue'
import ConfirmModal from '../ui/ConfirmModal.vue'

const devices = ref({ data: [], current_page: 1, last_page: 1, total: 0 })
const loading = ref(false)
const showDeleteModal = ref(false)
const deviceToDelete = ref(null)

const filters = ref({
    status: '',
    search: '',
    date_from: '',
    date_to: '',
})

let searchTimeout = null

const loadDevices = async (page = 1) => {
    loading.value = true

    try {
        const params = {
            page,
            ...filters.value,
        }

        // Remove empty filters
        Object.keys(params).forEach(key => {
            if (params[key] === '' || params[key] === null) {
                delete params[key]
            }
        })

        devices.value = await devicesApi.getAll(params)
    } catch (error) {
        console.error('Error loading devices:', error)
        showToast.error('Failed to load devices')
    } finally {
        loading.value = false
    }
}

const debounceSearch = () => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        loadDevices()
    }, 500)
}

const changePage = (page) => {
    loadDevices(page)
}

const confirmDelete = (device) => {
    deviceToDelete.value = device
    showDeleteModal.value = true
}

const deleteDevice = async () => {
    if (!deviceToDelete.value) return

    try {
        await devicesApi.delete(deviceToDelete.value.id)
        showToast.success('Device deleted successfully')
        loadDevices()
    } catch (error) {
        console.error('Error deleting device:', error)
        showToast.error('Failed to delete device')
    } finally {
        showDeleteModal.value = false
        deviceToDelete.value = null
    }
}

onMounted(() => {
    loadDevices()
})
</script>
