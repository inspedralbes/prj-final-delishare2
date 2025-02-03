<template>
  <div class="filter-buttons">
    <!-- Botón para las categorías -->
    <button @click="obtenerCategorias">Categoría</button>
    <div v-if="datos.length">
      <button 
        v-for="dato in datos" 
        :key="dato.id" 
        class="button-secondary"
        @click="filtrarPorCategoria(dato.id)"
      >
        {{ dato.name }}
      </button>
    </div>

    <!-- Botón para las cocinas -->
    <button @click="obtenerCuisines">Cuisine</button>
    <div v-if="cuisines.length">
      <button 
        v-for="cuisine in cuisines" 
        :key="cuisine.id" 
        class="button-secondary"
        @click="filtrarPorCuisine(cuisine.id)"
      >
        {{ cuisine.country }}
      </button>
    </div>

    <!-- Botón para los usuarios -->
    <button @click="obtenerUsers">Usuarios</button>
    <div v-if="usuarios.length">
      <button 
        v-for="usuario in usuarios" 
        :key="usuario.id" 
        class="button-secondary"
        @click="filtrarPorUsuario(usuario.id)"
      >
        {{ usuario.name }}
      </button>
    </div>

    <!-- Botón para los tiempos -->
    <button @click="obtenerTimes">Tiempo</button>
    <div v-if="times.length">
      <button 
        v-for="time in times" 
        :key="time"
        class="button-secondary"
        @click="filtrarPorTiempo(time)"
      >
        {{ time }} minutos
      </button>
    </div>

    <!-- Recetas filtradas por categoría, usuario o cocina -->
    <div v-if="recetas.length" class="recipe-list">
      <RecipeCard
        v-for="receta in recetas"
        :key="receta.id"
        :recipeId="receta.id"
        :title="receta.title"
        :description="receta.description || 'Descripción no disponible'"
        :image="receta.image"
      />
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import communicationManager from '../services/communicationManager';
import RecipeCard from './RecipeCard.vue';

export default {
  name: 'Botones',
  components: { RecipeCard },

  setup(_, { emit }) {
    // Variables reactivas para almacenar los datos
    const datos = ref([]);         // Para las categorías
    const usuarios = ref([]);      // Para los usuarios
    const recetas = ref([]);       // Para las recetas filtradas
    const cuisines = ref([]);      // Para las cocinas (países)
    const times = ref([]);         // Para los tiempos

    // Función para obtener las categorías
    const obtenerCategorias = async () => {
      try {
        datos.value = await communicationManager.fetchCategories();
      } catch (error) {
        console.error('Error al obtener categorías:', error);
      }
    };

    // Función para obtener las cocinas (países)
    const obtenerCuisines = async () => {
      try {
        cuisines.value = await communicationManager.fetchCuisines();
      } catch (error) {
        console.error('Error al obtener cocinas:', error);
      }
    };

    // Función para obtener los tiempos disponibles
    const obtenerTimes = async () => {
      try {
        const response = await communicationManager.getAllTimes();
        times.value = response.times; // Asegúrate de usar `response.times` aquí
      } catch (error) {
        console.error('Error al obtener tiempos:', error);
      }
    };

    // Función para filtrar por tiempo
    const filtrarPorTiempo = async (time) => {
      try {
        const response = await communicationManager.fetchRecipesByTime(time);
        recetas.value = response.recipes;
        emit('filtradoPorTiempo', true);
      } catch (error) {
        console.error('Error al filtrar recetas por tiempo:', error);
      }
    };

    // Función para filtrar por categoría
    const filtrarPorCategoria = async (categoryId) => {
      try {
        const response = await communicationManager.fetchRecipesByCategory(categoryId);
        recetas.value = response.recipes;
        emit('filtradoPorCategoria', true);
      } catch (error) {
        console.error('Error al filtrar recetas por categoría:', error);
      }
    };

    // Función para filtrar por cocina (país)
    const filtrarPorCuisine = async (cuisineId) => {
      try {
        const response = await communicationManager.fetchRecipesByCuisine(cuisineId);
        recetas.value = response.recipes;
        emit('filtradoPorCuisine', true);
      } catch (error) {
        console.error('Error al filtrar recetas por cocina:', error);
      }
    };

    // Función para obtener los usuarios
    const obtenerUsers = async () => {
      try {
        usuarios.value = await communicationManager.fetchUsers();
      } catch (error) {
        console.error('Error al obtener usuarios:', error);
      }
    };

    // Función para filtrar recetas por usuario
    const filtrarPorUsuario = async (userId) => {
      try {
        const response = await communicationManager.fetchRecipesByUser(userId);
        recetas.value = response.recipes;
        emit('filtradoPorUsuarios', true);
      } catch (error) {
        console.error('Error al filtrar recetas por usuario:', error);
      }
    };

    return {
      datos,
      usuarios,
      recetas,
      cuisines,
      times,
      obtenerCategorias,
      obtenerCuisines,
      obtenerUsers,
      obtenerTimes,
      filtrarPorCategoria,
      filtrarPorCuisine,
      filtrarPorUsuario,
      filtrarPorTiempo,
    };
  },
};
</script>

<style scoped>
.button-secondary {
  font-size: 0.85rem;
  padding: 8px 12px;
  background-color: #358600;
  border: none;
  color: white;
  cursor: pointer;
  border-radius: 20px;
}

.button-secondary:hover {
  background-color: #2a6b00;
}

.recipe-list {
  display: grid;
  grid-template-columns: repeat(2, 1fr); 
  gap: 20px;
  justify-items: center;
  margin-top: 20px;
}
</style>
