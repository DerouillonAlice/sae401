import axios from 'axios'

export const getWeatherByCity = (city) => {
  return axios.get(`/api/weather/${encodeURIComponent(city)}`)
}

export const searchCities = (query) => {
  return axios.get(`/api/search/${encodeURIComponent(query)}`)
}

export const addFavorite = (city) => {
  return axios.post('/api/favorites', { city })
}

export const removeFavorite = (city) => {
  return axios.delete(`/api/favorites/${encodeURIComponent(city)}`)
}

export const fetchFavorites = () => {
  return axios.get('/api/favorites')
}

export const getForecastByCity = (city) => {
  return axios.get(`/api/forecast/${encodeURIComponent(city)}`)
}

export const login = (email, password) => {
  return axios.post('/api/login_check', {
    username: email,
    password: password
  })
}

export const register = (firstname, email, password) => {
  return axios.post('/api/register', {
    firstname,
    email,
    password
  })
}

export const getProfile = () => {
  return axios.get('/api/user/profile', {
    headers: {
      Authorization: `Bearer ${localStorage.getItem('token')}`
    }
  })
}

export const updateProfile = (profile) => {
  return axios.put('/api/user/profile', profile, {
    headers: {
      Authorization: `Bearer ${localStorage.getItem('token')}`
    }
  })
}

export const changePassword = (oldPassword, newPassword) => {
  return axios.post('/api/user/change-password', {
    oldPassword,
    newPassword
  }, {
    headers: {
      Authorization: `Bearer ${localStorage.getItem('token')}`
    }
  })
}

export const updateFavoriteConfig = (favoriteId, config) => {
  return axios.put(`/api/favorites/${favoriteId}`, config, {
    headers: {
      Authorization: `Bearer ${localStorage.getItem('token')}`
    }
  })
}