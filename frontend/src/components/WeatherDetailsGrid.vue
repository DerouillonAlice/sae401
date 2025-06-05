<template>
  <div class="flex flex-col h-screen">
    <div class="flex flex-1 overflow-hidden">
      <main class="flex-1 overflow-auto sm:overflow-clip bg-gray-50 p-5">
        <div class="w-full flex flex-col gap-4 max-w-screen-xl mx-auto">
          <!-- Filtres -->
          <div class="mb-4 flex flex-wrap items-center gap-4">
            <label
              v-for="block in allBlocks"
              :key="block.i"
              class="flex items-center gap-2 text-sm bg-white px-3 py-2 rounded shadow border"
            >
              <input type="checkbox" v-model="block.active" class="accent-blue-500" />
              {{ block.name }}
            </label>
          </div>

          <!-- Température + Grid côte à côte -->
          <div class="flex flex-col lg:flex-row gap-4 items-start">
            <!-- Bloc Température -->
            <div
              class="relative flex-1 rounded-2xl border shadow bg-white p-6 flex flex-col justify-between overflow-hidden"
              :style="{ height: gridHeight ? `${gridHeight}px` : '520px', transition: 'height 0.3s ease' }"
              >
              <img
                src="@/assets/sun.png"
                alt="Soleil"
                class="absolute -top-28 -right-60 h-[580px] w-[580px] object-contain pointer-events-none z-0 opacity-90"
              />

              <!-- Température principale -->
              <div class="flex-1 flex flex-col justify-center items-center text-black text-center z-10 relative">
                <p class="text-5xl font-extrabold">
                  {{ weatherData?.main?.temp ? Math.round(weatherData.main.temp) + '°C' : '—' }}
                </p>
                <p class="text-xl font-bold mt-2">
                  Ressenti : {{ weatherData?.main?.feels_like ? Math.round(weatherData.main.feels_like) + '°C' : '—' }}
                </p>
              </div>

              <!-- Heure de mise à jour -->
              <div class="mt-4 text-sm font-semibold z-10 relative">
                Dernière mise à jour : {{ getLastUpdatedHour() }}
              </div>

              <!-- Prévisions intégrées -->
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
            <div ref="gridContainerRef" class="flex-1">
              <GridLayout
                ref="gridLayoutRef"
                :layout="layout"
                :col-num="colNum"
                :row-height="260"
                :is-draggable="true"
                :is-resizable="false"
                :auto-size="false"
                :use-css-transforms="false"
                :vertical-compact="false"
                :margin="[10, 10]"
                :width="containerWidth"
                class="h-auto mt-0"
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
                    class="h-full w-full flex flex-col items-center justify-center rounded-lg shadow text-lg font-semibold text-center p-4 border shadow backdrop-blur-sm bg-white/60 space-y-2"
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
import { ref, watch, onMounted, nextTick } from 'vue';
import { GridLayout, GridItem } from 'vue3-grid-layout';
import {
  CloudRainIcon,
  DropletIcon,
  EyeIcon,
  WindIcon,
  SunIcon,
  BarChart3Icon
} from 'lucide-vue-next';

import iconLightRain from '@/assets/light-rain.png';
import iconRain from '@/assets/rain.png';
import iconSun from '@/assets/sun.png';

const allBlocks = ref([
  { i: '1', name: 'Vent', active: true },
  { i: '2', name: 'Pression', active: true },
  { i: '3', name: 'Humidité', active: true },
  { i: '4', name: 'Visibilité', active: true },
  { i: '5', name: 'Nuages', active: true },
  { i: '6', name: 'Rosée', active: true }
]);

const weatherData = ref(null);
const forecastData = ref([]);

function getBlockContent(name) {
  if (!weatherData.value) return name;
  switch (name) {
    case 'Vent': return `${weatherData.value.wind.speed} m/s`;
    case 'Pression': return `${weatherData.value.main.pressure} hPa`;
    case 'Humidité': return `${weatherData.value.main.humidity} %`;
    case 'Visibilité': return `${weatherData.value.visibility / 1000} km`;
    case 'Nuages': return `${weatherData.value.clouds.all} %`;
    case 'Rosée': return `${weatherData.value.main.temp} °C`;
    default: return name;
  }
}

function getIconComponent(name) {
  switch (name) {
    case 'Vent': return WindIcon;
    case 'Pression': return BarChart3Icon;
    case 'Humidité': return DropletIcon;
    case 'Visibilité': return EyeIcon;
    case 'Nuages': return CloudRainIcon;
    case 'Rosée': return SunIcon;
    default: return SunIcon;
  }
}

function getForecastIcon(code) {
  if (code?.includes('09')) return iconLightRain;
  if (code?.includes('10')) return iconRain;
  if (code?.includes('01')) return iconSun;
  return iconSun;
}

