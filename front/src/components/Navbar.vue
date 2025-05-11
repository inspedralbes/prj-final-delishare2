<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';

const route = useRoute();
const authStore = useAuthStore();

// verificar si la ruta actual es login o register
const isAuthPage = computed(() => {
  return route.name === 'login' || route.name === 'register';
});

const isAuthenticated = computed(() => {
  return !!authStore.token;
});
</script>

<template>
  <!-- Si no estamos en la página de login o register, mostramos el Navbar -->
  <nav v-if="!isAuthPage" class="fixed bottom-0 left-0 w-full bg-white/90 backdrop-blur-md border-t border-lime-200 h-[70px] flex items-center z-50 mt-8 shadow-lg">
    <ul class="flex justify-around items-center w-full max-w-md mx-auto">
      <li>
        <router-link to="/" class="p-2 hover:bg-lime-50 rounded-lg transition-colors duration-200">
          <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
            <g id="SVGRepo_iconCarrier">
              <path d="M5 9.77746V16.2C5 17.8802 5 18.7203 5.32698 19.362C5.6146 19.9265 6.07354 20.3854 6.63803 20.673C7.27976 21 8.11984 21 9.8 21H14.2C15.8802 21 16.7202 21 17.362 20.673C17.9265 20.3854 18.3854 19.9265 18.673 19.362C19 18.7203 19 17.8802 19 16.2V5.00002M21 12L15.5668 5.96399C14.3311 4.59122 13.7133 3.90484 12.9856 3.65144C12.3466 3.42888 11.651 3.42893 11.0119 3.65159C10.2843 3.90509 9.66661 4.59157 8.43114 5.96452L3 12" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </g>
          </svg>
        </router-link>
      </li>
      <li>
        <router-link to="/search" class="p-2 hover:bg-lime-50 rounded-lg transition-colors duration-200">
          <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#4CAF50">
            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
            <g id="SVGRepo_iconCarrier">
              <path d="M11 6C13.7614 6 16 8.23858 16 11M16.6588 16.6549L21 21M19 11C19 15.4183 15.4183 19 11 19C6.58172 19 3 15.4183 3 11C3 6.58172 6.58172 3 11 3C15.4183 3 19 6.58172 19 11Z" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </g>
          </svg>
        </router-link>
      </li>
      <li>
        <router-link to="/agregar" class="p-2 hover:bg-lime-50 rounded-lg transition-colors duration-200">
          <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#4CAF50">
            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
            <g id="SVGRepo_iconCarrier">
              <path d="M8 12H16M12 8V16M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </g>
          </svg>
        </router-link>
      </li>
      <li>
        <router-link to="/live" class="p-2 hover:bg-lime-50 rounded-lg transition-colors duration-200">
          <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#4CAF50">
            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
            <g id="SVGRepo_iconCarrier">
              <path d="M12 10.5C12.8284 10.5 13.5 11.1716 13.5 12C13.5 12.8284 12.8284 13.5 12 13.5C11.1716 13.5 10.5 12.8284 10.5 12C10.5 11.1716 11.1716 10.5 12 10.5Z" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M15.2 8.8C16.4 10 16.4 14 15.2 15.2" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M8.8 8.8C7.6 10 7.6 14 8.8 15.2" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M18 6C20 8 20 16 18 18" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M6 6C4 8 4 16 6 18" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </g>
          </svg>
        </router-link>
      </li>
      <li>
        <router-link to="/perfil" class="p-2 hover:bg-lime-50 rounded-lg transition-colors duration-200">
          <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
            <g id="SVGRepo_iconCarrier">
              <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M20.5899 22C20.5899 18.13 16.7399 15 11.9999 15C7.25991 15 3.40991 18.13 3.40991 22" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </g>
          </svg>
        </router-link>
      </li>
    </ul>
  </nav>
</template>

<style scoped>
/* Se puede eliminar todo el CSS ya que ahora usamos Tailwind */
</style>