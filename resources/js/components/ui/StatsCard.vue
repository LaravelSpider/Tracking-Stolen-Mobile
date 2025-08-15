<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
          {{ title }}
        </p>
        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
          {{ formattedValue }}
        </p>
      </div>
      <div class="flex-shrink-0">
        <div 
          class="w-12 h-12 rounded-lg flex items-center justify-center text-2xl"
          :class="iconClasses"
        >
        <component :is="icon" class="w-6 h-6" />
        </div>
      </div>
    </div>
    
    <div v-if="change" class="mt-4 flex items-center">
      <span 
        class="text-sm font-medium"
        :class="changeClasses"
      >
        {{ change > 0 ? '+' : '' }}{{ change }}%
      </span>
      <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">
        {{ $t('stats.fromLastMonth') }}
      </span>
    </div>
  </div>
</template>

<script>
export default {
  name: 'StatsCard',
  props: {
    title: {
      type: String,
      required: true
    },
    value: {
      type: [Number, String],
      required: true
    },
    icon: { 
      type: [Object, Function], 
      required: true 
    },
    color: {
      type: String,
      default: 'blue',
      validator: (value) => ['blue', 'green', 'yellow', 'red', 'purple', 'indigo', 'orange'].includes(value)
    },
    change: {
      type: Number,
      default: null
    }
  },
  computed: {
    formattedValue() {
      if (typeof this.value === 'string') {
        return this.value.toLocaleString()
      }
      return this.value
    },
    iconClasses() {
      const colorClasses = {
        blue: 'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300',
        green: 'bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300',
        yellow: 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-300',
        red: 'bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-300',
        purple: 'bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-300',
        indigo: 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300',
        orange: 'bg-orange-100 text-orange-600 dark:bg-orange-900 dark:text-orange-300'
      }
      return colorClasses[this.color] || colorClasses.blue
    },
    changeClasses() {
      if (this.change > 0) {
        return 'text-green-600 dark:text-green-400'
      } else if (this.change < 0) {
        return 'text-red-600 dark:text-red-400'
      }
      return 'text-gray-600 dark:text-gray-400'
    }
  }
}
</script>
