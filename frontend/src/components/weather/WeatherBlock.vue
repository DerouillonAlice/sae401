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
          {{ getValue() }}
        </div>
        <div class="text-xs text-gray-600">
          {{ getUnit() }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
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

function formatTime(timestamp) {
  if (!timestamp) return '—'
  const date = new Date(timestamp * 1000)
  return `${date.getHours().toString().padStart(2, '0')}h${date.getMinutes().toString().padStart(2, '0')}`
}

function getSunriseTime() {
  // Priorité aux données actuelles du jour, sinon données de la ville
  const sunrise = props.currentDayData?.sys?.sunrise || props.cityData?.sunrise
  return formatTime(sunrise)
}

function getSunsetTime() {
  // Priorité aux données actuelles du jour, sinon données de la ville
  const sunset = props.currentDayData?.sys?.sunset || props.cityData?.sunset
  return formatTime(sunset)
}

function getValue() {
  if (!props.currentDayData) return '—'
  
  switch (props.name) {
    case 'Vent':
      return Math.round(props.currentDayData.wind?.speed * 3.6 || 0)
    case 'Humidité':
      return props.currentDayData.main?.humidity || 0
    case 'Visibilité':
      return Math.round((props.currentDayData.visibility || 0) / 1000)
    case 'Nuages':
      return props.currentDayData.clouds?.all || 0
    case 'Pression':
      return props.currentDayData.main?.pressure || 0
    default:
      return '—'
  }
}

function getUnit() {
  switch (props.name) {
    case 'Vent': return 'km/h'
    case 'Humidité': return '%'
    case 'Visibilité': return 'km'
    case 'Nuages': return '% couverture'
    case 'Pression': return 'hPa'
    default: return ''
  }
}
</script>