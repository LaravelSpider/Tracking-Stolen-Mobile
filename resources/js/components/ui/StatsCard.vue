<template>
    <div class="overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div :class="iconClasses" class="flex items-center justify-center w-8 h-8 rounded-md">
                        <component :is="iconComponent" class="w-5 h-5 text-white" />
                    </div>
                </div>
                <div class="flex-1 w-0 ml-5">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">
                            {{ title }}
                        </dt>
                        <dd class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ formattedValue }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import {
    DevicePhoneMobileIcon,
    ExclamationTriangleIcon,
    DocumentTextIcon,
    MagnifyingGlassIcon,
    CheckCircleIcon,
    FireIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    value: {
        type: [Number, String],
        required: true,
    },
    icon: {
        type: String,
        required: true,
    },
    color: {
        type: String,
        default: 'blue',
    },
})

const iconComponents = {
    DevicePhoneMobileIcon,
    ExclamationTriangleIcon,
    DocumentTextIcon,
    MagnifyingGlassIcon,
    CheckCircleIcon,
    FireIcon,
}

const iconComponent = computed(() => iconComponents[props.icon] || DevicePhoneMobileIcon)

const iconClasses = computed(() => {
    const colorClasses = {
        blue: 'bg-blue-500',
        red: 'bg-red-500',
        yellow: 'bg-yellow-500',
        green: 'bg-green-500',
        orange: 'bg-orange-500',
        purple: 'bg-purple-500',
    }
    return colorClasses[props.color] || 'bg-blue-500'
})

const formattedValue = computed(() => {
    if (typeof props.value === 'number') {
        return props.value.toLocaleString()
    }
    return props.value
}) 
</script>
