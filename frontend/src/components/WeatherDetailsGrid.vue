<template>
  <div class="flex flex-col">
    <div class="flex flex-1 overflow-hidden">
      <main class="flex-1 overflow-auto sm:overflow-clip">
        <div class="w-full flex flex-col gap-4 mx-auto">
          <!-- Configuration des blocs pour utilisateurs connectés -->
          <BlockSelector 
            v-if="auth.isConnected"
            v-model:blocks="allBlocks"
          />

          <div class="flex flex-col lg:flex-row gap-4 items-stretch">
            <!-- Carte météo principale -->
            <WeatherMainCard
              :current-day-data="currentDayData"
              :day-forecast-data="dayForecastData"
              :selected-day-index="selectedDayIndex"
              :image-url="imageUrl"
              :is-connected="auth.isConnected"
              :has-layout="layout.length > 0"
              @get-date-info="getDateInfo"
            />

            <!-- Grille personnalisable pour utilisateurs connectés -->
            <WeatherGrid
              v-if="auth.isConnected && layout.length > 0"
              v-model:layout="layout"
              :all-blocks="allBlocks"
              :current-day-data="currentDayData"
              :city-data="cityData"
              :forecast-data="forecastData"
              :selected-day-index="selectedDayIndex"
              :col-num="colNum"
              :container-width="containerWidth"
              @update-layout="updateLayout"
            />

            <!-- Blocs par défaut pour utilisateurs non connectés OU connectés sans layout -->
            <DefaultWeatherBlocks
              v-else
              :all-blocks="allBlocks"
              :current-day-data="currentDayData"
              :city-data="cityData"
              :forecast-data="forecastData"
              :selected-day-index="selectedDayIndex"
              @go-to-login="goToLogin"
              @go-to-register="goToRegister"
            />
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useWeatherImage } from '@/composables/useWeatherImage'
import { useAuthStore } from '@/stores/auth'
import { useWeatherData } from '@/composables/useWeatherData'
import { useWeatherLayout } from '@/composables/useWeatherLayout'

import WeatherMainCard from './weather/WeatherMainCard.vue'
import WeatherGrid from './weather/WeatherGrid.vue'
import DefaultWeatherBlocks from './weather/DefaultWeatherBlocks.vue'
import BlockSelector from './weather/BlockSelector.vue'

const props = defineProps({
  selectedDayIndex: {
    type: Number,
    default: 0
  }
})

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()

const selectedDayIndex = ref(props.selectedDayIndex)

// Utilisation des composables
const {
  weatherData,
  forecastData,
  todayHourlyData,
  cityData,
  fetchWeather,
  fetchForecast,
  getDefaultVille
} = useWeatherData(route, auth)

const {
  allBlocks,
  layout,
  colNum,
  containerWidth,
  updateLayout,
  loadFavoriteConfig
} = useWeatherLayout(route, auth, weatherData, forecastData)

// Données calculées
const currentDayData = computed(() => {
  if (selectedDayIndex.value === 0) {
    return weatherData.value
  } else {
    return forecastData.value[selectedDayIndex.value - 1] || null
  }
})

const dayForecastData = computed(() => {
  if (selectedDayIndex.value === 0) {
    return todayHourlyData.value
  } else {
    const dayData = forecastData.value[selectedDayIndex.value - 1]
    if (!dayData) return []

    const baseTemp = dayData.main?.temp || 20
    return [
      {
        label: 'Matin',
        temp: baseTemp - 3,
        icon: dayData.weather?.[0]?.icon
      },
      {
        label: 'Après-midi',
        temp: baseTemp + 2,
        icon: dayData.weather?.[0]?.icon
      },
      {
        label: 'Soir',
        temp: baseTemp - 1,
        icon: dayData.weather?.[0]?.icon
      }
    ]
  }
})

const { imageUrl } = useWeatherImage(currentDayData)

// Méthodes
const goToRegister = () => {
  router.push({ path: '/inscription' })
}

const goToLogin = () => {
  router.push({ path: '/connexion' })
}

function getDateInfo() {
  if (selectedDayIndex.value === 0) {
    if (!weatherData.value?.dt) return '—'
    const date = new Date(weatherData.value.dt * 1000)
    return `${date.getHours()}h${date.getMinutes().toString().padStart(2, '0')}`
  } else {
    const dayData = forecastData.value[selectedDayIndex.value - 1]
    if (!dayData) return '—'
    return dayData.date.toLocaleDateString('fr-FR', {
      weekday: 'long',
      day: 'numeric',
      month: 'long'
    })
  }
}

// Watchers et lifecycle
watch(() => props.selectedDayIndex, (newIndex) => {
  selectedDayIndex.value = newIndex
})

watch(
  () => route.query.ville,
  () => {
    fetchWeather()
    fetchForecast()
  },
  { immediate: true }
)

onMounted(() => {
  if (localStorage.getItem('token')) {
    auth.checkAuth()
  }
  fetchWeather()
  fetchForecast()
  loadFavoriteConfig()
})
</script>

