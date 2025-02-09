<template>
  <div>
    <!-- Título centrado -->
    <h1 class="title">Recetas Guardadas</h1>

    <div v-if="loading" class="loading">Cargando...</div>
    <div v-else>
      <ul class="recipe-list">
        <li v-for="recipe in savedRecipes" :key="recipe.id" class="recipe-item">
          <!-- Tarjeta de receta con RecipeCard -->
          <RecipeCard
            :recipeId="recipe.id"
            :title="recipe.title"
            :image="recipe.image" 
          />
          
          <!-- Botón de eliminar -->
          <button @click="removeSavedRecipe(recipe.id)" class="remove-btn">Eliminar</button>
        </li>
      </ul>
      <p v-if="savedRecipes.length === 0" class="no-recipes">No tienes recetas guardadas.</p>
    </div>
  </div>
</template>

<script>
import communicationManager from '@/services/communicationManager';
import { ref, onMounted } from 'vue';
import RecipeCard from '@/components/RecipeCard.vue';  // Asegúrate de que la ruta sea correcta

export default {
  components: {
    RecipeCard
  },
  setup() {
    const savedRecipes = ref([]);
    const loading = ref(true);

    const fetchSavedRecipes = async () => {
      try {
        const response = await communicationManager.getSavedRecipes();  
        savedRecipes.value = response;
      } catch (error) {
        console.error('Error al obtener recetas guardadas:', error);
      } finally {
        loading.value = false;
      }
    };

    const removeSavedRecipe = async (recipeId) => {
      try {
        await communicationManager.toggleSaveRecipe(recipeId);  
        savedRecipes.value = savedRecipes.value.filter(r => r.id !== recipeId);  
      } catch (error) {
        console.error('Error al eliminar la receta:', error);
      }
    };

    onMounted(() => {
      fetchSavedRecipes();
    });

    return { savedRecipes, loading, removeSavedRecipe };
  }
};
</script>

<style scoped>
/* --- Estilo para el título centrado --- */
.title {
  text-align: center;
  font-size: 32px;
  margin-bottom: 20px;
  color: #343330;
}

/* --- Estilo General para la Lista de Recetas --- */
.recipe-list {
  display: grid;
  grid-template-columns: repeat(2, 1fr);  /* Dos columnas por fila */
  gap: 20px;
  justify-items: center;
  padding: 20px 10px;
}

.recipe-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  width: 100%;  /* Ajustamos el tamaño de las tarjetas a la columna */
  max-width: 250px; /* Tamaño máximo para la tarjeta */
  margin-bottom: 20px;
}

/* --- Estilo para el Botón de Eliminar --- */
.remove-btn {
  background-color: #0c0636;
  color: white;
  border: none;
  padding: 8px 12px;
  border-radius: 20px;
  font-size: 14px;
  cursor: pointer;
  margin-top: 10px;
  transition: background-color 0.3s ease;
}

.remove-btn:hover {
  background-color:#322b5f;
}

/* --- Estilo de "No tienes recetas guardadas" --- */
.no-recipes {
  font-size: 18px;
  color: #888;
  margin-top: 20px;
}

/* --- Estilo de Cargando --- */
.loading {
  font-size: 18px;
  color: #444;
  text-align: center;
  padding: 20px;
  font-weight: bold;
}

/* --- Medios para Pantallas >600px (Tablets) --- */
@media (min-width: 600px) {
  .recipe-list {
    grid-template-columns: repeat(2, 1fr);  /* Dos columnas por fila en tablets */
  }

  .recipe-item {
    width: 100%;
    max-width: 280px;  /* Un poco más grande en pantallas grandes */
  }

  .remove-btn {
    font-size: 16px;
    padding: 10px 14px;
  }
}

/* --- Medios para Pantallas >1024px (Escritorio) --- */
@media (min-width: 1024px) {
  .recipe-list {
    grid-template-columns: repeat(2, 1fr);  /* Dos columnas por fila en escritorio */
  }

  .recipe-item {
    width: 100%;
    max-width: 300px;  /* Un tamaño mayor en pantallas grandes */
  }

  .remove-btn {
    font-size: 18px;
    padding: 12px 16px;
  }
}
</style>
