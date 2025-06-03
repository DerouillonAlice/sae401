<template>
    <div class=" p-4 shadow-md flex items-center justify-between rounded-2xl">
      <div class="flex items-center gap-2 w-full max-w-xl">
        <input
          v-model="searchQuery"
          @keyup.enter="rechercherVille"
          type="text"
          placeholder="Rechercher une ville ..."
          class="flex-1 rounded-full px-4 py-2 border border-gray-300 focus:outline-none shadow-inner"
        />
        <button
          @click="rechercherVille"
          class="bg-white hover:bg-gray-100 text-gray-600 p-2 rounded-full shadow-md"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
          </svg>
        </button>
      </div>
  
      <button
        @click="goToProfile"
        class="ml-4 p-2 bg-white hover:bg-gray-100 rounded-full shadow-md"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.657 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
      </button>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  import { useRouter } from 'vue-router'
  import axios from 'axios'
  
  const searchQuery = ref('')
  const router = useRouter()
  
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
  </script>
  
  <style scoped>
  input::placeholder {
    color: #999;
  }
  </style>
  