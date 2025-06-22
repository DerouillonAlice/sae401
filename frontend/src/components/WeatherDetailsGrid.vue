<template>
  <div class="flex flex-col ">
    <div class="flex flex-1 overflow-hidden">
      <main class="flex-1 overflow-auto sm:overflow-clip">
        <div class="w-full flex flex-col gap-4 mx-auto">
          <div v-if="auth.isConnected" class="w-full flex flex-wrap justify-evenly gap-y-4 py-4 px-2 rounded-xl">
            <label v-for="block in allBlocks" :key="block.i"
              class="flex items-center gap-2 text-sm font-medium cursor-pointer text-black">
              <input type="checkbox" v-model="block.active" class="peer hidden" />
              <span
                class="w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:bg-black peer-checked:border-white transition-all duration-200 shadow-sm"></span>
              <span class="whitespace-nowrap">{{ block.name }}</span>
            </label>
          </div>

          <div class="flex flex-col lg:flex-row gap-4 items-stretch">
            <div
              class="relative flex-1 rounded-2xl border shadow bg-white/60 backdrop-blur-md p-6 flex flex-col justify-between overflow-hidden">
              <img :src="imageUrl" alt="Météo"
                class="absolute -top-28 -right-60 max-h-[580px] max-w-[580px] object-contain pointer-events-none z-0 opacity-90" />

              <div class="flex-1 flex flex-col justify-center items-center text-black text-center z-10 relative">
                <p class="text-5xl font-extrabold">
                  {{ currentDayData?.main?.temp ? Math.round(currentDayData.main.temp) + '°C' : '—' }}
                </p>
                <p class="text-xl font-bold mt-2">
                  Ressenti : {{ currentDayData?.main?.feels_like ? Math.round(currentDayData.main.feels_like) + '°C' :
                    '—' }}
                </p>
                <p class="text-lg mt-2 capitalize">
                  {{ currentDayData?.weather?.[0]?.description || '—' }}
                </p>
              </div>

              <div class="mt-4 text-sm font-semibold z-10 relative">
                {{ selectedDayIndex === 0 ? 'Dernière mise à jour' : 'Prévisions pour' }} : {{ getDateInfo() }}
              </div>

              <div class="flex w-full mt-6 z-10 relative">
                <div v-for="(entry, index) in dayForecastData" :key="index"
                  class="flex-1 bg-white/70 backdrop-blur-md border border-gray-200 first:rounded-l-2xl last:rounded-r-2xl text-center py-4 px-2">
                  <div class="text-sm font-bold">{{ entry.label }}</div>
                  <img :src="getForecastIcon(entry.icon)" alt="Icon météo" class="w-10 h-10 mx-auto object-contain" />
                  <div class="text-base font-semibold">{{ Math.round(entry.temp) }}°C</div>
                </div>
              </div>
            </div>

            <div v-if="auth.isConnected" class="flex-1">
              <GridLayout ref="gridLayoutRef" :layout="layout" :col-num="colNum" :row-height="auto" :is-draggable="true"
                :is-resizable="false" :auto-size="true" :use-css-transforms="false" :vertical-compact="false"
                :width="containerWidth" class="h-full gap-4 mt-0" @layout-updated="updateLayout">
                <GridItem v-for="item in layout" :key="item.i" :i="item.i" :x="item.x" :y="item.y" :w="item.w"
                  :h="item.h">
                  <div
                    class="h-full w-full flex flex-col items-center justify-center rounded-lg shadow text-lg font-semibold text-center p-4 border backdrop-blur-sm bg-white/60 space-y-2">
                    <component :is="getIconComponent(item.name)" class="w-10 h-10 text-black" />
                    <div>
                      {{ getBlockContent(item.name) }}
                    </div>
                  </div>
                </GridItem>
              </GridLayout>
            </div>

            <div v-else class="flex-1 flex flex-col gap-4">
              <div class="flex flex-col lg:flex-row gap-4 items-stretch h-full min-h-[220px]">
                <div
                  v-for="block in allBlocks.slice(0, 3)"
                  :key="block.i"
                  class="flex-1 flex flex-col items-center justify-center rounded-2xl border shadow text-lg font-semibold space-y-2 bg-white/60 backdrop-blur-md p-6 text-black text-center h-full"
                >
                  <component :is="getIconComponent(block.name)" class="w-10 h-10 text-black mb-2" />
                  <div >{{ getBlockContent(block.name) }}</div>
                </div>
              </div>
              <div class="flex flex-col lg:flex-row gap-4 items-stretch">
                <div
                  class="flex-1 flex flex-col items-center justify-center rounded-2xl border shadow bg-white/60 backdrop-blur-md p-6 text-black text-center h-full"
                >
                  <div class="text-xl font-bold mb-2">Connectez-vous pour accéder à toutes les fonctionnalités</div>
                  <div class="flex gap-4 mt-4">
                    <button @click="goToLogin"
                      class="px-6 py-2 rounded-full bg-white/30 backdrop-blur-md border-white/70 hover:bg-white/40 transition-all duration-300 font-semibold">
                      Connexion
                    </button>
                    <button @click="goToRegister"
                      class="px-6 py-2 rounded-full bg-black text-white shadow hover:bg-black/90 transition-all duration-300 font-semibold">
                      Inscription
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useRoute } from 'vue-router'
import { GridLayout, GridItem } from 'vue3-grid-layout'

