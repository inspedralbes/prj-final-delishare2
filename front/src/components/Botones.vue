<template>
  <div class="filter-buttons">
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

    <!-- Recetas filtradas por categoría -->
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
  emits: ['filtradoPorCategoria'], // Declaramos el evento

  setup(_, { emit }) {
    const datos = ref([]);         
    const recetas = ref([]);       

    const obtenerCategorias = async () => {
      try {
        datos.value = await communicationManager.fetchCategories();
      } catch (error) {
        console.error('Error fetching categories:', error);
      }
    };

    const filtrarPorCategoria = async (categoryId) => {
      try {
        const response = await communicationManager.fetchRecipesByCategory(categoryId);
        recetas.value = response.recipes;
        emit('filtradoPorCategoria', true);  // Emitimos el evento para ocultar las recetas generales
      } catch (error) {
        console.error('Error filtering recipes:', error);
      }
    };

    return {
      datos,
      recetas,
      obtenerCategorias,
      filtrarPorCategoria,
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
