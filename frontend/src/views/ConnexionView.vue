<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()

const email = ref('')
const password = ref('')
const error = ref('')
const success = ref('')

const submitLogin = async (e) => {
  e.preventDefault()
  error.value = ''
  success.value = ''
  try {
    const loginRes = await axios.post('/api/login_check', {
      username: email.value,
      password: password.value
    })
    localStorage.setItem('token', loginRes.data.token)
    success.value = 'Connexion réussie !'
    await auth.checkAuth()
    window.location.href = '/'
  } catch (err) {
    error.value = err.response?.data?.message || 'Erreur lors de la connexion'
  }
}
</script>

<template>
  <div class="flex justify-center items-center flex-1">
    <form
      class="bg-white/30 backdrop-blur-lg p-10 rounded-xl shadow-lg w-full max-w-md"
      @submit="submitLogin"
    >
      <h2 class="text-2xl font-bold mb-6 text-center">Connexion</h2>
      <div class="mb-4">
        <label class="block text-sm font-semibold mb-1">Email</label>
        <input v-model="email" type="email" class="w-full px-4 py-2 rounded-xl bg-white shadow" required />
      </div>
      <div class="mb-6">
        <label class="block text-sm font-semibold mb-1">Mot de passe</label>
        <input v-model="password" type="password" class="w-full px-4 py-2 rounded-xl bg-white shadow" required />
      </div>
      <button
        type="submit"
        class="w-full bg-black text-white py-2 rounded-xl font-bold hover:bg-gray-800 transition"
      >
        Se connecter
      </button>
      <div v-if="error" class="text-red-600 mt-4 text-center">{{ error }}</div>
      <div v-if="success" class="text-green-600 mt-4 text-center">{{ success }}</div>
      <div class="mt-6 text-center text-sm text-gray-700">
        Vous n'avez pas de compte ?
        <router-link to="/inscription" class="text-blue-600 hover:underline font-semibold">
          Inscrivez-vous
        </router-link>
      </div>
    </form>
  </div>
</template>