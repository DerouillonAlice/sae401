<template>
  <div
    :class="[
      'relative rounded-xl border shadow bg-white/60 backdrop-blur-md p-6 flex flex-col justify-between overflow-hidden',
      isConnected && hasLayout ? 'flex-1' : isConnected ? 'w-full m-2' : 'w-full lg:max-w-md'
    ]"
  >
    <img 
      :src="imageUrl" 
      alt="Météo"
      class="absolute -top-28 -right-60 max-h-[580px] max-w-[580px] object-contain pointer-events-none z-0 opacity-90" 
    />

    <div class="flex-1 flex flex-col justify-center items-center text-black text-center z-10 relative">
      <p class="text-5xl font-extrabold">
        {{ formatTemperature(currentDayData?.main?.temp) }}
      </p>
      <p class="text-xl font-bold mt-2">
        Ressenti : {{ formatTemperature(currentDayData?.main?.feels_like) }}
      </p>
      <p class="text-lg mt-2 capitalize">
        {{ currentDayData?.weather?.[0]?.description || '—' }}
      </p>
    </div>

    <div class="mt-4 text-sm font-semibold z-10 relative">
      {{ selectedDayIndex === 0 ? 'Dernière mise à jour' : 'Prévisions pour' }} : {{ getDateInfoText() }}
    </div>

    <div class="flex w-full mt-6 z-10 relative">
      <div 
        v-for="(entry, index) in dayForecastData" 
        :key="index"
        class="flex-1 bg-white/70 backdrop-blur-md border border-gray-200 first:rounded-l-xl last:rounded-r-xl text-center py-4 px-2"
      >
        <div class="text-sm font-bold">{{ entry.label }}</div>
        <img 
          :src="getForecastIcon(entry.icon)" 
          alt="Icon météo" 
          class="w-10 h-10 mx-auto object-contain" 
        />
        <div class="text-base font-semibold">{{ formatTemperature(entry.temp) }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { formatTemperature } from '@/services/unitUtils'
import iconLightRain from '@/assets/light-rain.png'
import iconRain from '@/assets/rain.png'
import iconSun from '@/assets/sun.png'

const props = defineProps({
  currentDayData: Object,
  dayForecastData: Array,
  selectedDayIndex: Number,
  imageUrl: String,
  isConnected: Boolean,
  hasLayout: Boolean,
  getDateInfo: Function
})

function getForecastIcon(code) {
  if (code?.includes('09')) return iconLightRain
  if (code?.includes('10')) return iconRain
  if (code?.includes('01')) return iconSun
  return iconSun
}

function getDateInfoText() {
  // Appeler directement la fonction passée en prop
  return props.getDateInfo ? props.getDateInfo() : '—'
}
</script>