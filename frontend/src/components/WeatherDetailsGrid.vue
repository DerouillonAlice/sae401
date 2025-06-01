<template>
  <section>
    <!-- Titre ou autre header ici si besoin -->
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
  </section>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { GridLayout, GridItem } from 'vue3-grid-layout';

const allBlocks = ref([
  { i: '1', name: 'Vent', active: true },
  { i: '2', name: 'Pression', active: true },
  { i: '3', name: 'Humidité', active: true },
  { i: '4', name: 'Visibilité', active: true },
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

function updateLayout(newLayout) {
  const isMobile = window.innerWidth < 640;

  if (isMobile) {
    layout.value = newLayout.map((item) => ({
      ...item,
      x: 0,
      w: 1,
      h: 1,
    }));
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

  layout.value = correctedLayout;
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