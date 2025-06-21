<script setup>
import CityCard from './CityCard.vue'
import { ref, computed, onMounted, watch } from 'vue'
import { useSidebar } from '@/composables/useSidebar'
import { useAuthStore } from '@/stores/auth'
import { ChevronLeftIcon } from '@heroicons/vue/24/outline'
import fond from '../assets/fond.png'
import { useRouter } from 'vue-router'
import {
  getWeatherByCity,
  searchCities,
  addFavorite as apiAddFavorite,
  removeFavorite as apiRemoveFavorite,
  fetchFavorites as apiFetchFavorites
} from '../services/services'
import { useWeatherImage } from '@/composables/useWeatherImage' 
const { isSidebarOpen } = useSidebar()
const auth = useAuthStore()
const router = useRouter()

const grandesVilles = [
  { name: 'Paris' },
  { name: 'Londres' },
  { name: 'New York' },
  { name: 'Tokyo' },
  { name: 'Sydney' }
]

const meteoVilles = ref({})
const meteoFavoris = ref({})

const fetchMeteo = async (ville) => {
  try { 
    const res = await getWeatherByCity(ville)
    meteoVilles.value = { ...meteoVilles.value, [ville]: res.data }
  } catch {
    meteoVilles.value = { ...meteoVilles.value, [ville]: { error: 'Erreur météo' } }
  }
}

const fetchMeteoFavori = async (ville) => {
  try {
    const res = await getWeatherByCity(ville)
    meteoFavoris.value[ville] = res.data
  } catch {
    meteoFavoris.value[ville] = { error: 'Erreur météo' }
  }
}

watch(
  () => auth.favorites,
  (newFavorites) => {
    newFavorites.forEach(fav => {
      if (!meteoFavoris.value[fav.city]) {
        fetchMeteoFavori(fav.city)
      }
    })
  },
  { immediate: true, deep: true }
)

watch(
  () => auth.isConnected,
  (connected) => {
    if (connected) {
      auth.fetchFavorites()
    } else {
      grandesVilles.forEach(ville => {
        if (!meteoVilles.value[ville.name]) {
          fetchMeteo(ville.name)
        }
      })
    }
  },
  { immediate: true }
)

const cities = computed(() => {
  return auth.isConnected
    ? auth.favorites.map(fav => {
        const meteo = meteoFavoris.value[fav.city]
        const { imageUrl } = useWeatherImage(ref(meteo))
        return {
          id: fav.id,
          name: fav.city,
          meteo,
          imageSrc: imageUrl.value 
        }
      })
    : grandesVilles.map(ville => {
        const meteo = meteoVilles.value[ville.name]
        const { imageUrl } = useWeatherImage(ref(meteo))
        return {
          name: ville.name,
          meteo,
          imageSrc: imageUrl.value 
        }
      })
})

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}

const showAddFavorite = ref(false)
const newFavoriteCity = ref('')
const addFavoriteError = ref('')
const addFavoriteSuccess = ref('')
const selectedCity = ref(null)
const searchResults = ref([])
const isSearching = ref(false)

function openAddFavoriteModal() {
  showAddFavorite.value = true
  newFavoriteCity.value = ''
  searchResults.value = []
  selectedCity.value = null
  addFavoriteError.value = ''
  addFavoriteSuccess.value = ''
}

async function submitAddFavorite() {
  addFavoriteError.value = ''
  addFavoriteSuccess.value = ''
  if (auth.favorites.length >= MAX_FAVORITES) { 
    addFavoriteError.value = "Vous ne pouvez pas avoir plus de 7 favoris."
    return
  }
  if (!selectedCity.value) {
    addFavoriteError.value = "Veuillez sélectionner une ville dans la liste."
    return
  }
  const alreadyExists = auth.favorites.some( 
    fav => fav.city.toLowerCase() === selectedCity.value.name.toLowerCase()
  )
  if (alreadyExists) {
    addFavoriteError.value = "Cette ville est déjà dans vos favoris."
    return
  }
  try {
    await auth.addFavorite(selectedCity.value.name) 
    addFavoriteSuccess.value = 'Favori ajouté !'
    showAddFavorite.value = false
  } catch (err) {
    addFavoriteError.value = err.response?.data?.error || 'Erreur lors de l\'ajout'
  }
}

watch(newFavoriteCity, async (val) => {
  if (!val || val.length < 2) {
    searchResults.value = []
    return
  }
  isSearching.value = true
  try {
    const res = await searchCities(val)
    searchResults.value = res.data
  } catch {
    searchResults.value = []
  }
  isSearching.value = false
})

const selectCity = (city) => {
  selectedCity.value = city
  newFavoriteCity.value = city.name
  searchResults.value = []
}

