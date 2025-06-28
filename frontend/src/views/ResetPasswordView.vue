<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { resetPassword } from '@/services/services'

const route = useRoute()
const router = useRouter()

const token = ref('')
const password = ref('')
const confirmPassword = ref('')
const error = ref('')
const success = ref('')

onMounted(() => {
  token.value = route.query.token || ''
})

const handleSubmit = async () => {
  if (password.value !== confirmPassword.value) {
    error.value = 'Les mots de passe ne correspondent pas.'
    return
  }

  try {
    await resetPassword(token.value, password.value)
    success.value = 'Mot de passe réinitialisé avec succès.'
    setTimeout(() => router.push('/connexion'), 2000)
  } catch (err) {
    error.value = err.response?.data?.error || 'Erreur de réinitialisation.'
  }
}
</script>

<template>
  <div class="flex justify-center items-center h-screen px-4">
    <form @submit.prevent="handleSubmit" class="bg-white/30 backdrop-blur-lg p-10 rounded-xl shadow-lg w-full max-w-md">
      <h2 class="text-2xl font-bold mb-6 text-center">Nouveau mot de passe</h2>

      <input
        v-model="password"
        type="password"
        placeholder="Nouveau mot de passe"
        class="w-full px-4 py-2 rounded-xl shadow bg-white placeholder-gray-500 focus:outline-none mb-3"
        required
      />
      <input
        v-model="confirmPassword"
        type="password"
        placeholder="Confirmer le mot de passe"
        class="w-full px-4 py-2 rounded-xl shadow bg-white placeholder-gray-500 focus:outline-none mb-4"
        required
      />

      <button
        type="submit"
        class="w-full bg-black text-white py-2 rounded-xl font-bold hover:bg-gray-800 transition"
      >
        Réinitialiser
      </button>

      <p v-if="success" class="text-green-600 text-center mt-4">{{ success }}</p>
      <p v-if="error" class="text-red-600 text-center mt-4">{{ error }}</p>
    </form>
  </div>
</template>
