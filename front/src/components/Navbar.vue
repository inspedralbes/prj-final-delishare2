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
  <nav v-if="!isAuthPage" class="navbar">
    <ul>
      <li><router-link to="/"><svg width="30px" height="30px" viewBox="0 0 24 24" fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
            <g id="SVGRepo_iconCarrier">
              <path
                d="M5 9.77746V16.2C5 17.8802 5 18.7203 5.32698 19.362C5.6146 19.9265 6.07354 20.3854 6.63803 20.673C7.27976 21 8.11984 21 9.8 21H14.2C15.8802 21 16.7202 21 17.362 20.673C17.9265 20.3854 18.3854 19.9265 18.673 19.362C19 18.7203 19 17.8802 19 16.2V5.00002M21 12L15.5668 5.96399C14.3311 4.59122 13.7133 3.90484 12.9856 3.65144C12.3466 3.42888 11.651 3.42893 11.0119 3.65159C10.2843 3.90509 9.66661 4.59157 8.43114 5.96452L3 12"
                stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </g>
          </svg></router-link></li>
      <li><router-link to="/search"><svg width="30px" height="30px" viewBox="0 0 24 24" fill="none"
            xmlns="http://www.w3.org/2000/svg" stroke="#ffffff">
            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
            <g id="SVGRepo_iconCarrier">
              <path
                d="M11 6C13.7614 6 16 8.23858 16 11M16.6588 16.6549L21 21M19 11C19 15.4183 15.4183 19 11 19C6.58172 19 3 15.4183 3 11C3 6.58172 6.58172 3 11 3C15.4183 3 19 6.58172 19 11Z"
                stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </g>
          </svg></router-link></li>
      <li><router-link to="/agregar"><svg width="30px" height="30px" viewBox="0 0 24 24" fill="none"
            xmlns="http://www.w3.org/2000/svg" stroke="#ffffff">
            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
            <g id="SVGRepo_iconCarrier">
              <path
                d="M8 12H16M12 8V16M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z"
                stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </g>
          </svg></router-link></li>

      <li>
        <router-link to="/live">
          <svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
            stroke="#ffffff">
            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
            <g id="SVGRepo_iconCarrier">
              <path
                d="M12 10.5C12.8284 10.5 13.5 11.1716 13.5 12C13.5 12.8284 12.8284 13.5 12 13.5C11.1716 13.5 10.5 12.8284 10.5 12C10.5 11.1716 11.1716 10.5 12 10.5Z"
                stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M15.2 8.8C16.4 10 16.4 14 15.2 15.2" stroke="#ffffff" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" />
              <path d="M8.8 8.8C7.6 10 7.6 14 8.8 15.2" stroke="#ffffff" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" />
              <path d="M18 6C20 8 20 16 18 18" stroke="#ffffff" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" />
              <path d="M6 6C4 8 4 16 6 18" stroke="#ffffff" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" />
            </g>
          </svg>
        </router-link>
      </li>
      <li><router-link to="/perfil"><img src="../assets/images/perfil_black.png" alt="perfil" /></router-link></li>

    </ul>
  </nav>
</template>

<style scoped>
.navbar {
  background-color: #343330;
  padding: 10px 0;
  text-align: center;
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  z-index: 1000;
}

.navbar ul {
  list-style-type: none;
  margin: 0;
  padding: 0;
  display: flex;
  justify-content: space-around;
}

.navbar button {
  background-color: transparent;
  border: none;
  cursor: pointer;
  padding: 0;
}

.navbar img {
  width: 30px;
  height: 30px;
  object-fit: contain;
}

.navbar button:hover img {
  opacity: 0.8;
}
</style>