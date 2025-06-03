<template>
  <div class="flex flex-col h-screen">
    <div class="flex flex-1 overflow-hidden">
      <!-- Main Area -->
      <main class="flex-1 overflow-auto sm:overflow-clip bg-gray-50 p-5">
        <div class="w-full flex flex-col gap-4">
          <!-- Filtres en ligne -->
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
          <div class="w-full flex flex-col lg:flex-row gap-4">
            <!-- Bloc Température à gauche -->
            <div
              class="lg:w-2/4 w-full h-[340px] bg-red-100 rounded-lg shadow flex items-center justify-center text-xl font-bold"
            >
              🌡️ Température
            </div>

            <!-- Grid météo à droite -->
            <div class="lg:w-2/4 w-full">
              <GridLayout
                :layout="layout"
                :col-num="colNum"
                :row-height="160"
                :is-draggable="true"
                :is-resizable="false"
                :auto-size="false"
                :use-css-transforms="false"
                :vertical-compact="false"
                :margin="[10, 10]"
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
                    class="h-full flex items-center justify-center bg-blue-100 rounded-lg shadow text-lg font-semibold"
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
import { ref, watch, onMounted } from 'vue';
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
  1: [{ i: '1', x: 0, y: 0, w: 3, h: 2 }],
  2: [
    { i: '1', x: 0, y: 0, w: 3, h: 1 },
    { i: '2', x: 0, y: 1, w: 3, h: 1 },
  ],
  3: [
    { i: '1', x: 0, y: 0, w: 1.5, h: 1 },
    { i: '2', x: 1.5, y: 0, w: 1.5, h: 1 },
    { i: '3', x: 0, y: 1, w: 3, h: 1 },
  ],
  4: [
    { i: '1', x: 0, y: 0, w: 1.5, h: 1 },
    { i: '2', x: 1.5, y: 0, w: 1.5, h: 1 },
    { i: '3', x: 0, y: 1, w: 1.5, h: 1 },
    { i: '4', x: 1.5, y: 1, w: 1.5, h: 1 },
  ],
  5: [
    { i: '1', x: 0, y: 0, w: 1, h: 1 },
    { i: '2', x: 1, y: 0, w: 1, h: 1 },
    { i: '3', x: 2, y: 0, w: 1, h: 1 },
    { i: '4', x: 0, y: 1, w: 2, h: 1 },
    { i: '5', x: 2, y: 1, w: 1, h: 1 },
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

function updateColNum() {
  const width = window.innerWidth;
  colNum.value = width < 640 ? 1 : 3;
}

onMounted(() => {
  updateColNum();
  window.addEventListener('resize', updateColNum);
});

watch(
  () => allBlocks.value.map((b) => b.active),
  () => {
    const activeBlocks = allBlocks.value.filter((b) => b.active);
    const isMobile = window.innerWidth < 640;

    if (isMobile) {
      layout.value = activeBlocks.map((block, index) => ({
        i: block.i,
        x: 0,
        y: index,
        w: 1,
        h: 1,
        name: block.name,
      }));
    } else {
      const config = predefinedLayouts[activeBlocks.length] || [];
      layout.value = config.map((l, index) => ({
        ...l,
        name: activeBlocks[index]?.name || 'Bloc',
      }));
    }
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
    const isMobile = window.innerWidth < 640;

    if (isMobile) {
      const newMobileLayout = newLayout.map((item) => ({
        ...item,
        x: 0,
        w: 1,
        h: 1,
      }));

      if (JSON.stringify(layout.value) !== JSON.stringify(newMobileLayout)) {
        layout.value = newMobileLayout;
      }

      isUpdating = false;
      return;
    }

    const oldLayout = layout.value.map((l) => ({ ...l }));
    const activeBlocks = allBlocks.value.filter((b) => b.active);
    const layoutDef = predefinedLayouts[activeBlocks.length] || [];

    const sortedItems = newLayout.slice().sort((a, b) => a.y - b.y || a.x - b.x);

    const correctedLayout = layoutDef.map((slot, index) => {
      const movedBlock = sortedItems[index];
      const original = oldLayout.find((l) => l.i === movedBlock.i);
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
</style>
