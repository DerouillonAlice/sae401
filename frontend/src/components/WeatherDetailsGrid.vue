<template>
  <div class="flex flex-col">
    <div class="flex flex-1">
      <main class="flex-1 ">
        <div class="w-full flex flex-col gap-4 mx-auto">
          <BlockSelector 
            v-if="auth.isConnected"
            v-model:blocks="allBlocks"
          />

          <div class="flex flex-col lg:flex-row gap-4 items-stretch">
            <WeatherMainCard
              :current-day-data="currentDayData"
              :day-forecast-data="dayForecastData"
              :selected-day-index="selectedDayIndex"
              :image-url="imageUrl"
              :is-connected="auth.isConnected"
              :has-layout="layout.length > 0"
              :get-date-info="getDateInfo"
            />

            <div 
              v-if="auth.isConnected && layout.length > 0"
              class="flex-1 lg:flex-[2] w-full"
            >
              <WeatherGrid
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
            </div>

            <div v-else-if="auth.isConnected && layout.length === 0" 
                 class="flex-1 lg:flex-[2] flex flex-col gap-4">
              <div class="flex flex-col items-center justify-center rounded-2xl border shadow bg-white/60 backdrop-blur-md p-8 text-black text-center h-full min-h-[300px]">
                <div class="text-xl font-bold mb-2">Aucun bloc météo sélectionné</div>
                <div class="text-sm text-gray-600 mb-4">
                  Utilisez la configuration ci-dessus pour activer les blocs météo que vous souhaitez afficher.
                </div>
                <div class="text-lg">📊</div>
              </div>
            </div>

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

const {
  weatherData,
  forecastData,
  todayHourlyData,
  cityData,
  fetchWeather,
  fetchForecast,
  getDefaultVille,
  getHourlyDataForDay
} = useWeatherData(route, auth)

const {
  allBlocks,
  layout,
  colNum,
  containerWidth,
  updateLayout,
  loadFavoriteConfig
} = useWeatherLayout(route, auth)

const currentDayData = computed(() => {
  if (selectedDayIndex.value === 0) {
    return weatherData.value
  } else {
    return forecastData.value[selectedDayIndex.value - 1] || null
  }
})

const dayForecastData = computed(() => {
  return getHourlyDataForDay(selectedDayIndex.value)
})

const { imageUrl } = useWeatherImage(currentDayData)

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

