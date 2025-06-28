<script setup>
import { ref } from 'vue'
import { requestResetPassword } from '@/services/services'

const email = ref('')
const success = ref('')
const error = ref('')

const handleSubmit = async () => {
  success.value = ''
  error.value = ''
  try {
    await requestResetPassword(email.value)
    success.value = 'Un lien de réinitialisation vous a été envoyé par email.'
  } catch (err) {
    error.value = err.response?.data?.error || 'Erreur lors de la demande.'
  }
}
</script>

<template>
  <div class="flex justify-center items-center h-screen px-4">
    <form @submit.prevent="handleSubmit" class="bg-white/30 backdrop-blur-lg p-10 rounded-xl shadow-lg w-full max-w-md">
      <h2 class="text-2xl font-bold mb-6 text-center">Mot de passe oublié</h2>
      <input
        v-model="email"
        type="email"
        placeholder="Votre adresse e-mail"
        class="w-full px-4 py-2 rounded-xl shadow bg-white placeholder-gray-500 focus:outline-none mb-4"
        required
      />
      <button
        type="submit"
        class="w-full bg-black text-white py-2 rounded-xl font-bold hover:bg-gray-800 transition"
      >
        Envoyer le lien
      </button>

      <p v-if="success" class="text-green-600 text-center mt-4">{{ success }}</p>
      <p v-if="error" class="text-red-600 text-center mt-4">{{ error }}</p>
    </form>
  </div>
</template>
