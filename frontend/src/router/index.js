import { createRouter, createWebHistory } from 'vue-router'


const routes = [
  {
    path: '/',
    name: 'HomeView',
    component: () => import('@/views/HomeView.vue')
  },
  {
    path: '/profil',
    name: 'ProfilView',
    component: () => import('@/views/ProfilView.vue')
  },
  // {
  //   path: '/:pathMatch(.*)*',
  //   name: 'NotFound',
  //   component: () => import('@/views/NotFoundView.vue')
  // },
  {
    path: '/inscription',
    name: 'InscriptionView',
    component: () => import('@/views/InscriptionView.vue')
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router