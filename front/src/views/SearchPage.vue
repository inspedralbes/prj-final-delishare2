<template>
  <div class="page-container">
    <header class="header">
      <!-- Es substitueix el títol per logo, igual que a la landingPage -->
    </header>

    <div class="search-bar">
      <input type="text" v-model="searchQuery" placeholder="Cerca receptes..." />
    </div>

    <!-- Componente de Botons -->
    <Botones 
      @filtradoPorCategoria="mostrarRecetasGenerales = false"
      @filtradoPorCuisine="mostrarRecetasGenerales = false"
      @filtradoPorTiempo="filtrarPorTiempo"
    />

    <!-- Mostrar receptes generals només si no hi ha filtres actius -->
    <div v-if="mostrarRecetasGenerales">
      <div v-if="loading" class="loading">Carregant receptes...</div>
      <div v-else>
        <div v-if="filteredRecipes.length === 0" class="no-recipes">
          No hi ha receptes disponibles.
        </div>
        <div class="recipe-carousel">
          <RecipeCard
            v-for="recipe in filteredRecipes"
            :key="recipe.id"
            :recipeId="recipe.id"
            :title="recipe.title"
            :description="recipe.description || 'Descripció no disponible'"
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
      mostrarRecetasGenerales: true, // Se muestran todas las recetas por defecto
      tiempoFiltro: null, // Para filtrar por tiempo
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
      this.mostrarRecetasGenerales = false;
    },
  },
  computed: {
    filteredRecipes() {
      let recipesFilteredBySearch = this.recipes.filter(recipe =>
        recipe.title.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
        (recipe.description &&
          recipe.description.toLowerCase().includes(this.searchQuery.toLowerCase()))
      );

      if (this.tiempoFiltro) {
        recipesFilteredBySearch = recipesFilteredBySearch.filter(
          recipe => recipe.time <= this.tiempoFiltro
        );
      }
      return recipesFilteredBySearch;
    },
  },
};
</script>

<style scoped>
/* Contenedor general */
.page-container {
  text-align: center;
  padding: 20px;
  background-color: #fdfdff;
}

/* Estilos del header */
.header {
  margin-bottom: 20px;
}

/* Estilo del logo en el header (similar al landingPage) */
.header-logo {
  width: 200px; /* Puedes ajustar el ancho según necesites */
  height: auto;
}

/* Estilo de la barra de búsqueda */
.search-bar {
  margin: 10px 0;
}
.search-bar input {
  width: 80%;
  max-width: 300px;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 20px;
}

/* Mensajes de carga y ausencia de recetas */
.loading,
.no-recipes {
  margin: 20px 0;
  font-size: 16px;
  color: #333;
}

/* Contenedor de las cards con espacio extra inferior para el navbar */
.recipe-carousel {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
  justify-items: center;
  margin-top: 20px;
  margin-bottom: 60px; /* Espacio extra debajo de las recetas */
}

/* Tablets */
@media (min-width: 600px) {
  .recipe-carousel {
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
  }
}

/* Escritorio */
@media (min-width: 1024px) {
  .page-container {
    max-width: 1200px;
    margin: auto;
  }
  .recipe-carousel {
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
  }
}
</style>
