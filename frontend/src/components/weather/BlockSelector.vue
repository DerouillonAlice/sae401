<template>
  <div class="w-full flex flex-wrap justify-evenly gap-y-4 py-4 px-2 rounded-xl">
    <label 
      v-for="block in blocks" 
      :key="block.i"
      class="flex items-center gap-2 text-sm font-medium cursor-pointer text-black"
    >
      <input 
        type="checkbox" 
        v-model="block.active" 
        class="peer hidden" 
      />
      <span
        class="w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:bg-black peer-checked:border-white transition-all duration-200 shadow-sm"
      ></span>
      <span class="whitespace-nowrap">{{ block.name }}</span>
    </label>
  </div>
</template>

<script setup>
const props = defineProps({
  blocks: {
    type: Array,
    required: true
  }
})

const emit = defineEmits(['update:blocks'])

function toggleBlock(block) {
  const updatedBlocks = props.blocks.map(b => 
    b.i === block.i ? { ...b, active: !b.active } : b
  )
  emit('update:blocks', updatedBlocks)
}
</script>