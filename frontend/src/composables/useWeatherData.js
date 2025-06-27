import { ref } from 'vue'
import { getWeatherByCity, getForecastByCity } from '@/services/services'

export function useWeatherData(route, auth) {
  const weatherData = ref(null)
  const forecastData = ref([])
  const todayHourlyData = ref([])
  const cityData = ref(null)

  function getDefaultVille() {
    if (auth.isConnected && auth.favorites.length > 0) {
      return auth.favorites[0].city
    }
    return 'Paris'
  }

  function fetchWeather() {
    const ville = route.query.ville || getDefaultVille()
    getWeatherByCity(ville)
      .then(res => { weatherData.value = res.data })
  }

  function fetchForecast() {
    const ville = route.query.ville || getDefaultVille()
    getForecastByCity(ville)
      .then(res => {
        const data = res.data
        if (Array.isArray(data.list)) {
          cityData.value = data.city

          const today = new Date()
          const todayString = today.toDateString()

          const todayData = []
          const otherDaysData = []

          data.list.forEach(item => {
            const itemDate = new Date(item.dt * 1000)
            if (itemDate.toDateString() === todayString) {
              todayData.push(item)
            } else {
              otherDaysData.push(item)
            }
          })

          fetchTodayForecast(todayData)

          const dailyForecasts = []
          const processedDates = new Set()

          otherDaysData.forEach(item => {
            const date = new Date(item.dt * 1000)
            const dateKey = date.toDateString()

            if (!processedDates.has(dateKey)) {
              processedDates.add(dateKey)
              dailyForecasts.push({
                ...item,
                date: date,
                dayName: date.toLocaleDateString('fr-FR', { weekday: 'long' })
              })
            }
          })

          forecastData.value = dailyForecasts.slice(0, 7)
        }
      })
  }

  function fetchTodayForecast(forecastList) {
    const findClosest = (targetHour, startHour, endHour) => {
      const candidates = forecastList.filter(item => {
        const hour = new Date(item.dt * 1000).getHours()
        return hour >= startHour && hour < endHour
      })
      if (!candidates.length) return null
      return candidates.reduce((prev, curr) => {
        const prevHour = new Date(prev.dt * 1000).getHours()
        const currHour = new Date(curr.dt * 1000).getHours()
        return Math.abs(currHour - targetHour) < Math.abs(prevHour - targetHour) ? curr : prev
      })
    }

    const matin = findClosest(9, 6, 12)
    const apresmidi = findClosest(15, 12, 18)
    const soir = findClosest(20, 18, 23)

    todayHourlyData.value = [
      matin && {
        label: 'Matin',
        temp: matin.main?.temp,
        icon: matin.weather?.[0]?.icon
      },
      apresmidi && {
        label: 'Après-midi',
        temp: apresmidi.main?.temp,
        icon: apresmidi.weather?.[0]?.icon
      },
      soir && {
        label: 'Soir',
        temp: soir.main?.temp,
        icon: soir.weather?.[0]?.icon
      }
    ].filter(Boolean)
  }

  return {
    weatherData,
    forecastData,
    todayHourlyData,
    cityData,
    fetchWeather,
    fetchForecast,
    getDefaultVille
  }
}