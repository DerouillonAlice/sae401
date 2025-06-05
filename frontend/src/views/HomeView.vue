<script setup>
import WeatherDetailsGrid from '../components/WeatherDetailsGrid.vue'
import Sidebar from '../components/Sidebar.vue'
import WeekdaysTab from '../components/WeekdaysTab.vue'
import CityHeading from '../components/CityHeading.vue'
</script>

<template>
    <main class="grid h-screen grid-cols-1">
        <section class="grid gap-4 mx-8 grid-rows-[auto_auto_auto_1fr]">
            <CityHeading />

            <WeekdaysTab />

            <section class="weather-info">
                <div class="weather-info__details">
                    <div class="weather-info__param flex-1 w-full">
                        <WeatherDetailsGrid />
                    </div>
                </div>
            </section>
        </section>
    </main>
</template>

