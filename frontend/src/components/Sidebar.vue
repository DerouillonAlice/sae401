<script setup>
import CityCard from './CityCard.vue'
import { ref, computed, onMounted, watch } from 'vue'
import { useSidebar } from '@/composables/useSidebar'
import { useAuthStore } from '@/stores/auth'
import { ChevronLeftIcon } from '@heroicons/vue/24/outline'
import sunImg from '../assets/sun.png'
import rainImg from '../assets/rain.png'
import lightRainImg from '../assets/light-rain.png'
import axios from 'axios'

const { isSidebarOpen } = useSidebar()
const auth = useAuthStore()

const grandesVilles = [
  { name: 'Paris' },
  { name: 'Londres' },
  { name: 'New York' },
  { name: 'Tokyo' },
  { name: 'Sydney' }
]

const meteoVilles = ref({})
const meteoFavoris = ref({})
const favoris = ref([])

function getWeatherIcon(weather) {
  if (!weather) return sunImg
  const main = weather.weather?.[0]?.main?.toLowerCase() || ''
  if (main.includes('rain')) return rainImg
  if (main.includes('cloud')) return lightRainImg
  if (main.includes('clear')) return sunImg
  return sunImg
}

const fetchMeteo = async (ville) => {
  try { 
    const res = await axios.get(`/api/weather/${encodeURIComponent(ville)}`)
    console.log('Météo reçue pour', ville, res.data)
    meteoVilles.value = { ...meteoVilles.value, [ville]: res.data }
  } catch {
    meteoVilles.value = { ...meteoVilles.value, [ville]: { error: 'Erreur météo' } }
  }
}

const fetchMeteoFavori = async (ville) => {
  try {
    const res = await axios.get(`/api/weather/${encodeURIComponent(ville)}`)
    meteoFavoris.value[ville] = res.data
  } catch {
    meteoFavoris.value[ville] = { error: 'Erreur météo' }
  }
}

const fetchFavoris = async () => {
  try {
    const token = localStorage.getItem('token')
    if (!token) return
    const res = await axios.get('http://localhost:8319/api/favorites', {
      headers: { Authorization: `Bearer ${token}` }
    })
    favoris.value = res.data
    favoris.value.forEach(fav => fetchMeteoFavori(fav.city))
  } catch {
    favoris.value = []
  }
}

watch(
  () => auth.isConnected,
  (connected) => {
    if (connected) {
      fetchFavoris()
    } else {
      grandesVilles.forEach(ville => {
        if (!meteoVilles.value[ville.name]) {
          fetchMeteo(ville.name)
        }
      })
    }
  },
  { immediate: true }
)

const cities = computed(() => {
  const list = auth.isConnected
    ? favoris.value.map(fav => ({
        name: fav.city,
        meteo: meteoFavoris.value[fav.city],
        imageSrc: getWeatherIcon(meteoFavoris.value[fav.city])
      }))
    : grandesVilles.map(ville => ({
        name: ville.name,
        meteo: meteoVilles.value[ville.name],
        imageSrc: getWeatherIcon(meteoVilles.value[ville.name])
      }))

  return list
})

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}

</script>

<template>
  <main class="flex flex-col justify-center py-4 h-full relative">
    <section
      class="absolute left-0 grid grid-cols-[1fr_auto] mx-auto gap-2 h-[95vh] w-full p-4 rounded-r-xl bg-white/40 backdrop-blur-md shadow-xl transition-transform duration-300 ease-in-out border border-white/70"
    >
      <div v-if="isSidebarOpen" class="flex flex-col gap-4">
        <TransitionGroup name="fade" tag="div" class="contents">
          <CityCard
  v-for="city in cities"
  :key="city.name"
  :name="city.name"
>
  <template #default>
    <div>
      <span v-if="city.meteo && city.meteo.main && city.meteo.weather">
        {{ Math.round(city.meteo.main.temp) }}°C
        <img
          :src="`https://openweathermap.org/img/wn/${city.meteo.weather[0].icon}@2x.png`"
          :alt="city.meteo.weather[0].description"
          class="w-8 h-8 inline-block align-middle ml-2"
        />
      </span>
      <span v-else class="text-xs text-gray-400">Chargement...</span>
    </div>
  </template>
</CityCard>
          <a href="/connexion">
          <CityCard
            v-if="!auth.isConnected"
            name="Connectez-vous pour ajouter vos favoris"
            imageSrc=""
            isPlaceholder
            customClass="connect-card"
          />
        </a>
          <CityCard
            v-if="auth.isConnected"
            name="Ajouter une ville à vos favoris"
            isPlaceholder
            customClass="connect-card"
          />
        </TransitionGroup>
      </div>

      <div class="flex items-center justify-center">
        <button
          @click="toggleSidebar"
          class="rounded-full hover:bg-white/10 transition-colors duration-200 w-7 h-7 flex items-end justify-end"
        >
          <ChevronLeftIcon
            class="w-6 h-6 transition-transform duration-300"
            :class="{ 'rotate-180': !isSidebarOpen }"
          />
        </button>
      </div>
    </section>
  </main>
</template>

<style scoped>

</style>
