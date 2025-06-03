<template>
  <div id="app" class="min-h-screen flex flex-col">
    <Navbar />
    <div class="flex flex-1">
      <Sidebar class="w-64 h-screen flex-shrink-0" />
      <main class="flex-1">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import Navbar from './components/Navbar.vue'
import Sidebar from './components/Sidebar.vue'
</script>
