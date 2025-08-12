<template>
    <span :class="badgeClasses" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
        {{ statusText }}
    </span>
</template>

<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
    status: {
        type: String,
        required: true,
    },
})

const statusText = computed(() => {
    return t(`status.${props.status}`)
})

const badgeClasses = computed(() => {
    const statusClasses = {
        missing: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
        reported: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        investigating: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        found: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        closed: 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
    }
    return statusClasses[props.status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
})
</script>
