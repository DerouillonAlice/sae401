<script setup>
import Navbar from './components/Navbar.vue'
import Footer from './components/Footer.vue'
import Sidebar from './components/Sidebar.vue'
import fond from './assets/fond.png'
import { useSidebar } from './composables/useSidebar'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Pagination } from 'swiper/modules'
import 'swiper/swiper-bundle.css'
import 'swiper/css/pagination'
import { useAuthStore } from '@/stores/auth'
import { ref, computed, watch } from 'vue'
import { useWeatherImage } from '@/composables/useWeatherImage'
import { useRouter, useRoute } from 'vue-router'
import { formatTemperature } from '@/services/unitUtils'

const { isSidebarOpen } = useSidebar()
const auth = useAuthStore()
const router = useRouter()
const route = useRoute()

const isHomeView = computed(() => route.name === 'HomeView')

const grandesVilles = [
  { name: 'Paris' },
  { name: 'Londres' },
  { name: 'New York' },
  { name: 'Tokyo' },
  { name: 'Sydney' }
]

const meteoVilles = ref({})
const meteoFavoris = ref({})

async function fetchMeteo(ville) {
  try {
    const { getWeatherByCity } = await import('@/services/services')
    const res = await getWeatherByCity(ville)
    meteoVilles.value = { ...meteoVilles.value, [ville]: res.data }
  } catch {
    meteoVilles.value = { ...meteoVilles.value, [ville]: { error: 'Erreur météo' } }
  }
}

async function fetchMeteoFavori(ville) {
  try {
    const { getWeatherByCity } = await import('@/services/services')
    const res = await getWeatherByCity(ville)
    meteoFavoris.value[ville] = res.data
  } catch {
    meteoFavoris.value[ville] = { error: 'Erreur météo' }
  }
}

watch(
  () => auth.favorites,
  (newFavorites) => {
    newFavorites.forEach(fav => {
      if (!meteoFavoris.value[fav.city]) {
        fetchMeteoFavori(fav.city)
      }
    })
  },
  { immediate: true, deep: true }
)

watch(
  () => auth.isConnected,
  (connected) => {
    if (!connected) {
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
  return auth.isConnected
    ? auth.favorites.map(fav => {
      const meteo = meteoFavoris.value[fav.city]
      const { imageUrl } = useWeatherImage(ref(meteo))
      return {
        id: fav.id,
        name: fav.city,
        meteo,
        imageSrc: imageUrl.value
      }
    })
    : grandesVilles.map(ville => {
      const meteo = meteoVilles.value[ville.name]
      const { imageUrl } = useWeatherImage(ref(meteo))
      return {
        name: ville.name,
        meteo,
        imageSrc: imageUrl.value
      }
    })
})

function goToCity(cityName) {
  router.push({
    name: 'HomeView',
    query: {
      ville: cityName,
      t: Date.now()
    }
  })
}

import { inject } from 'vue'
const openAddFavoriteModal = inject('openAddFavoriteModal', () => {})
</script>

<template>
  <div id="app" class="min-h-screen w-full flex bg-cover bg-center" :style="{ backgroundImage: `url('${fond}')` }">
    <Sidebar class="hidden md:block" />

    <div 
      class="flex flex-col flex-1 min-h-screen w-full transition-all duration-300"
      :class="{
        'md:ml-64': isSidebarOpen,
        'md:ml-12': !isSidebarOpen
      }"
    >

      <Navbar />

      <div v-if="isHomeView" class="md:hidden flex flex-col items-center justify-center">
        <Swiper
          :slides-per-view="1"
          :space-between="8"
          class="w-full h-24"
          :modules="[Pagination]"
          :loop="true"
          :pagination="{ clickable: true }"
        >
          <SwiperSlide
            v-for="city in cities"
            :key="city.id || city.name"
            class="flex justify-center"
          >
            <div
              class="w-full mx-4 bg-white/40 rounded-md shadow flex flex-row items-center px-4 py-4 cursor-pointer"
              @click="goToCity(city.name)"
              style="min-height: 90px;"
            >
              <div class="flex flex-col flex-1 min-w-0">
                <span class="font-bold truncate text-lg sm:text-xl md:text-2xl mb-1">{{ city.name }}</span>
                <span
                  v-if="city.meteo && city.meteo.main && city.meteo.weather"
                  class="text-2xl font-bold md:text-3xl"
                >
                {{ formatTemperature(city.meteo.main.temp) }}
                </span>
              </div>
              <img
                v-if="city.imageSrc"
                :src="city.imageSrc"
                :alt="city.name"
                class="w-20 h-20 object-contain ml-4"
                style="min-width: 80px;"
              />
            </div>
          </SwiperSlide>
        </Swiper>
      </div>

      <main class="flex-1 overflow-y-auto md:p-6">
        <router-view class="h-full" />
      </main>
      <Footer class="pb-6 pt-4 mx-8" />
    </div>
  </div>
</template>

<style>
.swiper-pagination-bullet-active {
  background: black !important;
}
</style>