import SunCalc from 'suncalc'

import iconLightRain from '@/assets/light-rain.png';
import iconRain from '@/assets/rain.png';
import iconSun from '@/assets/sun.png';
import { useWeatherImage } from '@/composables/useWeatherImage'
import { getWeatherByCity, getForecastByCity } from '../services/services'
import {
  WindIcon,
  BarChart3Icon,
  DropletIcon,
  EyeIcon,
  CloudIcon,
  SunIcon
} from 'lucide-vue-next'
import { useAuthStore } from '@/stores/auth'

const props = defineProps({
  selectedDayIndex: {
    type: Number,
    default: 0
  }
})

const route = useRoute()
const weatherData = ref(null)
const forecastData = ref([])
const todayHourlyData = ref([])
const cityData = ref(null)
const selectedDayIndex = ref(props.selectedDayIndex)
const router = useRouter()

const auth = useAuthStore()

const goToRegister = () => {
  router.push({ path: '/inscription' })
}

const goToLogin = () => {
  router.push({ path: '/connexion' })
}


onMounted(() => {
  if (localStorage.getItem('token')) {
    auth.checkAuth()
  }
})

watch(() => props.selectedDayIndex, (newIndex) => {
  selectedDayIndex.value = newIndex
})

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

function getDefaultVille() {
  if (auth.isConnected && auth.favorites.length > 0) {
    return auth.favorites[0].city
  }
  return 'Paris'
}

function fetchWeather() {
  const ville = route.query.ville || getDefaultVille()
  getWeatherByCity(ville)
    .then(res => { weatherData.value = res.data })
}

function fetchForecast() {
  const ville = route.query.ville || getDefaultVille()
  getForecastByCity(ville)
    .then(res => {
      const data = res.data
      if (Array.isArray(data.list)) {
        cityData.value = data.city

        const today = new Date()
        const todayString = today.toDateString()

        const todayData = []
        const otherDaysData = []

        data.list.forEach(item => {
          const itemDate = new Date(item.dt * 1000)
          if (itemDate.toDateString() === todayString) {
            todayData.push(item)
          } else {
            otherDaysData.push(item)
          }
        })

        fetchTodayForecast(todayData)

        const dailyForecasts = []
        const processedDates = new Set()

        otherDaysData.forEach(item => {
          const date = new Date(item.dt * 1000)
          const dateKey = date.toDateString()

          if (!processedDates.has(dateKey)) {
            processedDates.add(dateKey)
            dailyForecasts.push({
              ...item,
              date: date,
              dayName: date.toLocaleDateString('fr-FR', { weekday: 'long' })
            })
          }
        })

        forecastData.value = dailyForecasts.slice(0, 7)
      }
    })
}