function getLastUpdatedHour() {
  if (!weatherData.value?.dt) return '—';
  const date = new Date(weatherData.value.dt * 1000);
  return `${date.getHours()}h`;
}

function fetchWeather() {
  fetch('/api/weather/Paris')
    .then(res => res.json())
    .then(data => {
      weatherData.value = data;
    })
    .catch(err => console.error("Erreur API weather:", err));
}

function fetchForecast() {
  fetch('/api/forecast/paris')
    .then(res => res.json())
    .then(data => {
      const targetHours = {
        Matin: '09:00:00',
        Midi: '12:00:00',
        Soir: '18:00:00',
      };
      const today = new Date().toISOString().split('T')[0];

      forecastData.value = Object.entries(targetHours).map(([label, hour]) => {
        const entry = data.list.find(e => e.dt_txt.includes(`${today} ${hour}`));
        return {
          label,
          temp: entry?.main?.temp ?? null,
          icon: entry?.weather?.[0]?.icon ?? null,
        };
      }).filter(e => e.temp !== null);
    })
    .catch(err => console.error("Erreur API forecast:", err));
}

const predefinedLayouts = {
  1: [{ i: '1', x: 0, y: 0, w: 1, h: 1 }],
  2: [{ i: '1', x: 0, y: 0, w: 1, h: 1 }, { i: '2', x: 1, y: 0, w: 1, h: 1 }],
  3: [{ i: '1', x: 0, y: 0, w: 1, h: 1 }, { i: '2', x: 1, y: 0, w: 1, h: 1 }, { i: '3', x: 0, y: 1, w: 1, h: 1 }],
  4: [{ i: '1', x: 0, y: 0, w: 1, h: 1 }, { i: '2', x: 1, y: 0, w: 1, h: 1 }, { i: '3', x: 0, y: 1, w: 1, h: 1 }, { i: '4', x: 1, y: 1, w: 1, h: 1 }],
  5: [{ i: '1', x: 0, y: 0, w: 1, h: 1 }, { i: '2', x: 1, y: 0, w: 1, h: 1 }, { i: '3', x: 2, y: 0, w: 1, h: 1 }, { i: '4', x: 0, y: 1, w: 1, h: 1 }, { i: '5', x: 1, y: 1, w: 1, h: 1 }],
  6: [{ i: '1', x: 0, y: 0, w: 1, h: 1 }, { i: '2', x: 1, y: 0, w: 1, h: 1 }, { i: '3', x: 2, y: 0, w: 1, h: 1 }, { i: '4', x: 0, y: 1, w: 1, h: 1 }, { i: '5', x: 1, y: 1, w: 1, h: 1 }, { i: '6', x: 2, y: 1, w: 1, h: 1 }]
};

const layout = ref([]);
const colNum = ref(3);
const containerWidth = ref(600);
const gridHeight = ref(0);
const gridContainerRef = ref(null);

function updateLayoutDimensions() {
  if (gridContainerRef.value) {
    containerWidth.value = gridContainerRef.value.offsetWidth;
    colNum.value = Math.floor(containerWidth.value / 180) || 1;
    nextTick(() => {
      const grid = gridContainerRef.value.querySelector('.vue-grid-layout');
      if (grid) gridHeight.value = grid.offsetHeight;
    });
  }
}

onMounted(() => {
  updateLayoutDimensions();
  window.addEventListener('resize', updateLayoutDimensions);
  nextTick(updateLayoutDimensions);

  fetchWeather();
  fetchForecast();
});

watch(
  () => allBlocks.value.map((b) => b.active),
  () => {
    const activeBlocks = allBlocks.value.filter((b) => b.active);
    const config = predefinedLayouts[activeBlocks.length] || [];
    layout.value = config.map((l, index) => ({
      ...l,
      name: activeBlocks[index]?.name || 'Bloc',
    }));
    nextTick(updateLayoutDimensions);
  },
  { immediate: true }
);

let isUpdating = false;
let updateTimeout;

function updateLayout(newLayout) {
  if (isUpdating) return;
  isUpdating = true;
  clearTimeout(updateTimeout);

  updateTimeout = setTimeout(() => {
    const activeBlocks = allBlocks.value.filter((b) => b.active);
    const layoutDef = predefinedLayouts[activeBlocks.length] || [];
    const sortedItems = newLayout.slice().sort((a, b) => a.y - b.y || a.x - b.x);
    const correctedLayout = layoutDef.map((slot, index) => {
      const movedBlock = sortedItems[index];
      const original = layout.value.find((l) => l.i === movedBlock.i);
      return { ...slot, i: movedBlock.i, name: original?.name || 'Bloc' };
    });

    if (JSON.stringify(layout.value) !== JSON.stringify(correctedLayout)) {
      layout.value = correctedLayout;
    }

    isUpdating = false;
    nextTick(updateLayoutDimensions);
  }, 50);
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
