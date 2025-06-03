import { createRouter, createWebHistory } from 'vue-router'

import HomeView from '@/views/HomeView.vue'
import ProfilView from '@/views/ProfilView.vue'

const routes = [
  {
    path: '/',
    name: 'HomeView',
    component: HomeView
  },
  {
    path: '/profil',
    name: 'ProfilView',
    component: ProfilView
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router