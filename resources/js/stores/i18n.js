import { defineStore } from 'pinia'
import ar from './locales/ar.json'
import en from './locales/en.json'

export const useI18nStore = defineStore('i18n', {
  state: () => ({
    legacy: false,
    globalInjection: true,
    currentLocale: localStorage.getItem('locale') || 'ar',
    locales: ['ar', 'en'],
    translations: {
      ar,
      en
    }
  }),

  actions: {
    setLocale(locale) {
      if (this.locales.includes(locale)) {
        this.currentLocale = locale
        localStorage.setItem('locale', locale)
      }
    },

    t(key) {
      const keys = key.split('.')
      let translation = this.translations[this.currentLocale]
      
      for (const k of keys) {
        if (translation && translation[k]) {
          translation = translation[k]
        } else {
          return key
        }
      }
      
      return translation
    }
  }
});