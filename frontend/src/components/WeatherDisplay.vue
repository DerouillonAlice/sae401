<template>
    <div class="">
      <input v-model="city" placeholder="Search city…" />
      <button @click="loadWeather">Get Weather</button>
  
      <div v-if="weather">
        <h2>Weather in {{ weather.name }}</h2>
        <p>{{ weather.weather[0].description }}</p>
        <p>🌡 Temp: {{ weather.main.temp }} °C</p>
        <p>💨 Wind: {{ weather.wind.speed }} m/s</p>
      </div>
    </div>
  </template>
  
  <script setup>
import { ref } from 'vue'
import { getWeatherByCity } from '../services/services'

const city = ref('')
const weather = ref(null)

async function loadWeather() {
  try {
    const res = await getWeatherByCity(city.value)
    weather.value = res.data
  } catch (err) {
    alert('Error fetching weather')
  }
}
</script>
  
  <style scoped>

  </style>
