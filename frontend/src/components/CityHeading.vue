<template>
  <section class="flex items-center justify-between w-full h-16 bg-white/40 backdrop-blur-md rounded-xl shadow overflow-hidden border border-white/70 px-4">
    <h1 class="text-2xl font-bold">{{ villeAffichee }}</h1>
    <div class="flex items-center gap-2">
      <span v-if="errorMsg" class="text-red-600 text-sm mr-2">{{ errorMsg }}</span>
      <button
        v-if="auth.isConnected"
        @click="toggleFavorite"
        class=""
        :aria-label="isFavorite ? 'Retirer des favoris' : 'Ajouter aux favoris'"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="w-8 h-8 transition-transform duration-300 ease-in-out"
          :class="{ 'fill-red-500 text-red-500 scale-110': isFavorite, 'text-white/40 stroke-red-500': !isFavorite }"
          viewBox="0 0 24 24"
          stroke="currentColor"
          fill="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 21.364l-7.682-7.682a4.5 4.5 0 010-6.364z"
          />
        </svg>
      </button>
    </div>
  </section>
</template>

<script setup>
import { useRoute } from 'vue-router'
import { computed, ref } from 'vue'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const auth = useAuthStore()

function getDefaultVille() {
  if (auth.isConnected && auth.favorites.length > 0) {
    return auth.favorites[0].city
  }
  return 'Paris'
}

const villeAffichee = computed(() => route.query.ville || getDefaultVille())

const isFavorite = computed(() =>
  auth.favorites.some(fav => fav.city.toLowerCase() === villeAffichee.value.toLowerCase())
)

const errorMsg = ref('')
let errorTimeout = null

const toggleFavorite = async () => {
  errorMsg.value = ''
  if (isFavorite.value) {
    await auth.removeFavorite(villeAffichee.value)
  } else {
    if (auth.favorites.length >= 7) {
      errorMsg.value = "Vous ne pouvez pas avoir plus de 7 favoris."
      if (errorTimeout) clearTimeout(errorTimeout)
      errorTimeout = setTimeout(() => {
        errorMsg.value = ''
      }, 3000)
      return
    }
    await auth.addFavorite(villeAffichee.value)
  }
}
</script>