<template>
  <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
    Header
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
        {{ $t('dashboard.title') }}
      </h1>
      <p class="mt-2 text-gray-600 dark:text-gray-400">
        {{ $t('dashboard.subtitle') }}
      </p>
    </div>

    Stats Cards
    <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6">
      <StatsCard :title="$t('dashboard.total_devices')" :value="stats.total_devices" icon="DevicePhoneMobileIcon"
        color="blue" />
      <StatsCard :title="$t('dashboard.missing')" :value="stats.missing" icon="ExclamationTriangleIcon" color="red" />
      <StatsCard :title="$t('dashboard.reported')" :value="stats.reported" icon="DocumentTextIcon" color="yellow" />
      <StatsCard :title="$t('dashboard.investigating')" :value="stats.investigating" icon="MagnifyingGlassIcon"
        color="blue" />
      <StatsCard :title="$t('dashboard.found')" :value="stats.found" icon="CheckCircleIcon" color="green" />
      <StatsCard :title="$t('dashboard.high_priority')" :value="stats.high_priority" icon="FireIcon" color="orange" />
    </div>

    Charts and Recent Reports
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
      Monthly Chart
      <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
        <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">
          {{ $t('dashboard.monthly_reports') }}
        </h3>
        <MonthlyChart :data="stats.monthly_stats" />
      </div>

      Recent Reports
      <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white">
            {{ $t('dashboard.recent_reports') }}
          </h3>
          <router-link to="/devices" class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400">
            {{ $t('dashboard.view_all') }}
          </router-link>
        </div>

        <div class="space-y-4">
          <div v-for="device in stats.recent_reports" :key="device.id"
            class="flex items-center p-3 space-x-4 transition-colors rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
            <div class="flex-shrink-0">
              <div class="flex items-center justify-center w-10 h-10 bg-gray-200 rounded-full dark:bg-gray-600">
                <DevicePhoneMobileIcon class="w-5 h-5 text-gray-600 dark:text-gray-300" />
              </div>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                {{ device.brand }} {{ device.model }}
              </p>
              <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                {{ $t('dashboard.reported_by') }}: {{ device.reporter.name }}
              </p>
              <p class="text-xs text-gray-400 dark:text-gray-500">
                {{ formatDate(device.created_at) }}
              </p>
            </div>
            <div class="flex-shrink-0">
              <StatusBadge :status="device.status" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useQuery } from '@tanstack/vue-query'
import {
  DevicePhoneMobileIcon,
  ExclamationTriangleIcon,
  DocumentTextIcon,
  MagnifyingGlassIcon,
  CheckCircleIcon,
  FireIcon,
} from '@heroicons/vue/24/outline'
import { StatsCard } from '../ui/StatsCard.vue'
import { StatusBadge } from '../ui/StatusBadge.vue'
import { MonthlyChart } from '../charts/MonthlyChart.vue'
import { dashboardApi } from '../services/api'
import { formatDate } from '../../utils/date'

const { data: stats, isLoading, error } = useQuery({
  queryKey: ['dashboard'],
  queryFn: dashboardApi.getStats,
  refetchInterval: 30000, // Refetch every 30 seconds
})
</script>
