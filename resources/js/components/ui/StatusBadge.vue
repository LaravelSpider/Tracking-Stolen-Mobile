<template>
  <span 
    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
    :class="badgeClasses"
  >
    <span class="w-2 h-2 rounded-full mr-1.5" :class="dotClasses"></span>
    {{ statusText }}
  </span>
</template>

<script>
import { computed } from 'vue'
import { useI18nStore } from '../../stores/i18n'

export default {
  name: 'StatusBadge',
  props: {
    status: {
      type: String,
      required: true,
      validator: (value) => ['investigating', 'recovered', 'closed', 'missing'].includes(value)
    }
  },
  setup(props) {
    const i18n = useI18nStore()
    const t = i18n.t
    // const { t } = useI18nStore()
    
    const statusConfig = computed(() => ({
      investigating: {
        text: t?.('status.investigating') || '',
        badgeClass: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        dotClass: 'bg-yellow-400'
      },
      recovered: {
        text: t?.('status.recovered') || '',
        badgeClass: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        dotClass: 'bg-green-400'
      },
      closed: {
        text: t?.('status.closed') || '',
        badgeClass: 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
        dotClass: 'bg-gray-400'
      },
      missing: {
        text: t?.('status.missing') || '',
        badgeClass: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
        dotClass: 'bg-red-400'
      }
    }))
    
    const  statusText = computed(() => statusConfig.value[props.status]?.text || props.status)
    const  badgeClasses = computed(() => statusConfig.value[props.status]?.badgeClass ?? statusConfig.value.missing.badgeClass)
    const  dotClasses = computed(() => statusConfig.value[props.status]?.dotClass ?? statusConfig.value.missing.dotClass)

    return { statusText, badgeClasses, dotClasses }
  }
}

</script>
