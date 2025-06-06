<template>
  <section class="flex items-center justify-center w-full h-16 bg-white/40 backdrop-blur-md rounded-md shadow overflow-hidden backdrop-blur-md shadow-xl border border-white/70">
    <h1 class="text-2xl font-bold">{{ villeAffichee }}</h1>
  </section>
</template>

<script setup>
import { useRoute } from 'vue-router'
import { computed } from 'vue'

const route = useRoute()
const villeAffichee = computed(() => route.query.ville || 'Paris')
</script>