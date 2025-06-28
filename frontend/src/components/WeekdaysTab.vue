<script setup lang="ts">
import { ref, computed } from 'vue'

const days = ['Aujourd\'hui', 'Demain', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim']

const selected = ref(0)

const emit = defineEmits(['update:modelValue'])

const selectDay = (index: number) => {
  selected.value = index
  emit('update:modelValue', index)
}

const dynamicDays = computed(() => {
  const today = new Date()
  const result = ['Aujourd\'hui']
  
  for (let i = 1; i < 5; i++) {
    const date = new Date(today)
    date.setDate(today.getDate() + i)
    const dayName = date.toLocaleDateString('fr-FR', { weekday: 'short' })
    result.push(dayName.charAt(0).toUpperCase() + dayName.slice(1))
  }
  
  return result
})
</script>

<template>
  <section class="flex w-full h-fit bg-white/40 backdrop-blur-md rounded-xl shadow overflow-hidden border border-white/70">
    <button
      v-for="(day, index) in dynamicDays"
      :key="index"
      @click="selectDay(index)"
      class="flex-1 py-2 h-16 text-sm font-medium text-center transition-colors duration-200"
      :class="{
        'bg-white/60 text-gray-900': selected === index,
        'text-gray-700 hover:bg-white/20': selected !== index
      }"
    >
      {{ day }}
    </button>
  </section>
</template>