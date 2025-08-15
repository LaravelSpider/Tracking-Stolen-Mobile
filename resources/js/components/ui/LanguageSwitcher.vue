<template>
  <div class="relative">
    <button @click="toggleDropdown"
      class="flex items-center p-1 text-sm text-gray-500 rounded-md hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
      <GlobeAltIcon class="w-5 h-5 mr-1" />
      <span class="hidden sm:block">{{ currentLanguage }}</span>
      <ChevronDownIcon class="w-4 h-4 ml-1" />
    </button>

    <div v-show="showDropdown"
      class="absolute right-0 z-50 w-48 mt-2 bg-white rounded-md shadow-lg dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none"
      @click.away="showDropdown = false">
      <div class="py-1">
        <button v-for="language in languages" :key="language.code" @click="changeLanguage(language.code)" :class="[
          language.code === currentLocale
            ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white'
            : 'text-gray-700 dark:text-gray-300',
          'group flex items-center px-4 py-2 text-sm w-full text-left hover:bg-gray-100 dark:hover:bg-gray-700'
        ]">
          <span class="mr-3 text-lg">{{ language.flag }}</span>
          {{ language.name }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { GlobeAltIcon, ChevronDownIcon } from '@heroicons/vue/24/outline'
import { useI18nStore } from '../../stores/i18n'

const i18nStore = useI18nStore()
const showDropdown = ref(false)

const languages = [
  { code: 'ar', name: 'العربية', flag: 'SD' },
  { code: 'en', name: 'English', flag: 'US' },
]

const currentLocale = computed(() => i18nStore.currentLocale)

const currentLanguage = computed(() => {
  const lang = languages.find(l => l.code === currentLocale.value)
  return lang ? lang.name : 'العربية'
})

const toggleDropdown = () => {
  showDropdown.value = !showDropdown.value
}

const changeLanguage = (locale) => {
  i18nStore.setLocale(locale)
  showDropdown.value = false
}
</script>
