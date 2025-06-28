import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    isConnected: false,
    user: null,
    favorites: []
  }),
  actions: {
    async checkAuth() {
      try {
        const token = localStorage.getItem('token')
        if (!token) throw new Error('No token')
        const res = await axios.get('/api/user/profile', {
          headers: { Authorization: `Bearer ${token}` }
        })
        this.isConnected = true
        this.user = res.data
        await this.fetchFavorites()
      } catch (error) {
        console.error('Erreur lors de la vérification de l\'authentification :', error)
        this.isConnected = false
        this.user = null
        this.favorites = []
        localStorage.removeItem('token')
      }
    },

    async reorderFavorites(newOrder) {
      try {
        const token = localStorage.getItem('token')
        if (!token) return
    
        await axios.post('/api/favorites/reorder', newOrder, {
          headers: { Authorization: `Bearer ${token}` }
        })
    
        await this.fetchFavorites()
      } catch (error) {
        console.error('Erreur lors du réordonnancement des favoris :', error)
      }
    },    
    
    async fetchFavorites() {
      try {
        const token = localStorage.getItem('token')
        if (!token) return
        const res = await axios.get('/api/favorites', {
          headers: { Authorization: `Bearer ${token}` }
        })
        this.favorites = [...res.data].sort((a, b) => a.position - b.position)
      } catch (error) {
        console.error('Erreur lors de la récupération des favoris :', error)
        this.favorites = []
      }
    },
    
    async addFavorite(city) {
      console.log('Ajout du favori :', city)
      try {
        const token = localStorage.getItem('token')
        if (!token) return

        const position = this.favorites.length

        await axios.post(
          '/api/favorites',
          { city, position },
          { headers: { Authorization: `Bearer ${token}` } }
        )

        await this.fetchFavorites()
      } catch (error) {
        console.error('Erreur lors de l\'ajout du favori :', error)
      }
    },
    
    async removeFavorite(cityName) {
      console.log('Suppression du favori :', cityName)
      try {
        const token = localStorage.getItem('token')
        if (!token) return
        
        const favorite = this.favorites.find(fav => fav.city.toLowerCase() === cityName.toLowerCase())
        if (!favorite) {
          console.error('Favori non trouvé :', cityName)
          return
        }
        
        await axios.delete(`/api/favorites/${favorite.id}`, {
          headers: { Authorization: `Bearer ${token}` }
        })
        
        this.favorites = this.favorites.filter(fav => fav.id !== favorite.id)
      } catch (error) {
        console.error('Erreur lors de la suppression du favori :', error)
      }
    }
  }
})