const removeFavorite = async (favoriteId) => {
  try {
    const favoriteToRemove = auth.favorites.find(fav => fav.id === favoriteId)
    if (favoriteToRemove) {
      await auth.removeFavorite(favoriteToRemove.city) 
    }
  } catch (err) {
    console.error('Erreur lors de la suppression du favori:', err)
  }
}

function goToCity(cityName) {
  router.push({ 
    name: 'HomeView', 
    query: { 
      ville: cityName, 
      t: Date.now()
    } 
  })
}

const MAX_FAVORITES = 7
const hasReachedFavoriteLimit = computed(() => auth.isConnected && auth.favorites.length >= MAX_FAVORITES) 
</script>

<template>
  <main class="flex flex-col justify-center py-4 h-full relative">
    <section
      class="absolute left-0 grid grid-cols-[1fr_auto] mx-auto gap-2 h-[95vh] w-full p-4 rounded-r-xl bg-white/40 backdrop-blur-md shadow-xl transition-transform duration-300 ease-in-out border border-white/70"
    >
      <div v-if="isSidebarOpen" class="flex flex-col gap-4">
        <TransitionGroup name="fade" tag="div" class="contents">
          <CityCard
            v-for="(city, index) in cities"
            :key="city.id || `${city.name}-${index}`"
            :name="city.name"
            :removable="auth.isConnected"
            @remove="removeFavorite(city.id)"
            @click="goToCity(city.name)"
          >
            <template #default>
              <div>
                <span v-if="city.meteo && city.meteo.main && city.meteo.weather">
                  {{ Math.round(city.meteo.main.temp) }}°C
                  <img
                    :src="city.imageSrc"
                    :alt="city.meteo?.weather?.[0]?.description || city.name"
                    class="w-8 h-8 inline-block align-middle ml-2"
                  />
                </span>
                <span v-else class="text-xs text-gray-400">Chargement...</span>
              </div>
            </template>
          </CityCard>
          <a href="/connexion">
          <CityCard
            v-if="!auth.isConnected"
            name="Connectez-vous pour ajouter vos favoris"
            imageSrc=""
            isPlaceholder
            customClass="connect-card"
          />
        </a>
          <CityCard
            v-if="auth.isConnected && !hasReachedFavoriteLimit"
            name="Ajouter une ville à vos favoris"
            isPlaceholder
            customClass="connect-card"
            @click="openAddFavoriteModal"
          />
        </TransitionGroup>
      </div>

      <div class="flex items-center justify-center">
        <button
          @click="toggleSidebar"
          class="rounded-full hover:bg-white/10 transition-colors duration-200 w-7 h-7 flex items-end justify-end"
        >
          <ChevronLeftIcon
            class="w-6 h-6 transition-transform duration-300"
            :class="{ 'rotate-180': !isSidebarOpen }"
          />
        </button>
      </div>
    </section>

    <transition name="fade">
  <div
    v-if="showAddFavorite"
    class="fixed inset-0 backdrop-blur-sm flex items-center justify-center z-50"
    
  >
    <div class=" backdrop-blur-lg bg-white/40 rounded-2xl shadow-xl  w-full max-w-sm text-gray-800"
    :style="{ backgroundImage: `url('${fond}')` }">
    <div class="overlay bg-white/50  inset-0 w-full h-full rounded-2xl p-6">

      <h3 class="text-xl font-semibold mb-4 text-center">Ajouter une ville à vos favoris</h3>

      <input
        v-model="newFavoriteCity"
        type="text"
        placeholder="Nom de la ville"
        class="w-full px-4 py-2 rounded-xl shadow bg-white placeholder-gray-500 focus:outline-none mb-2"
      />

      <ul
        v-if="searchResults.length"
        class="bg-white border rounded-xl shadow-inner max-h-40 overflow-y-auto mb-4"
      >
        <li
          v-for="city in searchResults"
          :key="city.id"
          @click="selectCity(city)"
          class="px-4 py-2 hover:bg-blue-100 cursor-pointer transition rounded"
        >
          {{ city.name }} <span v-if="city.country">({{ city.country }})</span>
        </li>
      </ul>

      <div class="flex gap-2 justify-end">
        <button
          @click="submitAddFavorite"
          class="bg-black text-white px-4 py-2 rounded-xl hover:bg-gray-800 transition font-semibold"
        >
          Ajouter
        </button>
        <button
          @click="showAddFavorite = false"
          class="bg-white px-4 py-2 rounded-xl shadow hover:bg-gray-100 transition"
        >
          Annuler
        </button>
      </div>

      <div v-if="addFavoriteError" class="text-red-600 mt-3 text-sm text-center">
        {{ addFavoriteError }}
      </div>
      <div v-if="addFavoriteSuccess" class="text-green-600 mt-3 text-sm text-center">
        {{ addFavoriteSuccess }}
      </div>
    </div>
  </div>
  </div>
</transition>

  </main>
</template>
<style scoped>

</style>
