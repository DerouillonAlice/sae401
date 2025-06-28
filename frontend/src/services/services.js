import axios from 'axios'

// Configuration directe sans variable d'environnement
axios.defaults.baseURL = '' // Laissez vide si l'API est sur le même domaine
// OU spécifiez l'URL complète :
// axios.defaults.baseURL = 'http://localhost:8000'

// Intercepteur pour ajouter automatiquement le token
axios.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// Intercepteur pour gérer les erreurs de validation Symfony
axios.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 422 && error.response.data.violations) {
      // Erreurs de validation Symfony
      const violations = error.response.data.violations
      const errors = violations.map(v => v.message).join(', ')
      
      return Promise.reject({
        ...error,
        validationErrors: violations,
        message: errors || 'Données invalides'
      })
    }
    return Promise.reject(error)
  }
)

const handleError = (error) => {
  let message = 'Une erreur est survenue'
  let validationErrors = []

  if (error.response) {
    console.error('API error:', error.response.data)
    
    if (error.response.status === 422 && error.response.data.violations) {
      validationErrors = error.response.data.violations
      message = validationErrors.map(v => v.message).join(', ')
    } else if (error.response.data.message) {
      message = error.response.data.message
    } else if (error.response.data.error) {
      message = error.response.data.error
    } else {
      message = 'Une erreur est survenue côté serveur.'
    }
    
    return {
      success: false,
      message,
      validationErrors,
      status: error.response.status
    }
  } else if (error.request) {
    console.error('No response received:', error.request)
    return {
      success: false,
      message: 'Aucune réponse du serveur.',
      validationErrors: [],
      status: null
    }
  } else {
    console.error('Unexpected error:', error.message)
    return {
      success: false,
      message: error.message,
      validationErrors: [],
      status: null
    }
  }
}

// Services Weather
export const getWeatherByCity = async (city) => {
  try {
    const res = await axios.get(`/api/weather/${encodeURIComponent(city)}`)
    return { success: true, data: res.data }
  } catch (error) {
    return handleError(error)
  }
}

export const getForecastByCity = async (city) => {
  try {
    const res = await axios.get(`/api/forecast/${encodeURIComponent(city)}`)
    return { success: true, data: res.data }
  } catch (error) {
    return handleError(error)
  }
}

export const searchCities = async (query) => {
  try {
    const res = await axios.get(`/api/search/${encodeURIComponent(query)}`)
    return { success: true, data: res.data }
  } catch (error) {
    return handleError(error)
  }
}

// Services Auth
export const login = async (email, password) => {
  try {
    const res = await axios.post('/api/login_check', {
      username: email,
      password
    })
    return { success: true, data: res.data }
  } catch (error) {
    return handleError(error)
  }
}

export const register = async (firstname, email, password) => {
  try {
    const res = await axios.post('/api/register', {
      firstname,
      email,
      password
    })
    return { success: true, data: res.data }
  } catch (error) {
    return handleError(error)
  }
}

// Services User
export const getProfile = async () => {
  try {
    const res = await axios.get('/api/user/profile')
    return { success: true, data: res.data }
  } catch (error) {
    return handleError(error)
  }
}

export const updateProfile = async (profile) => {
  try {
    const res = await axios.put('/api/user/profile', profile)
    return { success: true, data: res.data }
  } catch (error) {
    return handleError(error)
  }
}

export const changePassword = async (oldPassword, newPassword) => {
  try {
    const res = await axios.post('/api/user/change-password', {
      oldPassword,
      newPassword
    })
    return { success: true, data: res.data }
  } catch (error) {
    return handleError(error)
  }
}

// Services Favorites
export const fetchFavorites = async () => {
  try {
    const res = await axios.get('/api/favorites')
    return { success: true, data: res.data }
  } catch (error) {
    return handleError(error)
  }
}

export const addFavorite = async (city) => {
  try {
    const res = await axios.post('/api/favorites', { city })
    return { success: true, data: res.data }
  } catch (error) {
    return handleError(error)
  }
}


export const requestResetPassword = (email) => {
  return axios.post('/api/request-reset-password', { email })
}

export const resetPassword = (token, newPassword) => {
  return axios.post('/api/reset-password', { token, newPassword })

}

  export const removeFavorite = async (city) => {
  try {
    const res = await axios.delete(`/api/favorites/${encodeURIComponent(city)}`)
    return { success: true, data: res.data }
  } catch (error) {
    return handleError(error)
  }
}

export const updateFavoriteConfig = async (favoriteId, config) => {
  try {
    const res = await axios.put(`/api/favorites/${favoriteId}`, config)
    return { success: true, data: res.data }
  } catch (error) {
    return handleError(error)
  }
}

// Services Alerts
export const getAlertConfig = async () => {
  try {
    const res = await axios.get('/api/user/alert-config')
    return { success: true, data: res.data }
  } catch (error) {
    return handleError(error)
  }
}

export const updateAlertConfig = async (config) => {
  try {
    const res = await axios.put('/api/user/alert-config', config)
    return { success: true, data: res.data }
  } catch (error) {
    return handleError(error)
  }
}

export const testAlert = async () => {
  try {
    const res = await axios.post('/api/user/alert-config/test', {})
    return { success: true, data: res.data }
  } catch (error) {
    return handleError(error)
  }
}
