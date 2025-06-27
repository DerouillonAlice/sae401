<template>
  <div class="flex-1 lg:flex-[2] flex flex-col gap-4">
    <div class="flex flex-col lg:flex-row gap-4 items-stretch h-full min-h-[220px]">
      <div
        v-for="block in allBlocks.slice(0, 3)"
        :key="block.i"
        class="flex-1 flex flex-col items-center justify-center text-lg font-semibold space-y-2 text-black text-center h-full"
      >
        <WeatherBlock
          :name="block.name"
          :current-day-data="currentDayData"
          :city-data="cityData"
          :forecast-data="forecastData"
          :selected-day-index="selectedDayIndex"
          class="h-full w-full"
        />
      </div>
    </div>
    
    <div class="flex flex-col lg:flex-row gap-4 items-stretch">
      <div class="flex-1 flex flex-col items-center justify-center rounded-2xl border shadow bg-white/60 backdrop-blur-md p-6 text-black text-center h-full min-h-[180px]">
        <div class="text-xl font-bold mb-2">Connectez-vous pour accéder à toutes les fonctionnalités</div>
        <div class="text-sm text-gray-600 mb-4">
          Personnalisez votre dashboard, sauvegardez vos villes favorites et configurez vos préférences météo
        </div>
        <div class="flex gap-4 mt-4">
          <button 
            @click="$emit('go-to-login')"
            class="px-6 py-2 rounded-full bg-white/30 backdrop-blur-md border-white/70 hover:bg-white/40 transition-all duration-300 font-semibold"
          >
            Connexion
          </button>
          <button 
            @click="$emit('go-to-register')"
            class="px-6 py-2 rounded-full bg-black text-white shadow hover:bg-black/90 transition-all duration-300 font-semibold"
          >
            Inscription
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import WeatherBlock from './WeatherBlock.vue'

const props = defineProps({
  allBlocks: {
    type: Array,
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

const emit = defineEmits(['go-to-login', 'go-to-register'])
</script>