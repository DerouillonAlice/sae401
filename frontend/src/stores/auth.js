import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    isConnected: false,
    user: null,
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
      } catch {
        this.isConnected = false
        this.user = null
      }
    }
  }
})