function fetchTodayForecast(forecastList) {
  const findClosest = (targetHour, startHour, endHour) => {
    const candidates = forecastList.filter(item => {
      const hour = new Date(item.dt * 1000).getHours()
      return hour >= startHour && hour < endHour
    })
    if (!candidates.length) return null
    return candidates.reduce((prev, curr) => {
      const prevHour = new Date(prev.dt * 1000).getHours()
      const currHour = new Date(curr.dt * 1000).getHours()
      return Math.abs(currHour - targetHour) < Math.abs(prevHour - targetHour) ? curr : prev
    })
  }

  const matin = findClosest(9, 6, 12)      // 6h-12h, cible 9h
  const apresmidi = findClosest(15, 12, 18) // 12h-18h, cible 15h
  const soir = findClosest(20, 18, 23)     // 18h-23h, cible 20h

  todayHourlyData.value = [
    matin && {
      label: 'Matin',
      temp: matin.main?.temp,
      icon: matin.weather?.[0]?.icon
    },
    apresmidi && {
      label: 'Après-midi',
      temp: apresmidi.main?.temp,
      icon: apresmidi.weather?.[0]?.icon
    },
    soir && {
      label: 'Soir',
      temp: soir.main?.temp,
      icon: soir.weather?.[0]?.icon
    }
  ].filter(Boolean)
}

watch(
  () => route.query.ville,
  () => {
    fetchWeather()
    fetchForecast()
  },
  { immediate: true }
)

onMounted(() => {
  fetchWeather()
  fetchForecast()
})

function isFrenchCity() {
  return cityData.value?.country === 'FR'
}

function formatSunTime(date, timezoneOffsetSeconds) {
  if (isFrenchCity()) {
    return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
  }
  const local = new Date(date.getTime() + timezoneOffsetSeconds * 1000)
  return local.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
}

