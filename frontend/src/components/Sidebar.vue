<script setup>
import CityCard from './CityCard.vue'
import { ref, computed } from 'vue'
import { ChevronLeftIcon } from '@heroicons/vue/24/outline'
import sunImg from '../assets/sun.png'
import rainImg from '../assets/rain.png'
import lightRainImg from '../assets/light-rain.png'

const isLoggedIn = ref(false)
const isOpen = ref(true)

const cities = computed(() =>
  isLoggedIn.value
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
  isOpen.value = !isOpen.value
}
</script>

<template>
    <main class="flex flex-col justify-center py-4 h-screen relative">
        <section class="absolute inset-y-4 left-0 grid grid-cols-[1fr_auto] mx-auto gap-2 p-6 pr-2 rounded-r-xl bg-white/40 backdrop-blur-md shadow-xl w-full overflow-y-auto transition-transform duration-300 ease-in-out border border-white/70" :class="isOpen ? 'translate-x-0' : '-translate-x-[83%]'">
        <div class="flex flex-col gap-4">
            <TransitionGroup
                name="fade"
                tag="div"
                class="contents"
            >
                <CityCard
                    v-for="city in cities"
                    :key="city.name"
                    :name="city.name"
                    :imageAlt="city.imageAlt"
                    :imageSrc="city.imageSrc"
                />
                <CityCard
                    v-if="!isLoggedIn"
                    name="Connectez-vous pour ajouter vos favoris"
                    imageSrc=""
                    isPlaceholder
                    customClass="connect-card"
                />
                <CityCard
                    v-if="isLoggedIn"
                    name="Ajouter une ville à vos favoris"
                    isPlaceholder
                    customClass="connect-card"
                />
            </TransitionGroup>
        </div>
        
        <div class="flex items-center justify-center">
            <button @click="toggleSidebar" class="rounded-full hover:bg-white/10 transition-colors duration-200 w-7 h-7 flex items-end justify-end">
                <ChevronLeftIcon
                    class="w-6 h-6 transition-transform duration-300"
                    :class="{ 'rotate-180': !isOpen }"
                />
            </button>
        </div>
    </section>
    </main>
</template>

<style scoped>

</style>
