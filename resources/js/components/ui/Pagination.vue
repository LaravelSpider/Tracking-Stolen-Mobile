<template>
    <div
        class="flex items-center justify-between px-4 py-3 bg-white border-t border-gray-200 dark:bg-gray-800 dark:border-gray-700 sm:px-6">
        <div class="flex justify-between flex-1 sm:hidden">
            <button @click="goToPage(currentPage - 1)" :disabled="currentPage <= 1"
                class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md dark:border-gray-600 dark:text-gray-300 dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50">
                {{ $t('pagination.previous') }}
            </button>
            <button @click="goToPage(currentPage + 1)" :disabled="currentPage >= lastPage"
                class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md dark:border-gray-600 dark:text-gray-300 dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50">
                {{ $t('pagination.next') }}
            </button>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    {{ $t('pagination.showing') }}
                    <span class="font-medium">{{ startItem }}</span>
                    {{ $t('pagination.to') }}
                    <span class="font-medium">{{ endItem }}</span>
                    {{ $t('pagination.of') }}
                    <span class="font-medium">{{ total }}</span>
                    {{ $t('pagination.results') }}
                </p>
            </div>
            <div>
                <nav class="relative z-0 inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                    <button @click="goToPage(currentPage - 1)" :disabled="currentPage <= 1"
                        class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50">
                        <ChevronLeftIcon class="w-5 h-5" />
                    </button>

                    <button v-for="page in visiblePages" :key="page" @click="goToPage(page)" :class="[
                        page === currentPage
                            ? 'z-10 bg-blue-50 dark:bg-blue-900 border-blue-500 text-blue-600 dark:text-blue-200'
                            : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700',
                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                    ]">
                        {{ page }}
                    </button>

                    <button @click="goToPage(currentPage + 1)" :disabled="currentPage >= lastPage"
                        class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50">
                        <ChevronRightIcon class="w-5 h-5" />
                    </button>
                </nav>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    currentPage: {
        type: Number,
        required: true,
    },
    lastPage: {
        type: Number,
        required: true,
    },
    total: {
        type: Number,
        required: true,
    },
    perPage: {
        type: Number,
        default: 15,
    },
})

const emit = defineEmits(['page-changed'])

const startItem = computed(() => {
    return (props.currentPage - 1) * props.perPage + 1
})

const endItem = computed(() => {
    return Math.min(props.currentPage * props.perPage, props.total)
})

const visiblePages = computed(() => {
    const pages = []
    const start = Math.max(1, props.currentPage - 2)
    const end = Math.min(props.lastPage, props.currentPage + 2)

    for (let i = start; i <= end; i++) {
        pages.push(i)
    }

    return pages
})

const goToPage = (page) => {
    if (page >= 1 && page <= props.lastPage && page !== props.currentPage) {
        emit('page-changed', page)
    }
}
</script>