function getBlockContent(name) {
  const data = currentDayData.value
  if (!data) return name

  switch (name) {
    case 'Vent': return `${data.wind?.speed || 0} m/s`;
    case 'Cycle Soleil': {
      const tz = cityData.value?.timezone ?? 0
      const lat = cityData.value?.coord?.lat
      const lon = cityData.value?.coord?.lon
      let date
      if (selectedDayIndex.value === 0) {
        date = new Date()
      } else {
        const dayData = forecastData.value[selectedDayIndex.value - 1]
        date = dayData?.date || new Date()
      }

      if (lat && lon && date) {
        const times = SunCalc.getTimes(date, lat, lon)
        const sunrise = formatSunTime(times.sunrise, tz)
        const sunset = formatSunTime(times.sunset, tz)
        return `${sunrise} / ${sunset}`
      }
      return '—'
    }
    case 'Pression': return `${data.main?.pressure || 0} hPa`;
    case 'Humidité': return `${data.main?.humidity || 0} %`;
    case 'Visibilité': return `${Math.round((data.visibility || 0) / 1000)} km`;
    case 'Nuages': return `${data.clouds?.all || 0} %`;

    default: return name;
  }
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

function getIconComponent(name) {
  switch (name) {
    case 'Vent': return WindIcon;
    case 'Cycle Soleil': return SunIcon;
    case 'Pression': return BarChart3Icon;
    case 'Humidité': return DropletIcon;
    case 'Visibilité': return EyeIcon;
    case 'Nuages': return CloudIcon;
    default: return SunIcon;
  }
}

function getForecastIcon(code) {
  if (code?.includes('09')) return iconLightRain
  if (code?.includes('10')) return iconRain
  if (code?.includes('01')) return iconSun
  return iconSun
}

const allBlocks = ref([
  { i: '1', name: 'Vent', active: true },
  { i: '2', name: 'Cycle Soleil', active: true },
  { i: '3', name: 'Humidité', active: true },
  { i: '4', name: 'Visibilité', active: true },
  { i: '5', name: 'Nuages', active: true },
  { i: '6', name: 'Pression', active: true }
]);

const predefinedLayouts = {
  1: [{ i: '1', x: 0, y: 0, w: 3, h: 2 }],
  2: [{ i: '1', x: 0, y: 0, w: 3, h: 1 }, { i: '2', x: 0, y: 1, w: 3, h: 1 }],
  3: [{ i: '1', x: 0, y: 0, w: 1.5, h: 1 }, { i: '2', x: 1.5, y: 0, w: 1.5, h: 1 }, { i: '3', x: 0, y: 1, w: 3, h: 1 }],
  4: [{ i: '1', x: 0, y: 0, w: 1.5, h: 1 }, { i: '2', x: 1.5, y: 0, w: 1.5, h: 1 }, { i: '3', x: 0, y: 1, w: 1.5, h: 1 }, { i: '4', x: 1.5, y: 1, w: 1.5, h: 1 }],
  5: [{ i: '1', x: 0, y: 0, w: 1, h: 1 }, { i: '2', x: 1, y: 0, w: 1, h: 1 }, { i: '3', x: 2, y: 0, w: 1, h: 1 }, { i: '4', x: 0, y: 1, w: 2, h: 1 }, { i: '5', x: 2, y: 1, w: 1, h: 1 }],
  6: [{ i: '1', x: 0, y: 0, w: 1, h: 1 }, { i: '2', x: 1, y: 0, w: 1, h: 1 }, { i: '3', x: 2, y: 0, w: 1, h: 1 }, { i: '4', x: 0, y: 1, w: 1, h: 1 }, { i: '5', x: 1, y: 1, w: 1, h: 1 }, { i: '6', x: 2, y: 1, w: 1, h: 1 }]
}

const layout = ref([]);
const colNum = ref(3);
const containerWidth = ref(window.innerWidth || 1200);

function updateColNum() {
  const width = window.innerWidth;
  colNum.value = width < 640 ? 1 : 3;
}

function updateContainerWidth() {
  containerWidth.value = window.innerWidth;
}

onMounted(() => {
  updateColNum();
  updateContainerWidth();
  window.addEventListener('resize', updateColNum);
  window.addEventListener('resize', updateContainerWidth);
});

onUnmounted(() => {
  window.removeEventListener('resize', updateColNum);
  window.removeEventListener('resize', updateContainerWidth);
});

watch(() => allBlocks.value.map((b) => b.active), () => {
  const activeBlocks = allBlocks.value.filter((b) => b.active);
  const isMobile = window.innerWidth < 640;

  if (isMobile) {
    layout.value = activeBlocks.map((block, index) => ({
      i: block.i,
      x: 0,
      y: index,
      w: 1,
      h: 1,
      name: block.name,
    }));
  } else {
    const config = predefinedLayouts[activeBlocks.length] || [];
    layout.value = config.map((l, index) => ({
      ...l,
      name: activeBlocks[index]?.name || 'Bloc',
    }));
  }
}, { immediate: true });

let isUpdating = false
let updateTimeout

function updateLayout(newLayout) {
  if (isUpdating) return;
  isUpdating = true;
  clearTimeout(updateTimeout);

  const isMobile = window.innerWidth < 640;

  if (isMobile) {
    layout.value = newLayout.map((item) => ({ ...item, x: 0, w: 1, h: 1 }));
    return;
  }

  updateTimeout = setTimeout(() => {
    const activeBlocks = allBlocks.value.filter((b) => b.active)
    const layoutDef = predefinedLayouts[activeBlocks.length] || []
    const sortedItems = newLayout.slice().sort((a, b) => a.y - b.y || a.x - b.x)
    const correctedLayout = layoutDef.map((slot, index) => {
      const movedBlock = sortedItems[index]
      const original = layout.value.find((l) => l.i === movedBlock.i)
      return { ...slot, i: movedBlock.i, name: original?.name || 'Bloc' }
    })

    if (JSON.stringify(layout.value) !== JSON.stringify(correctedLayout)) {
      layout.value = correctedLayout
    }

    isUpdating = false
  }, 50)
}
</script>

<style scoped>
.vue-grid-item.vue-grid-placeholder {
  opacity: 0 !important;
}

.vue-grid-item {
  transition: all 0.3s ease;
}

.vue-grid-layout {
  padding-top: 0 !important;
  margin-top: 0 !important;
}
</style>
