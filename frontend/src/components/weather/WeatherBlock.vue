<template>
  <div class="bg-white/60 backdrop-blur-md rounded-2xl border shadow p-4 h-full flex flex-col justify-between">
    <div class="flex items-center justify-between mb-2">
      <h4 class="text-sm font-semibold text-gray-700">{{ name }}</h4>
    </div>
    
    <div class="flex-1 flex flex-col justify-center">
      <!-- Affichage spécial pour le cycle soleil -->
      <div v-if="name === 'Cycle Soleil'" class="space-y-2">
        <div class="text-center">
          <div class="text-lg font-bold text-gray-800">
            {{ getSunriseTime() }}
          </div>
        </div>
        <div class="text-center">
          <div class="text-lg font-bold text-gray-800">
            {{ getSunsetTime() }}
          </div>
        </div>
      </div>
      
      <!-- Affichage normal pour les autres blocs -->
      <div v-else>
        <div class="text-xl font-bold text-gray-800 mb-1">
          {{ getValueOnly() }}
        </div>
        <div class="text-xs text-gray-600">
          {{ getUnit() }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useUnitFormatters } from '@/services/unitUtils'
import { useAuthStore } from '@/stores/auth'

const props = defineProps({
  name: {
    type: String,
    required: true
  },
  currentDayData: {
    type: Object,
    default: null
  },
  cityData: {
    type: Object,
    default: null
  },
  forecastData: {
    type: Array,
    default: () => []
  },
  selectedDayIndex: {
    type: Number,
    default: 0
  }
})

const { formatTemperature, formatWind, formatPressure } = useUnitFormatters()

function formatTime(timestamp) {
  if (!timestamp) return '—'
  const date = new Date(timestamp * 1000)
  return `${date.getHours().toString().padStart(2, '0')}h${date.getMinutes().toString().padStart(2, '0')}`
}

function getSunriseTime() {
  const sunrise = props.currentDayData?.sys?.sunrise || props.cityData?.sunrise
  return formatTime(sunrise)
}

function getSunsetTime() {
  const sunset = props.currentDayData?.sys?.sunset || props.cityData?.sunset
  return formatTime(sunset)
}

// Fonction pour obtenir seulement la valeur (sans unité)
function getValueOnly() {
  if (!props.currentDayData) return '—'
  
  switch (props.name) {
    case 'Vent':
      const windSpeed = props.currentDayData.wind?.speed || 0
      const auth = useAuthStore()
      return auth.user?.uniteVent === 'km/h' 
        ? Math.round(windSpeed * 3.6) 
        : Math.round(windSpeed)
    case 'Humidité':
      return (props.currentDayData.main?.humidity || 0)
    case 'Visibilité':
      return Math.round((props.currentDayData.visibility || 0) / 1000)
    case 'Nuages':
      return (props.currentDayData.clouds?.all || 0)
    case 'Pression':
      const pressure = props.currentDayData.main?.pressure || 0
      const authStore = useAuthStore()
      return authStore.user?.unitePression === 'mmHg' 
        ? Math.round(pressure * 0.750062) 
        : Math.round(pressure)
    default:
      return '—'
  }
}

// Fonction pour obtenir seulement l'unité
function getUnit() {
  const auth = useAuthStore()
  
  switch (props.name) {
    case 'Vent':
      return auth.user?.uniteVent === 'km/h' ? 'km/h' : 'm/s'
    case 'Humidité':
      return '%'
    case 'Visibilité':
      return 'km'
    case 'Nuages':
      return '% couverture'
    case 'Pression':
      return auth.user?.unitePression === 'mmHg' ? 'mmHg' : 'hPa'
    default:
      return ''
  }
}

// Garder l'ancienne fonction pour compatibilité
function getValue() {
  if (!props.currentDayData) return '—'
  
  switch (props.name) {
    case 'Vent':
      return formatWind.value(props.currentDayData.wind?.speed || 0)
    case 'Humidité':
      return (props.currentDayData.main?.humidity || 0) + '%'
    case 'Visibilité':
      return Math.round((props.currentDayData.visibility || 0) / 1000) + ' km'
    case 'Nuages':
      return (props.currentDayData.clouds?.all || 0) + '% couverture'
    case 'Pression':
      return formatPressure.value(props.currentDayData.main?.pressure || 0)
    default:
      return '—'
  }
}
</script>