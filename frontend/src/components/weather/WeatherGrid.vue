<template>
  <div class="flex-1">
    <GridLayout 
      ref="gridLayoutRef" 
      :layout="layout" 
      :col-num="colNum" 
      :row-height="150" 
      :is-draggable="true"
      :is-resizable="false" 
      :auto-size="true" 
      :use-css-transforms="false" 
      :vertical-compact="false"
      :margin="[0, 0]"
      :container-padding="[0, 0]"
      :width="containerWidth" 
      class="h-full mt-0 weather-grid" 
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
        class="weather-grid-item"
      >
        <div class="p-2 h-full">
          <WeatherBlock
            :name="item.name"
            :current-day-data="currentDayData"
            :city-data="cityData"
            :forecast-data="forecastData"
            :selected-day-index="selectedDayIndex"
            class="h-full w-full"
          />
        </div>
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
/* Supprimer tous les paddings/margins par défaut */
.vue-grid-layout {
  padding: 0 !important;
  margin: 0 !important;
}

.vue-grid-item {
  user-select: none;
  padding: 0 !important;
  margin: 0 !important;
}

/* Créer un gap avec une div interne */
.weather-grid-item > div {
  box-sizing: border-box;
}

/* Placeholder amélioré */
.vue-grid-placeholder {
  background-color: rgba(59, 130, 246, 0.1) !important;
  border: 2px dashed rgba(59, 130, 246, 0.3) !important;
  border-radius: 12px !important;
  box-shadow: none !important;
  opacity: 1 !important;
  margin: 8px !important;
}

/* Supprimer les handles de resize */
.vue-grid-item > .vue-resizable-handle {
  display: none !important;
}

/* Transitions pour le drag */
.vue-grid-item.vue-grid-item-dragging {
  transition: none !important;
  z-index: 100 !important;
}

.vue-grid-item.vue-grid-item-moving {
  transition: transform 0.2s ease !important;
}
</style>