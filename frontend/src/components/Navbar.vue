<template>
  <div class="p-4 flex items-center gap-4 w-full max-w-7xl mx-auto">
    <div class="flex-1"></div>

    <div class="flex items-center gap-2 max-w-xl w-full mx-auto">
      <div class="relative flex-1">
        <input
          v-model="searchQuery"
          @keyup.enter="rechercherVille"
          type="text"
          placeholder="Rechercher une ville ..."
          class="w-full rounded-full px-8 py-2 pr-12 bg-white/30 backdrop-blur-md shadow-lg border border-white/60 focus:border-indigo-100 focus:ring-1  focus:bg-white/40 transition-all duration-300 ease-in-out outline-none"
        />
        <button
          @click="rechercherVille"
          type="button"
          class="absolute right-4 top-1/2 -translate-y-1/2 bg-transparent p-0 m-0"
          tabindex="-1"
        >
          <MagnifyingGlassIcon class="w-5 h-5" />
        </button>
      </div>
    </div>

    <div class="flex-1 flex justify-end">
      <button
        v-if="auth.isConnected"
        @click="goToProfile"
        class="p-2 rounded-full bg-white/30 backdrop-blur-md shadow-lg border border-white/70  hover:bg-white/40 transition-all duration-300 ease-in-out outline-none"
      >
        <UserIcon class="w-6 h-6" />
      </button>
      <button
        v-else
        @click="goToRegister"
        class="px-4 py-2 rounded-full bg-blue-500 text-white font-semibold shadow hover:bg-blue-600 transition-all duration-300"
      >
        Inscription
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'
import { UserIcon } from '@heroicons/vue/24/solid'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'

const searchQuery = ref('')
const router = useRouter()
const auth = useAuthStore()

onMounted(() => {
  auth.checkAuth()
})

const rechercherVille = async () => {
  if (!searchQuery.value.trim()) return
  try {
    const response = await axios.get(`api/search/${searchQuery.value}`)
    console.log('Résultats de recherche :', response.data)
  } catch (error) {
    console.error('Erreur lors de la recherche de ville :', error)
  }
}

const goToProfile = () => {
  router.push({ name: 'ProfilView' })
}

const goToRegister = () => {
  router.push({ path: '/register' })
}
</script>

<style scoped>
input::placeholder {
  color: #999;
}
</style>
