<template>
    <div class="max-w-lg mx-auto mt-10 p-6 bg-white rounded-xl shadow">
      <h2 class="text-2xl font-bold mb-4">Mon profil</h2>
      <div v-if="loading">Chargement...</div>
      <div v-else-if="error" class="text-red-500">{{ error }}</div>
      <div v-else>
        <div class="mb-2"><strong>Nom :</strong> {{ user.firstname }}</div>
        <div class="mb-2"><strong>Email :</strong> {{ user.email }}</div>
        <div class="mb-2"><strong>Unité température :</strong> {{ user.uniteTemperature }}</div>
        <div class="mb-2"><strong>Unité pression :</strong> {{ user.unitePression }}</div>
        <div class="mb-2"><strong>Unité vent :</strong> {{ user.uniteVent }}</div>
        <div class="mb-2"><strong>Notification email :</strong> {{ user.emailNotification || 'Non' }}</div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  import axios from 'axios'
  
  const user = ref({})
  const loading = ref(true)
  const error = ref('')
  
  onMounted(async () => {
    try {
      const response = await axios.get('/api/user/profile', {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`
        }
      })
      user.value = response.data
    } catch (e) {
      console.error('Erreur lors de la récupération du profil :', e)
      if (e.response?.status === 401) {
        localStorage.removeItem('token')
        window.location.href = '/connexion'
      } else {
        error.value = "Impossible de charger le profil"
      }
    } finally {
      loading.value = false
    }
  })
  </script>