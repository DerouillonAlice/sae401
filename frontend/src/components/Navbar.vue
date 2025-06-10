<template>
  <div class="p-4 pl-14 flex items-center justify-between gap-4 w-full mx-auto">

    <div class="flex items-center">
      <router-link
        to="/"
        class="p-2 rounded-full bg-white/30 backdrop-blur-md shadow-lg border border-white/70 hover:bg-white/40 transition-all duration-300 ease-in-out outline-none"
      >
        <HomeIcon class="w-6 h-6" />
      </router-link>
    </div>

    <div class="flex items-center gap-2 max-w-xl w-full mx-auto">
      <div class="relative flex-1">
        <input
          v-model="searchQuery"
          @input="onSearchInput"
          type="text"
          placeholder="Rechercher une ville ..."
          class="w-full rounded-full px-8 py-2 pr-12 bg-white/30 backdrop-blur-md shadow-lg border border-white/60 focus:border-indigo-100 focus:ring-1 focus:bg-white/40 transition-all duration-300 ease-in-out outline-none"
        />
        <button
          @click="rechercherVille"
          type="button"
          class="absolute right-4 top-1/2 -translate-y-1/2 bg-transparent p-0 m-0"
          tabindex="-1"
        >
          <MagnifyingGlassIcon class="w-5 h-5" />
        </button>

        <ul
          v-if="searchResults.length"
          class="absolute left-0 right-0 mt-2 bg-white/90 backdrop-blur-md shadow-lg rounded-lg border border-gray-300 z-10 max-h-40 overflow-y-auto"
        >
          <li
            v-for="(result, index) in searchResults"
            :key="index"
            @click="selectResult(result)"
            class="px-4 py-2 hover:bg-gray-200 cursor-pointer flex justify-between items-center"
          >
            <span>{{ result.name }}</span>
            <span v-if="result.country" class="text-sm text-gray-500">({{ result.country }})</span>
          </li>
        </ul>
      </div>
    </div>

    <div class="flex items-center gap-2">
      <button
        v-if="auth.isConnected"
        @click="goToProfile"
        class="p-2 rounded-full bg-white/30 backdrop-blur-md shadow-lg border border-white/70 hover:bg-white/40 transition-all duration-300 ease-in-out outline-none"
      >
        <UserIcon class="w-6 h-6" />
      </button>
      <button
        v-if="auth.isConnected"
        @click="logout"
        class="p-2 rounded-full bg-white/30 backdrop-blur-md shadow-lg border border-white/70 hover:bg-white/40 transition-all duration-300 ease-in-out outline-none"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1" />
        </svg>
      </button>
      <template v-if="!auth.isConnected">
        <button
          @click="goToLogin"
          class="p-2 rounded-full bg-white/30 backdrop-blur-md shadow-lg border border-white/70 hover:bg-white/40 transition-all duration-300 ease-in-out outline-none"
        >
          Connexion
        </button>
        <button
          @click="goToRegister"
          class="p-2 rounded-full bg-white/30 backdrop-blur-md shadow-lg border border-white/70 hover:bg-white/40 transition-all duration-300 ease-in-out outline-none"
        >
          Inscription
        </button>
      </template>
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
import { HomeIcon } from '@heroicons/vue/24/solid'

const searchQuery = ref('')
const searchResults = ref([]) 
const isSearching = ref(false) 
const router = useRouter()
const auth = useAuthStore()

onMounted(() => {
  if (localStorage.getItem('token')) {
    auth.checkAuth()
  }
})

const onSearchInput = async () => {
  if (searchQuery.value.length < 2) {
    searchResults.value = []
    return
  }
  isSearching.value = true
  try {
    const response = await axios.get(`/api/search/${encodeURIComponent(searchQuery.value)}`)
    searchResults.value = response.data.filter((result, index, self) =>
      index === self.findIndex((r) => r.name === result.name)
    )
  } catch (error) {
    console.error('Erreur lors de la recherche de ville :', error)
    searchResults.value = []
  } finally {
    isSearching.value = false
  }
}

const rechercherVille = async () => {
  if (!searchQuery.value.trim()) return
  await onSearchInput()
}

const selectResult = (result) => {
  router.push({
    name: 'HomeView',
    query: {
      ville: result.name,
      t: Date.now() 
    }
  })
  searchResults.value = [] 
  searchQuery.value = ''
}

const goToProfile = () => {
  router.push({ name: 'ProfilView' })
}

const goToRegister = () => {
  router.push({ path: '/inscription' })
}

const goToLogin = () => {
  router.push({ path: '/connexion' })
}

const logout = () => {
  localStorage.removeItem('token')
  auth.isConnected = false
  auth.user = null
  router.push('/')
}
</script>

<style scoped>
input::placeholder {
  color: #999;
}
</style>
