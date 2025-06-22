import { useAuthStore } from '@/stores/auth'

export function formatTemperature(temp) {
  const auth = useAuthStore()
  if (temp == null) return '—'
  if (auth.user?.uniteTemperature === 'Fahrenheit') {
    return Math.round(temp * 9/5 + 32) + '°F'
  }
  return Math.round(temp) + '°C'
}

export function formatWind(speed) {
  const auth = useAuthStore()
  if (speed == null) return '—'
  if (auth.user?.uniteVent === 'km/h') {
    return Math.round(speed * 3.6) + ' km/h'
  }
  return Math.round(speed) + ' m/s'
}

export function formatPressure(pressure) {
  const auth = useAuthStore()
  if (pressure == null) return '—'
  if (auth.user?.unitePression === 'mmHg') {
    return Math.round(pressure * 0.750062) + ' mmHg'
  }
  return Math.round(pressure) + ' hPa'
}