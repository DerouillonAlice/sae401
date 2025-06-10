import { computed } from 'vue';

export function useWeatherImage(weatherData: any) {
  const imageUrl = computed(() => {
    if (!weatherData.value || !weatherData.value.weather) {
      return new URL('@/assets/sun.png', import.meta.url).href;
    }

    const temp = weatherData.value.main.temp;
    const condition = weatherData.value.weather[0].main;
    const iconCode = weatherData.value.weather[0].icon;

    if (iconCode.endsWith('n')) {
      return new URL('@/assets/night.png', import.meta.url).href;
    }

    switch (condition) {
      case 'Thunderstorm':
        return new URL('@/assets/storm.png', import.meta.url).href;
      case 'Rain':
        return new URL('@/assets/rain.png', import.meta.url).href;
      case 'Drizzle':
        return new URL('@/assets/light-rain.png', import.meta.url).href;
      case 'Snow':
        return new URL('@/assets/mixed-weather.png', import.meta.url).href;
      case 'Clouds':
        return new URL('@/assets/cloudy.png', import.meta.url).href;
      case 'Clear':
        if (temp > 28) return new URL('@/assets/sun.png', import.meta.url).href;
        if (temp < 10) return new URL('@/assets/mixed-weather.png', import.meta.url).href;
        return new URL('@/assets/sun.png', import.meta.url).href;
      case 'Mist':
      case 'Smoke':
      case 'Haze':
      case 'Fog':
      case 'Dust':
      case 'Sand':
      case 'Ash':
        return new URL('@/assets/mixed-weather.png', import.meta.url).href;
      default:
        return new URL('@/assets/fond.png', import.meta.url).href;
    }
  });

  return { imageUrl };
}
