<template>
    <div class="max-w-3xl px-4 py-8 mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900">{{ $t('search.title') }}</h3>
                <p class="mb-6 text-sm text-gray-600">{{ $t('search.description') }}</p>

                <form @submit.prevent="searchDevice" class="space-y-4">
                    <div>
                        <label for="imei" class="block text-sm font-medium text-gray-700">{{ $t('search.imei_label')
                            }}</label>
                        <input v-model="searchForm.imei" type="text" id="imei" maxlength="15" pattern="[0-9]{15}"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            :placeholder="$t('search.imei_placeholder')" required />
                        <p class="mt-1 text-xs text-gray-500">{{ $t('search.imei_help') }}</p>
                    </div>

                    <div>
                        <button type="submit" :disabled="loading"
                            class="flex justify-center w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                            <svg v-if="loading" class="w-5 h-5 mr-3 -ml-1 text-white animate-spin"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            {{ loading ? $t('search.searching') : $t('search.search_button') }}
                        </button>
                    </div>
                </form>

                <!-- Search Results -->
                <div v-if="searchResult" class="mt-8">
                    <div v-if="searchResult.found" class="p-4 border border-red-200 rounded-md bg-red-50">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">{{ $t('search.device_found') }}</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <p><strong>{{ $t('search.brand') }}:</strong> {{ searchResult.device.brand }}</p>
                                    <p><strong>{{ $t('search.model') }}:</strong> {{ searchResult.device.model }}</p>
                                    <p><strong>{{ $t('search.status') }}:</strong> {{
                                        $t(`status.${searchResult.device.status}`) }}</p>
                                    <p><strong>{{ $t('search.reported_date') }}:</strong> {{
                                        formatDate(searchResult.device.created_at) }}</p>
                                    <p class="mt-2">{{ $t('search.contact_authorities') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="p-4 border border-green-200 rounded-md bg-green-50">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-green-400" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-green-800">{{ $t('search.device_not_found') }}</h3>
                                <div class="mt-2 text-sm text-green-700">
                                    <p>{{ searchResult.message }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const searchForm = ref({
    imei: '',
})

const searchResult = ref(null)
const loading = ref(false)

const searchDevice = async () => {
    loading.value = true
    searchResult.value = null

    try {
        const response = await axios.post('/api/search', searchForm.value)
        searchResult.value = response.data
    } catch (error) {
        console.error('Search error:', error)
    } finally {
        loading.value = false
    }
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString()
}
</script>
