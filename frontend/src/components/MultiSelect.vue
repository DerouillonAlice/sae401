<template>
  <div class="relative">
    <button 
      @click="toggleDropdown"
      type="button"
      class="w-full mt-1 px-4 py-2 rounded-xl bg-white border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-300 text-left flex justify-between items-center"
    >
      <span class="truncate">
        {{ selectedText }}
      </span>
      <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
      </svg>
    </button>

    <div 
      v-if="isOpen"
      class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-xl shadow-lg max-h-60 overflow-y-auto"
    >
      <div class="p-2">
        <label 
          v-for="option in options" 
          :key="option.value || option.id"
          class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer text-sm"
        >
          <input 
            :checked="isSelected(option)"
            type="checkbox"
            class="mr-3 h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
            @change="toggleOption(option)"
          />
          <span>{{ option.label || option.city }}</span>
        </label>
      </div>
      
      <div class="border-t border-gray-200 p-2 flex gap-2">
        <button 
          @click="selectAll"
          class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200"
        >
          Tout sélectionner
        </button>
        <button 
          @click="clearAll"
          class="text-xs px-2 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200"
        >
          Tout désélectionner
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  options: {
    type: Array,
    required: true
  },
  modelValue: {
    type: Array,
    default: () => []
  },
  placeholder: {
    type: String,
    default: 'Sélectionner...'
  }
})

const emit = defineEmits(['update:modelValue'])

const isOpen = ref(false)

const selectedText = computed(() => {
  if (props.modelValue.length === 0) {
    return props.placeholder
  }
  if (props.modelValue.length === 1) {
    const option = props.options.find(opt => 
      (opt.value && props.modelValue.includes(opt.value)) || 
      (opt.city && props.modelValue.includes(opt.city))
    )
    return option ? (option.label || option.city) : props.placeholder
  }
  return `${props.modelValue.length} sélectionné(s)`
})

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
}

const isSelected = (option) => {
  const value = option.value || option.city
  return props.modelValue.includes(value)
}

const toggleOption = (option) => {
  const value = option.value || option.city
  const newValue = [...props.modelValue]
  
  const index = newValue.indexOf(value)
  if (index > -1) {
    newValue.splice(index, 1)
  } else {
    newValue.push(value)
  }
  
  emit('update:modelValue', newValue)
}

const selectAll = () => {
  const allValues = props.options.map(option => option.value || option.city)
  emit('update:modelValue', allValues)
}

const clearAll = () => {
  emit('update:modelValue', [])
}

const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    isOpen.value = false
  }
}

if (typeof window !== 'undefined') {
  document.addEventListener('click', handleClickOutside)
}
</script>