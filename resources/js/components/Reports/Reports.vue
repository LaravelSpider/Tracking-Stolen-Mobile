<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8" :class="{ 'rtl': $i18n.locale === 'ar' }">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            {{ $t('reports.title') }}
          </h1>
          <p class="mt-2 text-gray-600 dark:text-gray-400">
            {{ $t('reports.subtitle') }}
          </p>
        </div>
  
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <StatsCard
            v-if="stats"
            :title="$t('reports.stats.total')"
            :value="stats.total_reports"
            icon="document-text"
            color="blue"
          />
          <StatsCard
            v-if="stats"
            :title="$t('reports.stats.pending')"
            :value="stats.pending_reports"
            icon="clock"
            color="yellow"
          />
          <StatsCard
            v-if="stats"
            :title="$t('reports.stats.resolved')"
            :value="stats.resolved_reports"
            icon="check-circle"
            color="green"
          />
          <StatsCard
            v-if="stats"
            :title="$t('reports.stats.thisMonth')"
            :value="stats.this_month"
            icon="calendar"
            color="purple"
          />
        </div>
  
        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ $t('reports.filters.status') }}
              </label>
              <select
                v-model="filters.status"
                @change="loadReports"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
              >
                <option value="">{{ $t('reports.filters.allStatuses') }}</option>
                <option value="stolen">{{ $t('device.status.stolen') }}</option>
                <option value="investigating">{{ $t('device.status.investigating') }}</option>
                <option value="recovered">{{ $t('device.status.recovered') }}</option>
                <option value="closed">{{ $t('device.status.closed') }}</option>
              </select>
            </div>
  
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ $t('reports.filters.dateFrom') }}
              </label>
              <input
                v-model="filters.date_from"
                @change="loadReports"
                type="date"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
              />
            </div>
  
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ $t('reports.filters.dateTo') }}
              </label>
              <input
                v-model="filters.date_to"
                @change="loadReports"
                type="date"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
              />
            </div>
  
            <div class="flex items-end">
              <button
                @click="exportReports"
                :disabled="loading"
                class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ $t('reports.export') }}
              </button>
            </div>
          </div>
        </div>
  
        <!-- Reports Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
          <div v-if="loading" class="flex justify-center items-center py-12">
            <LoadingSpinner />
          </div>
  
          <div v-else-if="reports.length === 0" class="text-center py-12">
            <div class="text-gray-500 dark:text-gray-400">
              <svg class="mx-auto h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <p class="text-lg font-medium">{{ $t('reports.noReports') }}</p>
              <p class="text-sm">{{ $t('reports.noReportsDesc') }}</p>
            </div>
          </div>
  
          <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    {{ $t('reports.table.device') }}
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    {{ $t('reports.table.status') }}
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    {{ $t('reports.table.reporter') }}
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    {{ $t('reports.table.date') }}
                  </th>
                  <th v-if="canUpdateStatus" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    {{ $t('reports.table.actions') }}
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-for="report in reports" :key="report.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div>
                      <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ report.brand }} {{ report.model }}
                      </div>
                      <div class="text-sm text-gray-500 dark:text-gray-400">
                        IMEI: {{ report.imei }}
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <StatusBadge :status="report.status" />
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div>
                      <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ report.reporter.name }}
                      </div>
                      <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ report.reporter.email }}
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ formatDate(report.created_at) }}
                  </td>
                  <td v-if="canUpdateStatus" class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <select
                      :value="report.status"
                      @change="updateStatus(report.id, $event.target.value)"
                      class="px-2 py-1 border border-gray-300 dark:border-gray-600 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    >
                      <option value="stolen">{{ $t('device.status.stolen') }}</option>
                      <option value="investigating">{{ $t('device.status.investigating') }}</option>
                      <option value="recovered">{{ $t('device.status.recovered') }}</option>
                      <option value="closed">{{ $t('device.status.closed') }}</option>
                    </select>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
  
          <!-- Pagination -->
          <Pagination
            v-if="pagination && pagination.last_page > 1"
            :current-page="pagination.current_page"
            :total-pages="pagination.last_page"
            :total-items="pagination.total"
            @page-changed="changePage"
          />
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import { ref, onMounted, computed } from 'vue'
  import { useI18n } from 'vue-i18n'
  import { useAuthStore } from '@/stores/auth'
  import StatsCard from '@/components/ui/StatsCard.vue'
  import StatusBadge from '@/components/ui/StatusBadge.vue'
  import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'
  import Pagination from '@/components/ui/Pagination.vue'
  import { formatDate } from '@/utils/date'
  import api from '@/services/api'
  
  export default {
    name: 'Reports',
    components: {
      StatsCard,
      StatusBadge,
      LoadingSpinner,
      Pagination
    },
    setup() {
      const { t } = useI18n()
      const authStore = useAuthStore()
      
      const loading = ref(false)
      const reports = ref([])
      const stats = ref(null)
      const pagination = ref(null)
      
      const filters = ref({
        status: '',
        date_from: '',
        date_to: ''
      })
  
      const canUpdateStatus = computed(() => {
        return authStore.user?.role === 'admin' || authStore.user?.role === 'security_agency'
      })
  
      const loadReports = async (page = 1) => {
        loading.value = true
        try {
          const params = {
            page,
            ...filters.value
          }
          
          const response = await api.get('/reports', { params })
          reports.value = response.data.data
          pagination.value = {
            current_page: response.data.current_page,
            last_page: response.data.last_page,
            total: response.data.total
          }
        } catch (error) {
          console.error('Error loading reports:', error)
          alert(t('reports.errors.loadFailed'))
        } finally {
          loading.value = false
        }
      }
  
      const loadStats = async () => {
        try {
          const response = await api.get('/reports/stats')
          stats.value = response.data
        } catch (error) {
          console.error('Error loading stats:', error)
        }
      }
  
      const updateStatus = async (reportId, newStatus) => {
        try {
          await api.put(`/reports/${reportId}/status`, { status: newStatus })
          alert(t('reports.statusUpdated'))
          loadReports()
        } catch (error) {
          console.error('Error updating status:', error)
          alert(t('reports.errors.updateFailed'))
        }
      }
  
      const exportReports = async () => {
        try {
          const response = await api.get('/reports/export', { 
            params: filters.value 
          })
          
          // Convert to CSV and download
          const csvContent = convertToCSV(response.data.data)
          downloadCSV(csvContent, response.data.filename)
          
          alert(t('reports.exportSuccess'))
        } catch (error) {
          console.error('Error exporting reports:', error)
          alert(t('reports.errors.exportFailed'))
        }
      }
  
      const convertToCSV = (data) => {
        if (!data.length) return ''
        
        const headers = Object.keys(data[0])
        const csvHeaders = headers.join(',')
        const csvRows = data.map(row => 
          headers.map(header => `"${row[header] || ''}"`).join(',')
        )
        
        return [csvHeaders, ...csvRows].join('\n')
      }
  
      const downloadCSV = (content, filename) => {
        const blob = new Blob([content], { type: 'text/csv;charset=utf-8;' })
        const link = document.createElement('a')
        const url = URL.createObjectURL(blob)
        link.setAttribute('href', url)
        link.setAttribute('download', filename)
        link.style.visibility = 'hidden'
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
      }
  
      const changePage = (page) => {
        loadReports(page)
      }
  
      onMounted(() => {
        loadReports()
        loadStats()
      })
  
      return {
        loading,
        reports,
        stats,
        pagination,
        filters,
        canUpdateStatus,
        loadReports,
        updateStatus,
        exportReports,
        changePage,
        formatDate
      }
    }
  }
  </script>
  