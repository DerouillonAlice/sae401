<template>
  <div class="flex flex-col ">
    <div class="flex flex-1 overflow-hidden">
      <main class="flex-1 overflow-auto sm:overflow-clip">
        <div class="w-full flex flex-col gap-4 mx-auto">
          <!-- Filtres -->
          <div class="w-full flex flex-wrap justify-evenly gap-y-4 py-4 px-2 rounded-xl">
            <label
              v-for="block in allBlocks"
              :key="block.i"
              class="flex items-center gap-2 text-sm font-medium cursor-pointer text-black"
            >
              <input
                type="checkbox"
                v-model="block.active"
                class="peer hidden"
              />
              <span
                class="w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:bg-black peer-checked:border-white transition-all duration-200 shadow-sm"
              ></span>
              <span class="whitespace-nowrap">{{ block.name }}</span>
            </label>
          </div>

          <!-- Température + Grid -->
          <div class="flex flex-col lg:flex-row gap-4 items-stretch">
            <!-- Bloc Température -->
            <div
              class="relative flex-1 rounded-2xl border shadow bg-white/60 backdrop-blur-md p-6 flex flex-col justify-between overflow-hidden"
            >
              <img
                :src="imageUrl"
                alt="Météo"
                class="absolute -top-28 -right-60 max-h-[580px] max-w-[580px] object-contain pointer-events-none z-0 opacity-90"
              />

              <div class="flex-1 flex flex-col justify-center items-center text-black text-center z-10 relative">
                <p class="text-5xl font-extrabold">
                  {{ currentDayData?.main?.temp ? Math.round(currentDayData.main.temp) + '°C' : '—' }}
                </p>
                <p class="text-xl font-bold mt-2">
                  Ressenti : {{ currentDayData?.main?.feels_like ? Math.round(currentDayData.main.feels_like) + '°C' : '—' }}
                </p>
                <p class="text-lg mt-2 capitalize">
                  {{ currentDayData?.weather?.[0]?.description || '—' }}
                </p>
              </div>

              <div class="mt-4 text-sm font-semibold z-10 relative">
                {{ selectedDayIndex === 0 ? 'Dernière mise à jour' : 'Prévisions pour' }} : {{ getDateInfo() }}
              </div>

              <div class="flex w-full mt-6 z-10 relative">
                <div
                  v-for="(entry, index) in dayForecastData"
                  :key="index"
                  class="flex-1 bg-white/70 backdrop-blur-md border border-gray-200 first:rounded-l-2xl last:rounded-r-2xl text-center py-4 px-2"
                >
                  <div class="text-sm font-bold">{{ entry.label }}</div>
                  <img
                    :src="getForecastIcon(entry.icon)"
                    alt="Icon météo"
                    class="w-10 h-10 mx-auto object-contain"
                  />
                  <div class="text-base font-semibold">{{ Math.round(entry.temp) }}°C</div>
                </div>
              </div>
            </div>

            <!-- Grid météo -->
            <div class="flex-1">
              <GridLayout
                ref="gridLayoutRef"
                :layout="layout"
                :col-num="colNum"
                :row-height="auto"
                :is-draggable="true"
                :is-resizable="false"
                :auto-size="true"
                :use-css-transforms="false"
                :vertical-compact="false"
                :width="containerWidth"
                class="h-full gap-4 mt-0"
                @layout-updated="updateLayout"
              >
                <GridItem
                  v-for="item in layout"
                  :key="item.i"
                  :i="item.i"
                  :x="item.x"
                  :y="item.y"
                  :w="item.w"
                  :h="item.h"
                >
                  <div
                    class="h-full w-full flex flex-col items-center justify-center rounded-lg shadow text-lg font-semibold text-center p-4 border backdrop-blur-sm bg-white/60 space-y-2"
                  >
                    <component :is="getIconComponent(item.name)" class="w-10 h-10 text-black" />
                    <div>
                      {{ getBlockContent(item.name) }}
                    </div>
                  </div>
                </GridItem>
              </GridLayout>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { GridLayout, GridItem } from 'vue3-grid-layout'
import {
  CloudIcon,
  DropletIcon,
  EyeIcon,
  WindIcon,
  SunIcon,
  BarChart3Icon
} from 'lucide-vue-next'

