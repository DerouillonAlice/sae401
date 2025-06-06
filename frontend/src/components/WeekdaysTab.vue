<script setup lang="ts">
import { ref } from 'vue'

const days = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim']

const selected = ref(0)

const emit = defineEmits<{
  (e: 'update:modelValue', index: number): void
}>()

const selectDay = (index: number) => {
  selected.value = index
  emit('update:modelValue', index)
}
</script>

<template>
  <section class="flex w-full bg-white/40 backdrop-blur-md rounded-md shadow overflow-hidden backdrop-blur-md shadow-xl  border border-white/70">
    <button
      v-for="(day, index) in days"
      :key="day"
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