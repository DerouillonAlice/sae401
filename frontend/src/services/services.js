import axios from 'axios'

const handleError = (error) => {
  if (error.response) {
    console.error('API error:', error.response.data)
    return {
      success: false,
      message: error.response.data.message || 'Une erreur est survenue côté serveur.',
      status: error.response.status
    }
  } else if (error.request) {
    console.error('No response received:', error.request)
    return {
      success: false,
      message: 'Aucune réponse du serveur.',
      status: null
    }
  } else {
    console.error('Unexpected error:', error.message)
    return {
      success: false,
      message: error.message,
      status: null
    }
  }
}

export const getWeatherByCity = async (city) => {
  try {
    const res = await axios.get(`/api/weather/${encodeURIComponent(city)}`)
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

export const fetchFavorites = async () => {
  try {
    const res = await axios.get('/api/favorites')
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

export const getProfile = async () => {
  try {
    const res = await axios.get('/api/user/profile', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    })
    return { success: true, data: res.data }
  } catch (error) {
    return handleError(error)
  }
}

export const updateProfile = async (profile) => {
  try {
    const res = await axios.put('/api/user/profile', profile, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    })
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
    }, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    })
    return { success: true, data: res.data }
  } catch (error) {
    return handleError(error)
  }
}
