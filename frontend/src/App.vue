<script setup>
import Navbar from './components/Navbar.vue'
import Footer from './components/Footer.vue'
import Sidebar from './components/Sidebar.vue'
import fond from './assets/fond.png'
import { useSidebar } from './composables/useSidebar'

const { isSidebarOpen } = useSidebar()
</script>

<template>
  <div
    id="app"
    class="h-screen flex bg-cover bg-center"
    :style="{ backgroundImage: `url('${fond}')` }"
  >
    <Sidebar
      class="transition-all duration-300 h-screen flex-shrink-0"
      :class="isSidebarOpen ? 'w-64' : 'w-12'"
    />

    <div class="flex flex-col flex-1 min-w-0 h-screen">
      <Navbar />
      <main class="flex-1 overflow-y-auto p-6">
        <router-view />
      <Footer class="fixed bottom-0 py-4" />
      </main>
    </div>
  </div>
</template>
