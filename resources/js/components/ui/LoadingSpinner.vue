<template>
  <div class="flex items-center justify-center" :class="containerClass">
    <div class="border-b-2 rounded-full animate-spin" :class="spinnerClass">
    </div>
    <span v-if="text" class="ml-3 text-sm text-gray-600 dark:text-gray-400">
      {{ text }}
    </span>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value),
  },
  text: {
    type: String,
    default: '',
  },
  color: {
    type: String,
    default: 'blue',
  },
})

const containerClass = computed(() => {
  const sizeClasses = {
    sm: 'p-2',
    md: 'p-4',
    lg: 'p-6',
    xl: 'p-8',
  }
  return sizeClasses[props.size]
})

const spinnerClass = computed(() => {
  const sizeClasses = {
    sm: 'h-4 w-4',
    md: 'h-8 w-8',
    lg: 'h-12 w-12',
    xl: 'h-16 w-16',
  }

  const colorClasses = {
    blue: 'border-blue-500',
    red: 'border-red-500',
    green: 'border-green-500',
    yellow: 'border-yellow-500',
    purple: 'border-purple-500',
  }

  return `${sizeClasses[props.size]} ${colorClasses[props.color]}`
})
</script>
