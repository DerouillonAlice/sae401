<template>
  <div class="h-full w-full flex flex-col items-center justify-center rounded-xl shadow text-lg font-semibold text-center p-4 border backdrop-blur-sm bg-white/60 space-y-2">
    <component :is="getIconComponent(name)" class="w-10 h-10 text-black" />
    <div>
      {{ getBlockContent(name) }}
    </div>
  </div>
</template>

<script setup>
import SunCalc from 'suncalc'
import { formatWind, formatPressure } from '@/services/unitUtils'
import {
  WindIcon,
  BarChart3Icon,
  DropletIcon,
  EyeIcon,
  CloudIcon,
  SunIcon
} from 'lucide-vue-next'

const props = defineProps({
  name: String,
  currentDayData: Object,
  cityData: Object,
  forecastData: Array,
  selectedDayIndex: Number
})

function getIconComponent(name) {
  switch (name) {
    case 'Vent': return WindIcon
    case 'Cycle Soleil': return SunIcon
    case 'Pression': return BarChart3Icon
    case 'Humidité': return DropletIcon
    case 'Visibilité': return EyeIcon
    case 'Nuages': return CloudIcon
    default: return SunIcon
  }
}

function isFrenchCity() {
  return props.cityData?.country === 'FR'
}

function formatSunTime(date, timezoneOffsetSeconds) {
  if (isFrenchCity()) {
    return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
  }
  const local = new Date(date.getTime() + timezoneOffsetSeconds * 1000)
  return local.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
}

function getBlockContent(name) {
  const data = props.currentDayData
  if (!data) return name

  switch (name) {
    case 'Vent': return formatWind(data.wind?.speed)
    case 'Cycle Soleil': {
      const tz = props.cityData?.timezone ?? 0
      const lat = props.cityData?.coord?.lat
      const lon = props.cityData?.coord?.lon
      let date
      if (props.selectedDayIndex === 0) {
        date = new Date()
      } else {
        const dayData = props.forecastData[props.selectedDayIndex - 1]
        date = dayData?.date || new Date()
      }

      if (lat && lon && date) {
        const times = SunCalc.getTimes(date, lat, lon)
        const sunrise = formatSunTime(times.sunrise, tz)
        const sunset = formatSunTime(times.sunset, tz)
        return `${sunrise} / ${sunset}`
      }
      return '—'
    }
    case 'Pression': return formatPressure(data.main?.pressure)
    case 'Humidité': return `${data.main?.humidity || 0} %`
    case 'Visibilité': return `${Math.round((data.visibility || 0) / 1000)} km`
    case 'Nuages': return `${data.clouds?.all || 0} %`
    default: return name
  }
}
</script>