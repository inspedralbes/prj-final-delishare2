<template>
  <div class="search-page">
    <h1>Recetas Disponibles</h1>

    <div class="search-bar">
      <input type="text" v-model="searchQuery" placeholder="Buscar recetas..." />
    </div>

    <!-- Componente de Botones -->
    <Botones 
      @filtradoPorCategoria="mostrarRecetasGenerales = false"
      @filtradoPorCuisine="mostrarRecetasGenerales = false"
      @filtradoPorTiempo="filtrarPorTiempo"
    />

    <!-- Mostrar recetas generales solo si no hay filtros activos -->
    <div v-if="mostrarRecetasGenerales">
      <div v-if="loading" class="loading">Cargando recetas...</div>

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
  </div>
</template>

<script>
import Botones from '@/components/Botones.vue';
import RecipeCard from '@/components/RecipeCard.vue';
import communicationManager from '@/services/communicationManager';

export default {
  components: { Botones, RecipeCard },
  data() {
    return {
      loading: true,
      recipes: [],
      searchQuery: '',
      mostrarRecetasGenerales: true, // Mostrar recetas generales por defecto
      tiempoFiltro: null, // Variable para filtrar por tiempo
    };
  },
  mounted() {
    this.fetchRecipes();
  },
  methods: {
    async fetchRecipes() {
      try {
        this.loading = true;
        this.recipes = await communicationManager.fetchRecipes();
      } catch (error) {
        console.error('Error al obtener las recetas:', error);
      } finally {
        this.loading = false;
      }
    },
    
    // Método para filtrar por tiempo
    filtrarPorTiempo(tiempo) {
      this.tiempoFiltro = tiempo;
      this.mostrarRecetasGenerales = false; // Cuando hay un filtro, ocultar las recetas generales
    }
  },
  computed: {
    filteredRecipes() {
      let recipesFilteredBySearch = this.recipes.filter(recipe =>
        recipe.title.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
        (recipe.description && recipe.description.toLowerCase().includes(this.searchQuery.toLowerCase()))
      );

      // Si se ha aplicado un filtro de tiempo, filtramos también por eso
      if (this.tiempoFiltro) {
        recipesFilteredBySearch = recipesFilteredBySearch.filter(recipe => recipe.time <= this.tiempoFiltro);
      }

      return recipesFilteredBySearch;
    },
  },
};
</script>

<style scoped>
.recipe-list {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
  justify-items: center;
  margin-top: 20px;
}
</style>
