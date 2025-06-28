<template>
  <div class="flex-1">
    <GridLayout 
      v-if="!isMobile"
      ref="gridLayoutRef" 
      :layout="layout" 
      :col-num="colNum" 
      :row-height="150" 
      :is-draggable="true"
      :is-resizable="false" 
      :auto-size="true" 
      :use-css-transforms="true" 
      :vertical-compact="true"
      :margin="[8, 8]"
      :container-padding="[0, 0]"
      :width="containerWidth" 
      class="weather-grid"
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
        <div class="grid-item-content">
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

    <div v-else class="mobile-weather-grid">
      <div 
        v-for="item in layout" 
        :key="item.i"
        class="mobile-weather-item"
      >
        <WeatherBlock
          :name="item.name"
          :current-day-data="currentDayData"
          :city-data="cityData"
          :forecast-data="forecastData"
          :selected-day-index="selectedDayIndex"
          class="h-full w-full"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
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

const isMobile = computed(() => props.colNum === 1)
</script>

<style>
.vue-grid-layout {
  padding: 0 !important;
  margin: 0 !important;
}

.vue-grid-item {
  user-select: none;
  padding: 0 !important;
  margin: 0 !important;
  touch-action: none;
}

.weather-grid {
  min-height: auto !important;
  height: auto !important;
  max-height: none !important;
  overflow: visible !important;
}

.grid-item-content {
  padding: 8px;
  height: 100%;
  box-sizing: border-box;
}

.weather-grid-item > div {
  box-sizing: border-box;
}

.vue-grid-placeholder {
  background-color: rgba(59, 130, 246, 0.1) !important;
  border: 2px dashed rgba(59, 130, 246, 0.3) !important;
  border-radius: 12px !important;
  box-shadow: none !important;
  opacity: 1 !important;
}

.vue-grid-item > .vue-resizable-handle {
  display: none !important;
}

.vue-grid-item.vue-grid-item-dragging {
  transition: none !important;
  z-index: 100 !important;
}

.vue-grid-item.vue-grid-item-moving {
  transition: transform 0.2s ease !important;
}

/* Styles pour la version mobile */
.mobile-weather-grid {
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding: 8px;
  width: 100%;
  box-sizing: border-box;
}

.mobile-weather-item {
  width: 100%;
  height: 120px;
  box-sizing: border-box;
  padding: 4px;
}
</style>