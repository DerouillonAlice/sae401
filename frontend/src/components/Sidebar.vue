<script setup>
import CityCard from './CityCard.vue'
import { ref, computed } from 'vue'
import sunImg from '../assets/sun.png'
import rainImg from '../assets/rain.png'
import lightRainImg from '../assets/light-rain.png'

const isLoggedIn = ref(false)

const cities = computed(() =>
  isLoggedIn.value
    ? [
        { name: 'Paris', imageSrc: sunImg },
        { name: 'Londres', imageSrc: rainImg },
        { name: 'New York', imageSrc: sunImg },
        { name: 'Tokyo', imageSrc: lightRainImg },
        { name: 'Montréal', imageSrc: lightRainImg }
      ]
    : [
        { name: 'Paris', imageSrc: sunImg },
        { name: 'New York', imageSrc: sunImg },
        { name: 'Londres', imageSrc: rainImg },
        { name: 'Reims', imageSrc: lightRainImg }
      ]
)
</script>

<template>
    <main class="flex flex-col justify-center py-4 h-screen">
        <section class="grid mx-auto gap-4 p-6 rounded-r-xl bg-white/20 backdrop-blur-md shadow-xl w-full overflow-y-auto" :class="[`grid-rows-${cities.length}`]">
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
            </TransitionGroup>
        </section>
    </main>
</template>

<style scoped>

@media (max-width: 768px) {
  .sidebar {
    flex-direction: row;
    
  }
}
</style>
