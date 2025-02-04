<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';  // Importa useRoute para obtener la ruta actual
import { useAuthStore } from '@/stores/authStore';

const route = useRoute();  // Obtén la ruta actual
const authStore = useAuthStore();  // Usa el store para obtener el estado de autenticación

// Computed para verificar si la ruta actual es login o register
const isAuthPage = computed(() => {
  return route.name === 'login' || route.name === 'register';  // Verifica si estamos en login o register
});

const isAuthenticated = computed(() => {
  return !!authStore.token;  // Verifica si el usuario está autenticado
});
</script>

<template>
  <!-- Si no estamos en la página de login o register, mostramos el Navbar -->
  <nav v-if="!isAuthPage" class="navbar">
    <ul>
      <li><router-link to="/recetas"><img src="../assets/images/homee.png" alt="Home" /></router-link></li>
      <li><router-link to="/search"><img src="../assets/images/lupa.png" alt="Search" class="search-icon" /></router-link></li>
      <li><router-link to="/agregar"><img src="../assets/images/mas.png" alt="Post" /></router-link></li>
      <li><router-link to="/guardar"><img src="../assets/images/guardar.png" alt="Saved" /></router-link></li>
      <li><router-link to="/perfil"><img src="../assets/images/perfil.png" alt="perfil" /></router-link></li>
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
  