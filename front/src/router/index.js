import { createRouter, createWebHistory } from 'vue-router';
import LandingPage from '@/views/LandingPage.vue';
import SearchPage from '@/views/SearchPage.vue';
import InfoReceta from '@/views/InfoReceta.vue'; // Importa el componente de la página de detalles
import AgregarReceta from '@/views/AgregarReceta.vue'; // Importa el componente de la página de detalles
import login from '@/components/login.vue'; // Ajusta si el archivo tiene otro nombre o ruta
import register from '@/components/register.vue'; // Ajusta si el archivo tiene otro nombre o ruta
import Guardadas from '@/views/Guardadas.vue';

const routes = [

  {
    path: '/recetas',  // Ruta principal
    name: 'LandingPage',
    component: LandingPage,  // El componente que se renderiza para esta ruta
    meta: { requiresAuth: true },

  },  
  {
    path: '/agregar',
    name: 'AgregarReceta',
    component: AgregarReceta,  // El componente que se renderiza para la búsqueda
    meta: { requiresAuth: true },
  },
  {
    path: '/search',  // Ruta de búsqueda
    name: 'SearchPage',
    component: SearchPage,  // El componente que se renderiza para la búsqueda
    meta: { requiresAuth: true },

  },
  {
    path: '/',  // Ruta con el parámetro de id de la receta
    name: 'login',
    component: login,
    props: true, // Habilita pasar el parámetro `recipeId` como prop al componente

  },
  {
    path: '/register',  // Ruta con el parámetro de id de la receta
    name: 'register',
    component: register,
    props: true, // Habilita pasar el parámetro `recipeId` como prop al componente

  },
  {
    path: '/info/:recipeId',  // Ruta con el parámetro de id de la receta
    name: 'InfoReceta',
    component: InfoReceta,
    props: true, // Habilita pasar el parámetro `recipeId` como prop al componente
    meta: { requiresAuth: true },

  },
  {
    path: '/guardar',
    name: 'Guardadas',
    component: Guardadas,
    meta: { requiresAuth: true },

  },
  {
    path: '/perfil',
    name: 'Perfil',
    component: () => import('@/views/Perfil.vue'),
    meta: { requiresAuth: true },
}

];

const router = createRouter({
  history: createWebHistory(), // Usa el historial del navegador para manejar rutas
  routes,
});

export default router;
