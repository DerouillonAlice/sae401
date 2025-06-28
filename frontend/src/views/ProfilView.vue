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
            <label for="firstname" class="font-semibold">Nom</label>
            <input id="firstname" v-model="user.firstname" type="text"
              class="w-full mt-1 px-4 py-2 rounded-xl bg-white border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-300" />
          </div>

          <div>
            <label for="email" class="font-semibold">Email</label>
            <input id="email" v-model="user.email" type="email"
              class="w-full mt-1 px-4 py-2 rounded-xl bg-white border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-300" />
          </div>

          <div>
            <label for="uniteTemperature" class="font-semibold">Unité température</label>
            <select id="uniteTemperature" v-model="user.uniteTemperature"
              class="w-full mt-1 px-4 py-2 rounded-xl bg-white border border-gray-300 shadow-sm">
              <option value="Celsius">Celsius</option>
              <option value="Fahrenheit">Fahrenheit</option>
            </select>
          </div>

          <div>
            <label for="unitePression" class="font-semibold">Unité pression</label>
            <select id="unitePression" v-model="user.unitePression"
              class="w-full mt-1 px-4 py-2 rounded-xl bg-white border border-gray-300 shadow-sm">
              <option value="hPa">hPa</option>
              <option value="mmHg">mmHg</option>
            </select>
          </div>

          <div>
            <label for="uniteVent" class="font-semibold">Unité vent</label>
            <select id="uniteVent" v-model="user.uniteVent"
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


    <div class="flex flex-col gap-6">
      <div class="p-6 rounded-2xl max-w-4xl bg-white/60 backdrop-blur-md border shadow text-black">
        <h2 class="text-3xl font-bold mb-6 text-center">Modifier le mot de passe</h2>

        <form @submit.prevent="changePassword" class="space-y-4">
          <div>
            <label for="oldPassword" class="font-semibold">Ancien mot de passe</label>
            <input id="oldPassword" v-model="oldPassword" type="password"
              class="w-full mt-1 px-4 py-2 rounded-xl bg-white border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
              required />
          </div>
          <div>
            <label for="newPassword" class="font-semibold">Nouveau mot de passe</label>
            <input id="newPassword" v-model="newPassword" type="password"
              class="w-full mt-1 px-4 py-2 rounded-xl bg-white border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
              required />
          </div>
          <button type="submit"
            class="w-full bg-black text-white py-2 rounded-xl font-bold hover:bg-gray-800 transition">
            Modifier le mot de passe
          </button>
          <div v-if="passwordError" class="text-red-600 text-xs text-center bg-red-50 p-2 rounded">{{ passwordError }}
          </div>
          <div v-if="passwordSuccess" class="text-green-600 text-xs text-center bg-green-50 p-2 rounded">{{
            passwordSuccess }}</div>
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
          <label class="font-semibold">Types d'événements</label>
          <MultiSelect v-model="alertConfig.alertTypes" :options="alertTypes"
            placeholder="Sélectionnez les types d'événements..." />
        </div>

        <div>
          <label class="font-semibold">Villes surveillées</label>
          <MultiSelect v-if="userFavorites.length > 0" v-model="alertConfig.locations" :options="userFavorites"
            placeholder="Sélectionnez les villes à surveiller..." />
          <div v-else class="text-sm text-gray-500 italic bg-gray-50 p-3 rounded-xl mt-1">
            Aucune ville favorite. Ajoutez des favoris pour configurer les alertes.
          </div>
        </div>

        <div class="flex gap-2">
          <button @click="saveAlertConfig" :disabled="alertLoading"
            class="flex-1 bg-black text-white py-2 rounded-xl font-bold hover:bg-gray-800 transition disabled:opacity-50">
            {{ alertLoading ? 'Sauvegarde...' : 'Sauvegarder' }}
          </button>

          <button @click="handleTestAlert" :disabled="alertLoading"
            class="px-4 bg-blue-600 text-white py-2 rounded-xl font-bold hover:bg-blue-700 transition disabled:opacity-50"
            title="Tester le système d'alertes">
            🧪
          </button>
        </div>

        <div v-if="alertSuccess" class="text-green-600 text-xs text-center bg-green-50 p-2 rounded">{{ alertSuccess }}
        </div>
        <div v-if="alertError" class="text-red-600 text-xs text-center bg-red-50 p-2 rounded">{{ alertError }}</div>
      </div>
    </div>
  </div>
  </div>

  <AlertDisplay ref="alertDisplay" />
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AlertDisplay from '../components/AlertDisplay.vue'
import MultiSelect from '../components/MultiSelect.vue'
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
const alertDisplay = ref(null)

const {
  alertConfig,
  alertLoading,
  alertError,
  alertSuccess,
  userFavorites,
  alertTypes,
  saveAlertConfig,
  initAlerts,
  testAlertSystem
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

const handleTestAlert = async () => {
  const testAlert = await testAlertSystem()
  if (testAlert && alertDisplay.value) {
    alertDisplay.value.addAlert(testAlert)
  }
}
</script>