import iconLightRain from '@/assets/light-rain.png';
import iconRain from '@/assets/rain.png';
import iconSun from '@/assets/sun.png';
import { useWeatherImage } from '@/composables/useWeatherImage'

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

function fetchWeather() {
  const ville = route.query.ville || 'Paris'
  fetch(`/api/weather/${encodeURIComponent(ville)}`)
    .then(res => res.json())
    .then(data => { weatherData.value = data })
}

function fetchForecast() {
  const ville = route.query.ville || 'Paris'
  fetch(`/api/forecast/${encodeURIComponent(ville)}`)
    .then(res => res.json())
    .then(data => {
      if (Array.isArray(data.list)) {
        cityData.value = data.city
        
        fetchTodayForecast(data.list)
        
        const dailyForecasts = []
        const processedDates = new Set()
        
        data.list.forEach(item => {
          const date = new Date(item.dt * 1000)
          const dateKey = date.toDateString()
          
          if (!processedDates.has(dateKey) && 
              (item.dt_txt.includes(' 12:00:00') || !processedDates.has(dateKey))) {
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
  const getHourEntry = (hour) =>
    forecastList.find(item => item.dt_txt && item.dt_txt.includes(` ${hour}:00:00`))

  const matin = getHourEntry('10')
  const apresmidi = getHourEntry('14')
  const soir = getHourEntry('21')

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

function getBlockContent(name) {
  const data = currentDayData.value
  if (!data) return name
  
  switch (name) {
    case 'Vent': return `${data.wind?.speed || 0} m/s`;
    case 'Pression': return `${data.main?.pressure || 0} hPa`;
    case 'Humidité': return `${data.main?.humidity || 0} %`;
    case 'Visibilité': return `${Math.round((data.visibility || 0) / 1000)} km`;
    case 'Nuages': return `${data.clouds?.all || 0} %`;
    case 'Cycle Soleil': {
      if (selectedDayIndex.value === 0 && data.sys?.sunrise && data.sys?.sunset) {
        const sunrise = new Date(data.sys.sunrise * 1000).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        const sunset = new Date(data.sys.sunset * 1000).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        return `${sunrise} / ${sunset}`;
      } else if (cityData.value?.sunrise && cityData.value?.sunset) {
        const baseSunrise = cityData.value.sunrise
        const baseSunset = cityData.value.sunset
        
        const dayOffset = selectedDayIndex.value
        const minutesPerDay = 1.5 
        
        const adjustedSunrise = baseSunrise + (dayOffset * minutesPerDay * 60)
        const adjustedSunset = baseSunset + (dayOffset * minutesPerDay * 60)
        
        const sunrise = new Date(adjustedSunrise * 1000).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        const sunset = new Date(adjustedSunset * 1000).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        
        return `${sunrise} / ${sunset}`;
      }
      
      const today = new Date()
      const targetDate = new Date()
      targetDate.setDate(today.getDate() + selectedDayIndex.value)
      
      const dayOfYear = Math.floor((targetDate - new Date(targetDate.getFullYear(), 0, 0)) / 1000 / 60 / 60 / 24)
      
      const sunriseMin = 5.75
      const sunriseMax = 8.5  
      const sunsetMin = 16.75  
      const sunsetMax = 21.5 
      
      const seasonFactor = Math.cos(2 * Math.PI * (dayOfYear - 172) / 365)
      
      const sunriseDecimal = sunriseMin + (sunriseMax - sunriseMin) * (1 - seasonFactor) / 2
      const sunsetDecimal = sunsetMax + (sunsetMin - sunsetMax) * (1 - seasonFactor) / 2
      
      const formatTime = (decimal) => {
        const hours = Math.floor(decimal)
        const minutes = Math.round((decimal - hours) * 60)
        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`
      }
      
      return `${formatTime(sunriseDecimal)} / ${formatTime(sunsetDecimal)}`;
    }
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
    case 'Pression': return BarChart3Icon;
    case 'Humidité': return DropletIcon;
    case 'Visibilité': return EyeIcon;
    case 'Nuages': return CloudIcon;
    case 'Cycle Soleil': return SunIcon;
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
  { i: '2', name: 'Pression', active: true },
  { i: '3', name: 'Humidité', active: true },
  { i: '4', name: 'Visibilité', active: true },
  { i: '5', name: 'Nuages', active: true },
  { i: '6', name: 'Cycle Soleil', active: true }
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
