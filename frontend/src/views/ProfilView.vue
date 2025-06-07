<template>
  <div class="w-full max-w-2xl mx-auto p-6 rounded-2xl bg-white/60 backdrop-blur-md border shadow h-full text-black">
    <h2 class="text-3xl font-bold mb-6 text-center">Mon profil</h2>

    <div v-if="loading" class="text-center text-gray-600">Chargement...</div>

    <div v-else-if="error" class="text-center text-red-600 font-semibold">
      {{ error }}
    </div>

    <div v-else>
      <form @submit.prevent="updateProfile" class="space-y-4 relative">
        <div>
          <label class="font-semibold">Nom</label>
          <input
            v-model="user.firstname"
            type="text"
            class="w-full mt-1 px-4 py-2 rounded-xl bg-white border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
          />
        </div>

        <div>
          <label class="font-semibold">Email</label>
          <input
            v-model="user.email"
            type="email"
            class="w-full mt-1 px-4 py-2 rounded-xl bg-white border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
          />
        </div>

        <div>
          <label class="font-semibold">Unité température</label>
          <select
            v-model="user.uniteTemperature"
            class="w-full mt-1 px-4 py-2 rounded-xl bg-white border border-gray-300 shadow-sm"
          >
            <option value="Celsius">Celsius</option>
            <option value="Fahrenheit">Fahrenheit</option>
          </select>
        </div>

        <div>
          <label class="font-semibold">Unité pression</label>
          <select
            v-model="user.unitePression"
            class="w-full mt-1 px-4 py-2 rounded-xl bg-white border border-gray-300 shadow-sm"
          >
            <option value="hPa">hPa</option>
            <option value="mmHg">mmHg</option>
          </select>
        </div>

        <div>
          <label class="font-semibold">Unité vent</label>
          <select
            v-model="user.uniteVent"
            class="w-full mt-1 px-4 py-2 rounded-xl bg-white border border-gray-300 shadow-sm"
          >
            <option value="km/h">km/h</option>
            <option value="m/s">m/s</option>
          </select>
        </div>

        <!-- Messages de succès/erreur -->
        <div class="relative">
          <button
            type="submit"
            class="w-full bg-black text-white py-2 rounded-xl font-bold hover:bg-gray-800 transition"
          >
            Mettre à jour
          </button>
          <div
            v-if="success"
            class="absolute inset-0 flex items-center justify-center bg-green-100 text-green-600 font-medium border border-green-300 rounded-xl p-2"
          >
            {{ success }}
          </div>
          <div
            v-if="error"
            class="absolute inset-0 flex items-center justify-center bg-red-100 text-red-600 font-medium border border-red-300 rounded-xl p-2"
          >
            {{ error }}
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const user = ref({})
const loading = ref(true)
const error = ref('')
const success = ref('')

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

const updateProfile = async () => {
  success.value = ''
  error.value = ''
  try {
    await axios.put('/api/user/profile', user.value, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    })
    success.value = 'Profil mis à jour avec succès !'
    // Efface le message de succès après 3 secondes
    setTimeout(() => {
      success.value = ''
    }, 3000)
  } catch (e) {
    console.error('Erreur lors de la mise à jour du profil :', e)
    error.value = "Impossible de mettre à jour le profil"
    // Efface le message d'erreur après 3 secondes
    setTimeout(() => {
      error.value = ''
    }, 3000)
  }
}
</script>
