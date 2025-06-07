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
                  {{ weatherData?.main?.temp ? Math.round(weatherData.main.temp) + '°C' : '—' }}
                </p>
                <p class="text-xl font-bold mt-2">
                  Ressenti : {{ weatherData?.main?.feels_like ? Math.round(weatherData.main.feels_like) + '°C' : '—' }}
                </p>
              </div>

              <div class="mt-4 text-sm font-semibold z-10 relative">
                Dernière mise à jour : {{ getLastUpdatedHour() }}
              </div>

              <div class="flex w-full mt-6 z-10 relative">
                <div
                  v-for="(entry, index) in forecastData"
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
import { ref, watch, onMounted, onUnmounted } from 'vue'
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

const route = useRoute()
const weatherData = ref(null)
const forecastData = ref([])
const { imageUrl } = useWeatherImage(weatherData)

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
        // Cherche les entrées pour 10h, 14h et 21h
        const getHourEntry = (hour) =>
          data.list.find(item => item.dt_txt && item.dt_txt.includes(` ${hour}:00:00`))

        const matin = getHourEntry('10')
        const apresmidi = getHourEntry('14')
        const soir = getHourEntry('21')

        forecastData.value = [
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
      } else if (Array.isArray(data)) {
        forecastData.value = data
      } else {
        forecastData.value = []
      }
    })
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


const allBlocks = ref([
  { i: '1', name: 'Vent', active: true },
  { i: '2', name: 'Pression', active: true },
  { i: '3', name: 'Humidité', active: true },
  { i: '4', name: 'Visibilité', active: true },
  { i: '5', name: 'Nuages', active: true },
  { i: '6', name: 'Cycle Soleil', active: true }
]);


function getBlockContent(name) {
  if (!weatherData.value) return name
  switch (name) {
    case 'Vent': return `${weatherData.value.wind.speed} m/s`;
    case 'Pression': return `${weatherData.value.main.pressure} hPa`;
    case 'Humidité': return `${weatherData.value.main.humidity} %`;
    case 'Visibilité': return `${weatherData.value.visibility / 1000} km`;
    case 'Nuages': return `${weatherData.value.clouds.all} %`;
    case 'Cycle Soleil': {
      const sunrise = new Date(weatherData.value.sys.sunrise * 1000).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
      const sunset = new Date(weatherData.value.sys.sunset * 1000).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
      return `${sunrise} / ${sunset}`;
    }
    default: return name;
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

function getLastUpdatedHour() {
  if (!weatherData.value?.dt) return '—'
  const date = new Date(weatherData.value.dt * 1000)
  return `${date.getHours()}h`
}

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
  fetchWeather();
  fetchForecast();
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
