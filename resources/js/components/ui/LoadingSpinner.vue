<template>
  <div class="flex items-center justify-center" :class="containerClass">
    <div class="relative">
      <div 
        class="animate-spin rounded-full border-4 border-solid border-current border-r-transparent"
        :class="spinnerClass"
      ></div>
      <div v-if="text" class="mt-4 text-center">
        <p class="text-sm text-gray-600 dark:text-gray-400">{{ text }}</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'LoadingSpinner',
  props: {
    size: {
      type: String,
      default: 'md',
      validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value)
    },
    color: {
      type: String,
      default: 'blue',
      validator: (value) => ['blue', 'green', 'yellow', 'red', 'purple', 'gray'].includes(value)
    },
    text: {
      type: String,
      default: null
    },
    fullScreen: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    containerClass() {
      return this.fullScreen ? 'fixed inset-0 bg-white dark:bg-gray-900 bg-opacity-75 z-50' : 'py-4'
    },
    spinnerClass() {
      const sizeClasses = {
        sm: 'h-6 w-6',
        md: 'h-8 w-8',
        lg: 'h-12 w-12',
        xl: 'h-16 w-16'
      }
      
      const colorClasses = {
        blue: 'text-blue-600',
        green: 'text-green-600',
        yellow: 'text-yellow-600',
        red: 'text-red-600',
        purple: 'text-purple-600',
        gray: 'text-gray-600'
      }
      
      return `${sizeClasses[this.size]} ${colorClasses[this.color]}`
    }
  }
}
</script>
