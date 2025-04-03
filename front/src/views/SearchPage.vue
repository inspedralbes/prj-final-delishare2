<template>
  <div class="page-container">
    <header class="header">
      <img src="@/assets/images/delishare.png" alt="Logotip de DeliShare" class="header-logo" />
    </header>

    <!-- Mostrar contenido solo si está autenticado -->
    <div v-if="authStore.isAuthenticated">
      <div class="search-bar">
        <input type="text" v-model="searchQuery" placeholder="Cerca receptes..." />
      </div>

      <Botones 
        @filtradoPorCategoria="mostrarRecetasGenerales = false"
        @filtradoPorCuisine="mostrarRecetasGenerales = false"
        @filtradoPorTiempo="filtrarPorTiempo"
      />

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

    <!-- Mostrar mensaje de autenticación requerida si no hay token -->
    <div v-else class="auth-required-container">
      <div class="auth-required-message">
        <p>Per veure i buscar receptes, has d'iniciar sessió</p>
        <button @click="goToLogin" class="login-button">Iniciar Sessió</button>
      </div>
    </div>
  </div>
</template>

<script>
import Botones from '@/components/Botones.vue';
import RecipeCard from '@/components/RecipeCard.vue';
import communicationManager from '@/services/communicationManager';
import { useAuthStore } from '@/stores/authStore';
import { useRouter } from 'vue-router';

export default {
  name: 'SearchPage',
  components: { Botones, RecipeCard },
  setup() {
    const authStore = useAuthStore();
    const router = useRouter();
    return { authStore, router };
  },
  data() {
    return {
      loading: true,
      recipes: [],
      searchQuery: '',
      mostrarRecetasGenerales: true,
      tiempoFiltro: null,
    };
  },
  mounted() {
    this.checkAuthAndLoadData();
  },
  methods: {
    goToLogin() {
      this.router.push({ 
        name: 'login',
        query: { redirect: this.$route.fullPath }
      });
    },
    async checkAuthAndLoadData() {
      if (this.authStore.isAuthenticated) {
        await this.fetchRecipes();
      }
    },
    async fetchRecipes() {
      try {
        this.loading = true;
        this.recipes = await communicationManager.fetchRecipes();
      } catch (error) {
        console.error('Error al obtener las recetas:', error);
        if (error.response?.status === 401) {
          this.authStore.clearAuth();
        }
      } finally {
        this.loading = false;
      }
    },
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
  watch: {
    'authStore.isAuthenticated'(newVal) {
      if (newVal) {
        this.checkAuthAndLoadData();
      }
    }
  }
};
</script>


<style scoped>

/* Nuevos estilos para el mensaje de autenticación */
.auth-required-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 50vh;
}

.auth-required-message {
  text-align: center;
  padding: 2rem;
  background-color: #f8f9fa;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  max-width: 400px;
  width: 100%;
}

.auth-required-message p {
  margin-bottom: 1.5rem;
  font-size: 1.1rem;
  color: #343a40;
}

.login-button {
  padding: 0.75rem 1.5rem;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.3s;
}

.login-button:hover {
  background-color: #45a049;
}
* {
  font-family:'Times New Roman', Times, serif;
}

.page-container {
  text-align: center;
  padding: 20px;
  background-color: #fdfdff;
}

.header {
  margin-bottom: 20px;
}

.header-logo {
  width: 200px; 
  height: auto;
}

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
.search-bar input:focus{
  border-color: rgb(39, 39, 81);
 outline: none; 
}
.loading,
.no-recipes {
  margin: 20px 0;
  font-size: 16px;
  color: #333;
}

.recipe-carousel {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
  justify-items: center;
  margin-top: 20px;
  margin-bottom: 60px; 
}

@media (min-width: 600px) {
  .recipe-carousel {
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
  }
}

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
