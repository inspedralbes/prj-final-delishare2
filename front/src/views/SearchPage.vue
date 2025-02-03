<template>
  <div class="search-page">
    <h1>Recetas Disponibles</h1>

    <!-- Campo de búsqueda -->
    <div class="search-bar">
      <input type="text" v-model="searchQuery" placeholder="Buscar recetas..." />
    </div>

    <!-- Componente de Botones -->
    <Botones />
    
    <!-- Mostrar mensaje de carga mientras se obtienen las recetas -->
    <div v-if="loading" class="loading">
      Cargando recetas...
    </div>
    
    <!-- Mostrar las recetas cuando se han cargado -->
    <div v-else>
      <div v-if="filteredRecipes.length === 0" class="no-recipes">
        No hay recetas disponibles.
      </div>

      <div class="recipe-list">
        <RecipeCard
          v-for="recipe in filteredRecipes"
          :key="recipe.id"
          :recipeId="recipe.id"
          :title="recipe.title"
          :description="recipe.description || 'Descripción no disponible'"
          :image="recipe.image"
        />
      </div>
    </div>
  </div>
</template>

<script>
import Botones from '@/components/Botones.vue';
import RecipeCard from '@/components/RecipeCard.vue';
import communicationManager from '@/services/communicationManager'; // Importamos communicationManager

export default {
  components: {
    Botones,
    RecipeCard
  },
  data() {
    return {
      loading: true, // Para indicar si estamos cargando las recetas
      recipes: [],   // Aquí se guardarán las recetas
      searchQuery: '' // Término de búsqueda
    };
  },
  mounted() {
    this.fetchRecipes();
  },
  methods: {
    async fetchRecipes() {
      try {
        this.loading = true; // Activamos el estado de carga
        this.recipes = await communicationManager.fetchRecipes(); // Usamos communicationManager
      } catch (error) {
        console.error('Error al obtener las recetas:', error);
      } finally {
        this.loading = false; // Finalizamos el estado de carga
      }
    }
  },
  computed: {
    filteredRecipes() {
      return this.recipes.filter(recipe => 
        recipe.title.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
        (recipe.description && recipe.description.toLowerCase().includes(this.searchQuery.toLowerCase()))
      );
    }
  }
};
</script>

<style scoped>
/* Los estilos de la página de búsqueda siguen siendo los mismos */
body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  background-color: #f5f5f5;
  color: #343330;
  margin-bottom: 30px;
  margin-top: 20px;
}

h1 {
  text-align: center;
  color: #358600;
  font-size: 2em;
  margin-bottom: 20px;
}

.search-bar {
  text-align: center;
  margin-bottom: 10px;
}

.search-bar input {
  padding: 10px;
  width: 70%;
  max-width: 400px;
  border-radius: 5px;
  border: 1px solid #ccc;
  font-size: 16px;
}

.filter-buttons {
  display: flex;
  justify-content: center;
  gap: 10px;
  margin-bottom: 20px;
}

.filter-buttons button {
  padding: 8px 16px;
  background-color: #358600;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.filter-buttons button:hover {
  background-color: #2a6b00;
}

.loading {
  text-align: center;
  font-size: 1.2em;
  color: #358600;
}

.no-recipes {
  text-align: center;
  font-size: 1.2em;
  color: #666;
}

.recipe-list {
  display: grid;
  grid-template-columns: repeat(2, 1fr); /* 2 tarjetas por fila */
  gap: 20px;
  justify-items: center;
  margin-top: 20px;
}
</style>
