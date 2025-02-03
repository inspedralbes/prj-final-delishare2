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
    const datos = ref([]);         // For categories
    const usuarios = ref([]);      // For users
    const recetas = ref([]);       // For filtered recipes
    const cuisines = ref([]);      // For cuisines (countries)

    // Function to fetch categories
    const obtenerCategorias = async () => {
      try {
        datos.value = await communicationManager.fetchCategories();
      } catch (error) {
        console.error('Error fetching categories:', error);
      }
    };

    // Function to fetch cuisines (countries)
    const obtenerCuisines = async () => {
      try {
        cuisines.value = await communicationManager.fetchCuisines();
      } catch (error) {
        console.error('Error fetching cuisines:', error);
      }
    };

    // Function to filter recipes by category
    const filtrarPorCategoria = async (categoryId) => {
      try {
        const response = await communicationManager.fetchRecipesByCategory(categoryId);
        recetas.value = response.recipes;
        emit('filtradoPorCategoria', true);
      } catch (error) {
        console.error('Error filtering recipes by category:', error);
      }
    };

    // Function to filter recipes by cuisine (country)
    const filtrarPorCuisine = async (cuisineId) => {
      try {
        const response = await communicationManager.fetchRecipesByCuisine(cuisineId);
        recetas.value = response.recipes;
        emit('filtradoPorCuisine', true);
      } catch (error) {
        console.error('Error filtering recipes by cuisine:', error);
      }
    };

    // Function to fetch users
    const obtenerUsers = async () => {
      try {
        usuarios.value = await communicationManager.fetchUsers();
      } catch (error) {
        console.error('Error fetching users:', error);
      }
    };

    // Function to filter recipes by user
    const filtrarPorUsuario = async (userId) => {
      try {
        const response = await communicationManager.fetchRecipesByUser(userId);
        recetas.value = response.recipes;
        emit('filtradoPorUsuarios', true);
      } catch (error) {
        console.error('Error filtering recipes by user:', error);
      }
    };

    return {
      datos,
      usuarios,
      recetas,
      cuisines,
      obtenerCategorias,
      obtenerCuisines,
      obtenerUsers,
      filtrarPorCategoria,
      filtrarPorCuisine,
      filtrarPorUsuario,
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
