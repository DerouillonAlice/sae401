<template>
  <div class="flex flex-col h-screen">
    <div class="flex flex-1 overflow-hidden">
      <main class="flex-1 overflow-auto sm:overflow-clip bg-gray-50 p-5">
        <div class="w-full flex flex-col gap-4 max-w-screen-xl mx-auto">
          <!-- Filtres -->
          <div class="mb-4 flex flex-wrap items-center gap-4">
            <label
              v-for="block in allBlocks"
              :key="block.i"
              class="flex items-center gap-2 text-sm bg-white px-3 py-2 rounded shadow border"
            >
              <input type="checkbox" v-model="block.active" class="accent-blue-500" />
              {{ block.name }}
            </label>
          </div>

          <!-- Température + Grid côte à côte -->
          <div class="flex flex-col lg:flex-row gap-4 items-start">
            <!-- Bloc Température -->
            <div
              class="flex-1 bg-red-100 rounded-lg shadow flex items-center justify-center text-xl font-bold"
              :style="{
                height: gridHeight ? `${gridHeight}px` : '420px',
                transition: 'height 0.3s ease'
              }"
            >
              🌡️ Température
            </div>

            <!-- Grid météo -->
            <div ref="gridContainerRef" class="flex-1">
              <GridLayout
                ref="gridLayoutRef"
                :layout="layout"
                :col-num="colNum"
                :row-height="200"
                :is-draggable="true"
                :is-resizable="false"
                :auto-size="false"
                :use-css-transforms="false"
                :vertical-compact="false"
                :margin="[10, 10]"
                :width="containerWidth"
                class="h-auto mt-0"
                @layout-updated="updateLayout"
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
                  <div
                    class="h-full w-full flex items-center justify-center bg-blue-100 rounded-lg shadow text-lg font-semibold"
                  >
                    {{ item.name }}
                  </div>
                </GridItem>
              </GridLayout>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, nextTick } from 'vue';
import { GridLayout, GridItem } from 'vue3-grid-layout';

const allBlocks = ref([
  { i: '1', name: 'Vent', active: true },
  { i: '2', name: 'Pression', active: true },
  { i: '3', name: 'Humidité', active: true },
  { i: '4', name: 'Visibilité', active: true },
  { i: '5', name: 'Nuages', active: true },
  { i: '6', name: 'Rosée', active: true },
]);

const predefinedLayouts = {
  1: [{ i: '1', x: 0, y: 0, w: 1, h: 1 }],
  2: [
    { i: '1', x: 0, y: 0, w: 1, h: 1 },
    { i: '2', x: 1, y: 0, w: 1, h: 1 },
  ],
  3: [
    { i: '1', x: 0, y: 0, w: 1, h: 1 },
    { i: '2', x: 1, y: 0, w: 1, h: 1 },
    { i: '3', x: 0, y: 1, w: 1, h: 1 },
  ],
  4: [
    { i: '1', x: 0, y: 0, w: 1, h: 1 },
    { i: '2', x: 1, y: 0, w: 1, h: 1 },
    { i: '3', x: 0, y: 1, w: 1, h: 1 },
    { i: '4', x: 1, y: 1, w: 1, h: 1 },
  ],
  5: [
    { i: '1', x: 0, y: 0, w: 1, h: 1 },
    { i: '2', x: 1, y: 0, w: 1, h: 1 },
    { i: '3', x: 2, y: 0, w: 1, h: 1 },
    { i: '4', x: 0, y: 1, w: 1, h: 1 },
    { i: '5', x: 1, y: 1, w: 1, h: 1 },
  ],
  6: [
    { i: '1', x: 0, y: 0, w: 1, h: 1 },
    { i: '2', x: 1, y: 0, w: 1, h: 1 },
    { i: '3', x: 2, y: 0, w: 1, h: 1 },
    { i: '4', x: 0, y: 1, w: 1, h: 1 },
    { i: '5', x: 1, y: 1, w: 1, h: 1 },
    { i: '6', x: 2, y: 1, w: 1, h: 1 },
  ],
};

const layout = ref([]);
const colNum = ref(3);
const containerWidth = ref(600);
const gridHeight = ref(0);
const gridContainerRef = ref(null);

function updateLayoutDimensions() {
  if (gridContainerRef.value) {
    containerWidth.value = gridContainerRef.value.offsetWidth;
    colNum.value = Math.floor(containerWidth.value / 180) || 1;

    // calcul de la hauteur de la grille
    nextTick(() => {
      const grid = gridContainerRef.value.querySelector('.vue-grid-layout');
      if (grid) {
        gridHeight.value = grid.offsetHeight;
      }
    });
  }
}

onMounted(() => {
  updateLayoutDimensions();
  window.addEventListener('resize', updateLayoutDimensions);
  nextTick(updateLayoutDimensions);
});

watch(
  () => allBlocks.value.map((b) => b.active),
  () => {
    const activeBlocks = allBlocks.value.filter((b) => b.active);
    const config = predefinedLayouts[activeBlocks.length] || [];
    layout.value = config.map((l, index) => ({
      ...l,
      name: activeBlocks[index]?.name || 'Bloc',
    }));

    nextTick(updateLayoutDimensions);
  },
  { immediate: true }
);

let isUpdating = false;
let updateTimeout;

function updateLayout(newLayout) {
  if (isUpdating) return;

  isUpdating = true;
  clearTimeout(updateTimeout);

  updateTimeout = setTimeout(() => {
    const activeBlocks = allBlocks.value.filter((b) => b.active);
    const layoutDef = predefinedLayouts[activeBlocks.length] || [];
    const sortedItems = newLayout.slice().sort((a, b) => a.y - b.y || a.x - b.x);
    const correctedLayout = layoutDef.map((slot, index) => {
      const movedBlock = sortedItems[index];
      const original = layout.value.find((l) => l.i === movedBlock.i);
      return {
        ...slot,
        i: movedBlock.i,
        name: original?.name || 'Bloc',
      };
    });

    if (JSON.stringify(layout.value) !== JSON.stringify(correctedLayout)) {
      layout.value = correctedLayout;
    }

    isUpdating = false;
    nextTick(updateLayoutDimensions);
  }, 50);
}
</script>

<style scoped>
.vue-grid-item.vue-grid-placeholder {
  opacity: 0 !important;
}
.vue-grid-item {
  transition: all 0.3s ease;
}
.vue-grid-layout {
  padding-top: 0 !important;
  margin-top: 0 !important;
}
</style>
