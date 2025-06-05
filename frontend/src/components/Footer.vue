<template>
  <footer class="w-full">
    <div class="px-6 py-2 border-t border-white/60 text-center text-sm text-gray-700">
      <div class="flex flex-col sm:flex-row justify-center items-center gap-2 sm:gap-4">
        <span>&copy; {{ new Date().getFullYear() }} WeatherApp</span>
        <span class="hidden sm:inline">•</span>
        <router-link to="/contact" class="hover:underline hover:text-black transition">Contact</router-link>
        <span class="hidden sm:inline">•</span>
        <router-link to="/mentions-legales" class="hover:underline hover:text-black transition">Mentions légales</router-link>
      </div>
    </div>
  </footer>
</template>
