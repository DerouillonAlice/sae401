import { ref } from 'vue'
import { getAlertConfig, updateAlertConfig, fetchFavorites, testAlert } from '../services/services'

export function useAlert() {
  const alertConfig = ref({
    alertTypes: [],
    severity: 'moderate',
    locations: []
  })
  const alertLoading = ref(false)
  const alertError = ref('')
  const alertSuccess = ref('')
  const userFavorites = ref([])

  const alertTypes = [
    { value: 'thunderstorm', label: 'Orages' },
    { value: 'tornado', label: 'Tornades' },
    { value: 'hurricane', label: 'Ouragans' },
    { value: 'wind', label: 'Vents forts' },
    { value: 'rain', label: 'Pluies' },
    { value: 'flood', label: 'Inondations' },
    { value: 'snow', label: 'Neige' },
    { value: 'ice', label: 'Verglas' },
    { value: 'fog', label: 'Brouillard' },
    { value: 'heat', label: 'Canicule' },
    { value: 'cold', label: 'Grand froid' }
  ]

  const loadUserFavorites = async () => {
    try {
      const response = await fetchFavorites()
      if (response.success) {
        userFavorites.value = response.data || []
      }
    } catch (e) {
      console.error('Erreur lors du chargement des favoris:', e)
    }
  }

  const loadAlertConfig = async () => {
    alertLoading.value = true
    alertError.value = ''

    try {
      const response = await getAlertConfig()
      if (response.success && response.data) {
        alertConfig.value = {
          alertTypes: response.data.alertTypes || [],
          severity: response.data.severity || 'moderate',
          locations: response.data.locations || []
        }
      }
    } catch (e) {
      console.error('Erreur lors du chargement des alertes:', e)
      alertError.value = 'Impossible de charger la configuration des alertes'
    } finally {
      alertLoading.value = false
    }
  }

  const saveAlertConfig = async () => {
    alertLoading.value = true
    alertError.value = ''
    alertSuccess.value = ''

    try {
      const configToSave = {
        ...alertConfig.value,
        enabled: alertConfig.value.alertTypes.length > 0 || alertConfig.value.locations.length > 0
      }

      const response = await updateAlertConfig(configToSave)
      if (response.success) {
        alertSuccess.value = 'Configuration des alertes sauvegardée !'
        setTimeout(() => { alertSuccess.value = '' }, 3000)
      } else {
        alertError.value = response.message || 'Erreur lors de la sauvegarde'
      }
    } catch (e) {
      console.error('Erreur lors de la sauvegarde des alertes:', e)
      alertError.value = e.response?.data?.message || 'Impossible de sauvegarder la configuration'
      setTimeout(() => { alertError.value = '' }, 3000)
    } finally {
      alertLoading.value = false
    }
  }

  const toggleAlertType = (type) => {
    const index = alertConfig.value.alertTypes.indexOf(type)
    if (index > -1) {
      alertConfig.value.alertTypes.splice(index, 1)
    } else {
      alertConfig.value.alertTypes.push(type)
    }
  }

  const toggleFavoriteLocation = (city) => {
    const index = alertConfig.value.locations.indexOf(city)
    if (index > -1) {
      alertConfig.value.locations.splice(index, 1)
    } else {
      alertConfig.value.locations.push(city)
    }
  }

  const initAlerts = async () => {
    await Promise.all([
      loadUserFavorites(),
      loadAlertConfig()
    ])
  }

  const testAlertSystem = async () => {
    alertLoading.value = true
    alertError.value = ''
    alertSuccess.value = ''

    try {
      const response = await testAlert()
      if (response.success) {
        alertSuccess.value = `✅ Test réussi ! Alerte simulée générée`
        setTimeout(() => { alertSuccess.value = '' }, 5000)
        
        // Retourner l'alerte pour l'affichage
        return response.data.alert
      }
    } catch (e) {
      console.error('Erreur lors du test:', e)
      alertError.value = 'Erreur lors du test des alertes'
      setTimeout(() => { alertError.value = '' }, 3000)
    } finally {
      alertLoading.value = false
    }
  }

  return {
    alertConfig,
    alertLoading,
    alertError,
    alertSuccess,
    userFavorites,
    alertTypes,
    
    loadUserFavorites,
    loadAlertConfig,
    saveAlertConfig,
    toggleAlertType,
    toggleFavoriteLocation,
    initAlerts,
    testAlertSystem
  }
}