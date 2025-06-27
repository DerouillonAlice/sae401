<template>
  <div class="w-full justify-center mx-auto p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="p-6 rounded-2xl max-w-4xl bg-white/60 backdrop-blur-md border shadow text-black">
      <h2 class="text-3xl font-bold mb-6 text-center">Mon profil</h2>

      <div v-if="loading" class="text-center text-gray-600">Chargement...</div>

      <div v-else-if="error" class="text-center text-red-600 font-semibold">
        {{ error }}
      </div>

      <div v-else>
        <form @submit.prevent="updateProfile" class="space-y-4">
          <div>
            <label class="font-semibold">Nom</label>
            <input v-model="user.firstname" type="text"
              class="w-full mt-1 px-4 py-2 rounded-xl bg-white border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-300" />
          </div>

          <div>
            <label class="font-semibold">Email</label>
            <input v-model="user.email" type="email"
              class="w-full mt-1 px-4 py-2 rounded-xl bg-white border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-300" />
          </div>

          <div>
            <label class="font-semibold">Unité température</label>
            <select v-model="user.uniteTemperature"
              class="w-full mt-1 px-4 py-2 rounded-xl bg-white border border-gray-300 shadow-sm">
              <option value="Celsius">Celsius</option>
              <option value="Fahrenheit">Fahrenheit</option>
            </select>
          </div>

          <div>
            <label class="font-semibold">Unité pression</label>
            <select v-model="user.unitePression"
              class="w-full mt-1 px-4 py-2 rounded-xl bg-white border border-gray-300 shadow-sm">
              <option value="hPa">hPa</option>
              <option value="mmHg">mmHg</option>
            </select>
          </div>

          <div>
            <label class="font-semibold">Unité vent</label>
            <select v-model="user.uniteVent"
              class="w-full mt-1 px-4 py-2 rounded-xl bg-white border border-gray-300 shadow-sm">
              <option value="km/h">km/h</option>
              <option value="m/s">m/s</option>
            </select>
          </div>

          <button type="submit"
            class="w-full bg-black text-white py-2 rounded-xl font-bold hover:bg-gray-800 transition">
            Mettre à jour
          </button>
          <div v-if="success" class="text-green-600 text-xs text-center bg-green-50 p-2 rounded">{{ success }}</div>
          <div v-if="error" class="text-red-600 text-xs text-center bg-red-50 p-2 rounded">{{ error }}</div>
        </form>
      </div>
    </div>

    <!-- Carte mot de passe existante -->
    <div class="p-6 rounded-2xl max-w-4xl bg-white/60 backdrop-blur-md border shadow text-black">
      <h2 class="text-3xl font-bold mb-6 text-center">Modifier le mot de passe</h2>

      <form @submit.prevent="changePassword" class="space-y-4">
        <div>
          <label class="font-semibold">Ancien mot de passe</label>
          <input v-model="oldPassword" type="password"
            class="w-full mt-1 px-4 py-2 rounded-xl bg-white border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
            required />
        </div>
        <div>
          <label class="font-semibold">Nouveau mot de passe</label>
          <input v-model="newPassword" type="password"
            class="w-full mt-1 px-4 py-2 rounded-xl bg-white border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
            required />
        </div>
        <button type="submit" class="w-full bg-black text-white py-2 rounded-xl font-bold hover:bg-gray-800 transition">
          Modifier le mot de passe
        </button>
        <div v-if="passwordError" class="text-red-600 text-xs text-center bg-red-50 p-2 rounded">{{ passwordError }}</div>
        <div v-if="passwordSuccess" class="text-green-600 text-xs text-center bg-green-50 p-2 rounded">{{ passwordSuccess }}</div>
      </form>
    </div>

    <div class="p-6 rounded-2xl max-w-4xl bg-white/60 backdrop-blur-md border shadow text-black">
      <h2 class="text-2xl font-bold mb-4 text-center">Alertes météo</h2>

      <div v-if="alertLoading" class="text-center text-gray-600">Chargement...</div>

      <div v-else class="space-y-4">
        <div>
          <label class="font-semibold">Gravité minimum</label>
          <select v-model="alertConfig.severity"
            class="w-full mt-1 px-4 py-2 rounded-xl bg-white border border-gray-300 shadow-sm">
            <option value="minor">Mineur</option>
            <option value="moderate">Modéré</option>
            <option value="severe">Sévère</option>
            <option value="extreme">Extrême</option>
          </select>
        </div>

        <div>
          <label class="font-semibold text-sm mb-2 block">Types d'événements</label>
          <div class="grid grid-cols-3 gap-x-3 gap-y-2 text-xs">
            <label v-for="type in alertTypes" :key="type.value"
              class="flex items-center cursor-pointer hover:bg-gray-50 p-1 rounded">
              <input :checked="alertConfig.alertTypes.includes(type.value)" type="checkbox"
                class="mr-2 h-3 w-3 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                @change="toggleAlertType(type.value)" />
              <span class="text-xs">{{ type.label }}</span>
            </label>
          </div>
        </div>

        <div>
          <label class="font-semibold text-sm mb-2 block">Villes surveillées</label>
          <div v-if="userFavorites.length > 0" class="grid grid-cols-2 gap-x-3 gap-y-1 text-xs">
            <label v-for="favorite in userFavorites" :key="favorite.id"
              class="flex items-center cursor-pointer hover:bg-gray-50 p-1 rounded">
              <input :checked="alertConfig.locations.includes(favorite.city)" type="checkbox"
                class="mr-2 h-3 w-3 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                @change="toggleFavoriteLocation(favorite.city)" />
              <span class="truncate">{{ favorite.city }}</span>
            </label>
          </div>
          <div v-else class="text-xs text-gray-500 italic bg-gray-50 p-2 rounded">
            Ajoutez des favoris pour les surveiller
          </div>
        </div>

        <button @click="saveAlertConfig" :disabled="alertLoading"
          class="w-full bg-black text-white py-2 px-4 rounded-xl text-sm font-semibold hover:bg-gray-800 transition disabled:opacity-50 mt-4">
          {{ alertLoading ? 'Sauvegarde...' : 'Sauvegarder' }}
        </button>

        <div v-if="alertSuccess" class="text-green-600 text-xs text-center bg-green-50 p-2 rounded">{{ alertSuccess }}</div>
        <div v-if="alertError" class="text-red-600 text-xs text-center bg-red-50 p-2 rounded">{{ alertError }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import {
  getProfile,
  updateProfile as apiUpdateProfile,
  changePassword as apiChangePassword
} from '../services/services'
import { useAlert } from '../composables/useAlert'

const user = ref({})
const loading = ref(true)
const error = ref('')
const success = ref('')
const oldPassword = ref('')
const newPassword = ref('')
const passwordError = ref('')
const passwordSuccess = ref('')

const {
  alertConfig,
  alertLoading,
  alertError,
  alertSuccess,
  userFavorites,
  alertTypes,
  saveAlertConfig,
  toggleAlertType,
  toggleFavoriteLocation,
  initAlerts
} = useAlert()

onMounted(async () => {
  try {
    const response = await getProfile()
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

  await initAlerts()
})

const updateProfile = async () => {
  success.value = ''
  error.value = ''
  try {
    await apiUpdateProfile(user.value)
    success.value = 'Profil mis à jour avec succès !'
    setTimeout(() => { success.value = '' }, 3000)
  } catch (e) {
    console.error('Erreur lors de la mise à jour du profil :', e)
    error.value = "Impossible de mettre à jour le profil"
    setTimeout(() => { error.value = '' }, 3000)
  }
}

const changePassword = async () => {
  passwordError.value = ''
  passwordSuccess.value = ''
  try {
    await apiChangePassword(oldPassword.value, newPassword.value)
    passwordSuccess.value = 'Mot de passe modifié avec succès !'
    oldPassword.value = ''
    newPassword.value = ''
    setTimeout(() => { passwordSuccess.value = '' }, 3000)
  } catch (e) {
    console.error('Erreur lors de la modification du mot de passe :', e)
    passwordError.value = e.response?.data?.message || "Impossible de modifier le mot de passe"
    setTimeout(() => { passwordError.value = '' }, 3000)
  }
}
</script>
