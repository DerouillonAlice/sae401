<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth' // Assurez-vous que le chemin est correct

const auth = useAuthStore()

const firstname = ref('')
const email = ref('')
const password = ref('')
const error = ref('')
const success = ref('')

const submitRegister = async (e) => {
  e.preventDefault()
  error.value = ''
  success.value = ''
  try {
    await axios.post('/api/register', {
      firstname: firstname.value,
      email: email.value,
      password: password.value
    })
    const loginRes = await axios.post('/api/login_check', {
      username: email.value,
      password: password.value
    })
    localStorage.setItem('token', loginRes.data.token)
    success.value = 'Inscription réussie !'
    await auth.checkAuth()
    window.location.href = '/'

  } catch (err) {
    error.value = err.response?.data?.message || 'Erreur lors de l\'inscription'
  }
}
</script>

<template>
  <div class="flex justify-center items-center flex-1">
    <form
      class="bg-white/30 backdrop-blur-lg p-10 rounded-xl shadow-lg w-full max-w-md"
      @submit="submitRegister"
    >
      <h2 class="text-2xl font-bold mb-6 text-center">Créer un compte</h2>
      <div class="mb-4">
        <label class="block text-sm font-semibold mb-1">Nom</label>
        <input v-model="firstname" type="text" class="w-full px-4 py-2 rounded-xl bg-white shadow" required />
      </div>
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
        S'inscrire
      </button>
      <div v-if="error" class="text-red-600 mt-4 text-center">{{ error }}</div>
      <div v-if="success" class="text-green-600 mt-4 text-center">{{ success }}</div>
    </form>
  </div>
</template>
