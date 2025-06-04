<script setup>
import CityCard from './CityCard.vue'
import { computed } from 'vue'
import { useSidebar } from '@/composables/useSidebar'
import { useAuthStore } from '@/stores/auth'
import { ChevronLeftIcon } from '@heroicons/vue/24/outline'
import sunImg from '../assets/sun.png'
import rainImg from '../assets/rain.png'
import lightRainImg from '../assets/light-rain.png'

const { isSidebarOpen } = useSidebar()
const auth = useAuthStore()

const cities = computed(() =>
  auth.isConnected
    ? [
        { name: 'Paris', imageSrc: sunImg },
        { name: 'Londres', imageSrc: rainImg },
        { name: 'New York', imageSrc: sunImg },
        { name: 'Montréal', imageSrc: lightRainImg }
      ]
    : [
        { name: 'Paris', imageSrc: sunImg },
        { name: 'Londres', imageSrc: rainImg },
        { name: 'Paris', imageSrc: sunImg },
        { name: 'Reims', imageSrc: lightRainImg }
      ]
)

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}
</script>

<template>
    <main class="flex flex-col justify-center py-4  h-full relative">
        <section
  class="absolute  left-0 grid grid-cols-[1fr_auto] mx-auto gap-2 h-full w-full p-4 rounded-r-xl bg-white/40 backdrop-blur-md shadow-xl transition-transform duration-300 ease-in-out border border-white/70"
>

  <div v-if="isSidebarOpen" class="flex flex-col gap-4">
    <TransitionGroup name="fade" tag="div" class="contents">
      <CityCard
        v-for="city in cities"
        :key="city.name"
        :name="city.name"
        :imageAlt="city.imageAlt"
        :imageSrc="city.imageSrc"
      />
      <CityCard
        v-if="!auth.isConnected"
        name="Connectez-vous pour ajouter vos favoris"
        imageSrc=""
        isPlaceholder
        customClass="connect-card"
      />
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
