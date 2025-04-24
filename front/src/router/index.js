// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router';
import LandingPage from '@/views/LandingPage.vue';
import SearchPage from '@/views/SearchPage.vue';
import Chat from '@/views/Chat.vue';
import InfoReceta from '@/views/InfoReceta.vue';
import AgregarReceta from '@/views/AgregarReceta.vue';
import login from '@/components/login.vue';
import register from '@/components/register.vue';
import Guardadas from '@/views/Guardadas.vue';
import { useAuthStore } from '@/stores/authStore';  // Importa el store de autenticación
import UserProfile from '@/components/UserProfile.vue';
import Formulario from '@/views/Formulario.vue';
import RecetasList from '@/views/RecetasList.vue';
import UsersList from '@/views/UsersList.vue';
import CuisinesView from '@/views/CuisinesView.vue'; // Importamos la nueva vista

const routes = [
  {
    path: '/Chat',
    name: 'ChatBot',
    component: Chat,
    meta: { requiresAuth: false },
  },
  {
    path: '/recetas',
    name: 'RecetasList',
    component: RecetasList,
    meta: { requiresAuth: false },
  },
  {
    path: '/users',
    name: 'UsersList',
    component: UsersList,
    meta: { requiresAuth: true },
  },
  {
    path: '/formulario',
    name: 'Formulario',
    component: Formulario,
    meta: { requiresAuth: false },
  },
  {
    path: '/notifications',
    name: 'Notifications',
    component: () => import('../views/NotificationsPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/agregar',
    name: 'AgregarReceta',
    component: AgregarReceta,
    meta: { requiresAuth: false },
  },
  {
    path: '/search',
    name: 'SearchPage',
    component: SearchPage,
    meta: { requiresAuth: false },
  },
  {
    path: '/user/:userId',
    name: 'UserProfile',
    component: UserProfile,
    props: true
  },
  {
    path: '/login',
    name: 'login',
    component: login,
  },
  {
    path: '/',
    name: 'LandingPage',
    component: LandingPage,
    meta: { requiresAuth: false }
  },
  {
    path: '/register',
    name: 'register',
    component: register,
  },
  {
    path: '/info/:recipeId',
    name: 'InfoReceta',
    component: InfoReceta,
    props: true,
    meta: { requiresAuth: true },
  },
  {
    path: '/guardar',
    name: 'Guardadas',
    component: Guardadas,
    meta: { requiresAuth: false },
  },
  {
    path: '/perfil',
    name: 'Perfil',
    component: () => import('@/views/Perfil.vue'),
    meta: { requiresAuth: false },
  },
  // Nueva ruta para las cocinas
  {
    path: '/cuisines',
    name: 'CuisinesView',
    component: CuisinesView,
    meta: { requiresAuth: false } // Ajusta según tus necesidades de autenticación
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  
  if (to.meta.requiresAuth && !authStore.token) {
    // Para InfoReceta, permitimos que el componente maneje la lógica
    if (to.name === 'InfoReceta') {
      next();
    } else {
      next({ name: 'login' });
    }
  } else {
    next();
  }
});

export default router;