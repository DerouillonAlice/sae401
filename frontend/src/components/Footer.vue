<template>
  <footer class="w-full mt-8">
    <hr class="mb-2 border-gray-300" />
    <div class="flex flex-col sm:flex-row justify-center items-center gap-4 text-sm text-gray-600 py-2">
      <span>&copy; {{ new Date().getFullYear() }} WeatherApp</span>
      <span class="hidden sm:inline">|</span>
      <router-link to="/contact" class="hover:underline">Contact</router-link>
      <span class="hidden sm:inline">|</span>
      <router-link to="/mentions-legales" class="hover:underline">Mentions légales</router-link>
    </div>
  </footer>
</template>