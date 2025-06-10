<script setup>
import { ref } from 'vue'
import WeatherDetailsGrid from '../components/WeatherDetailsGrid.vue'
import Sidebar from '../components/Sidebar.vue'
import WeekdaysTab from '../components/WeekdaysTab.vue'
import CityHeading from '../components/CityHeading.vue'

const selectedDayIndex = ref(0)

const handleDayChange = (dayIndex) => {
  selectedDayIndex.value = dayIndex
}
</script>

<template>
    <main class="grid grid-cols-1">
        <section class="grid gap-4 mx-8 ">
            <CityHeading />

            <WeekdaysTab @update:modelValue="handleDayChange" />

            <section class="weather-info">
                <div class="weather-info__details">
                    <div class="weather-info__param flex-1 w-full">
                        <WeatherDetailsGrid :selectedDayIndex="selectedDayIndex" />
                    </div>
                </div>
            </section>
        </section>
    </main>
</template>

