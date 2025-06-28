import axios from 'axios'

// Configuration directe sans variable d'environnement
axios.defaults.baseURL = ''

// Intercepteurs...
axios.interceptors.request.use((config) => {
  const publicRoutes = [
    '/api/contact',
    '/api/login_check',
    '/api/register',
    '/api/request-reset-password',
    '/api/reset-password'
  ]

  const isPublic = publicRoutes.some(route => config.url.includes(route))

  if (!isPublic) {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
  }

  return config
})

axios.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 422 && error.response.data.violations) {
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

// ========================================
// SERVICES WEATHER
// ========================================
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

// ========================================
// SERVICES AUTH
// ========================================
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

// ========================================
// SERVICES USER
// ========================================
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

export const requestResetPassword = async (email) => {
  try {
    const res = await axios.post('/api/request-reset-password', { email })
    return { success: true, data: res.data }
  } catch (error) {
    return handleError(error)
  }
}

export const resetPassword = async (token, newPassword) => {
  try {
    const res = await axios.post('/api/reset-password', { token, newPassword })
    return { success: true, data: res.data }
  } catch (error) {
    return handleError(error)
  }
}

// ========================================
// SERVICES FAVORITES
// ========================================
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

// ========================================
// SERVICES ALERTS
// ========================================
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

// Service Contact
export const sendContactMessage = async (name, email, message) => {
  try {
    const res = await axios.post('http://localhost:8319/api/contact', {
      name,
      email,
      message
    })
    return { success: true, data: res.data }
  } catch (error) {
    return handleError(error)
  }
}

