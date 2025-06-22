<template>
  <div class=" flex items-center justify-center  px-2">
    <div class="w-full p-8 border rounded-2xl bg-white/40 border-white/60 backdrop-blur-md">
      <h1 class="text-3xl font-extrabold text-center mb-6 ">Contact</h1>
      <form class="space-y-5">
        <div>
          <label class="block font-semibold mb-1 text-gray-700" for="name">Nom</label>
          <input
            id="name"
            type="text"
            class="w-full px-4 py-2 rounded-xl bg-white border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
            placeholder="Votre nom"
            required
          />
        </div>
        <div>
          <label class="block font-semibold mb-1 text-gray-700" for="email">Email</label>
          <input
            id="email"
            type="email"
            class="w-full px-4 py-2 rounded-xl bg-white border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
            placeholder="Votre email"
            required
          />
        </div>
        <div>
          <label class="block font-semibold mb-1 text-gray-700" for="message">Message</label>
          <textarea
            id="message"
            rows="4"
            class="w-full px-4 py-2 rounded-xl bg-white border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-300 resize-none"
            placeholder="Votre message"
            required
          ></textarea>
        </div>
        <button
          type="submit"
          class="w-full bg-black text-white py-2 rounded-xl font-bold hover:bg-gray-800 transition"
        >
          Envoyer
        </button>
        <div v-if="successMsg" class="text-green-600 mt-3 text-sm text-center">
          {{ successMsg }}
        </div>
        <div v-if="errorMsg" class="text-red-600 mt-3 text-sm text-center">
          {{ errorMsg }}
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const successMsg = ref('')
const errorMsg = ref('')

</script>