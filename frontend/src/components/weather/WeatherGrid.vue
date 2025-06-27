<template>
  <div class="flex-1">
    <GridLayout 
      ref="gridLayoutRef" 
      :layout="layout" 
      :col-num="colNum" 
      :row-height="auto" 
      :is-draggable="true"
      :is-resizable="false" 
      :auto-size="true" 
      :use-css-transforms="false" 
      :vertical-compact="false"
      :width="containerWidth" 
      class="h-full gap-4 mt-0" 
      @layout-updated="$emit('update-layout', $event)"
    >
      <GridItem 
        v-for="item in layout" 
        :key="item.i" 
        :i="item.i" 
        :x="item.x" 
        :y="item.y" 
        :w="item.w" 
        :h="item.h"
      >
        <WeatherBlock
          :name="item.name"
          :current-day-data="currentDayData"
          :city-data="cityData"
          :forecast-data="forecastData"
          :selected-day-index="selectedDayIndex"
        />
      </GridItem>
    </GridLayout>
  </div>
</template>

<script setup>
import { GridLayout, GridItem } from 'vue3-grid-layout'
import WeatherBlock from './WeatherBlock.vue'

const props = defineProps({
  layout: Array,
  allBlocks: Array,
  currentDayData: Object,
  cityData: Object,
  forecastData: Array,
  selectedDayIndex: Number,
  colNum: Number,
  containerWidth: Number
})

const emit = defineEmits(['update:layout', 'update-layout'])
</script>

<style>
.vue-grid-placeholder {
  background-color: transparent !important;
  border: none !important;
  box-shadow: none !important;
  opacity: 1 !important;
}

.vue-grid-item {
  user-select: none; 
}

.vue-grid-layout {
  padding-top: 0 !important;
  margin-top: 0 !important;
}
</style>