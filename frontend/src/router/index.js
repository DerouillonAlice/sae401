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
  {
    path: '/connexion',
    name: 'ConnexionView',
    component: () => import('@/views/ConnexionView.vue')
  },

  {
    path :'/mentions-legales',
    name: 'MentionsLegalesView',
    component: () => import('@/views/MentionsLegalesView.vue')
  },
  { 
    path: '/contact',
    name: 'ContactView',
    component: () => import('@/views/ContactView.vue')
  },
  {
    path: '/mot-de-passe-oublie',
    name: 'ForgotPassword',
    component: () => import('@/views/ForgotPasswordView.vue')
  },
  {
    path: '/reset-password',
    name: 'ResetPassword',
    component: () => import('@/views/ResetPasswordView.vue')
  }
  
]

const router = createRouter({
  history: createWebHistory(),
  routes
})


export default router