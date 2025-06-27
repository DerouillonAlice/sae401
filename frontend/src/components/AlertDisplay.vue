<template>
  <div v-if="alerts.length > 0" class="fixed top-4 right-4 z-50 space-y-2">
    <div 
      v-for="alert in alerts" 
      :key="alert.id"
      :class="getAlertClass(alert.severity)"
      class="max-w-sm p-4 rounded-lg shadow-lg border-l-4"
    >
      <div class="flex items-start">
        <div class="flex-shrink-0">
          <span class="text-xl">{{ getAlertIcon(alert.type) }}</span>
        </div>
        <div class="ml-3 flex-1">
          <h4 class="text-sm font-semibold">{{ alert.title }}</h4>
          <p class="text-xs mt-1">{{ alert.message }}</p>
          <p class="text-xs text-gray-500 mt-1">{{ alert.location }} - {{ alert.timestamp }}</p>
          <span v-if="alert.isTest" class="inline-block px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded mt-2">
            TEST
          </span>
        </div>
        <button @click="removeAlert(alert.id)" class="ml-2 text-gray-400 hover:text-gray-600">
          ✕
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const alerts = ref([])

const getAlertClass = (severity) => {
  const classes = {
    minor: 'bg-yellow-50 border-yellow-400 text-yellow-800',
    moderate: 'bg-orange-50 border-orange-400 text-orange-800',
    severe: 'bg-red-50 border-red-400 text-red-800',
    extreme: 'bg-purple-50 border-purple-400 text-purple-800'
  }
  return classes[severity] || classes.moderate
}

const getAlertIcon = (type) => {
  const icons = {
    thunderstorm: '⛈️',
    tornado: '🌪️',
    hurricane: '🌀',
    wind: '💨',
    rain: '🌧️',
    flood: '🌊',
    snow: '❄️',
    ice: '🧊',
    fog: '🌫️',
    heat: '🔥',
    cold: '🥶'
  }
  return icons[type] || '⚠️'
}

const addAlert = (alert) => {
  alerts.value.push(alert)
  setTimeout(() => {
    removeAlert(alert.id)
  }, 10000)
}

const removeAlert = (id) => {
  const index = alerts.value.findIndex(alert => alert.id === id)
  if (index > -1) {
    alerts.value.splice(index, 1)
  }
}

defineExpose({
  addAlert,
  removeAlert,
  alerts
})
</script>