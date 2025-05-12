<template>
  <div class="text-center p-5 bg-lime-50">
    <header class="mb-5">
      <div class="relative overflow-hidden rounded-xl">
        <div class="bg-gradient-to-br from-lime-100 via-lime-200 to-green-200 py-10 px-4 sm:py-14 sm:px-8 relative">
          <div class="absolute inset-0 bg-white/30 backdrop-blur-sm rounded-xl"></div>
          <div class="relative z-10 flex flex-col items-center justify-center">
            <img src="@/assets/images/delishare.png" alt="Logotip de DeliShare" class="w-[180px] h-auto mb-4 drop-shadow" />
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-lime-900 bg-gradient-to-r from-lime-900 via-lime-700 to-green-800 bg-clip-text text-transparent mb-2">Cercador de receptes</h1>
            <p class="text-lg sm:text-xl text-lime-700 font-medium">Explora, filtra i descobreix receptes delicioses</p>
          </div>
        </div>
      </div>
    </header>

    <!-- Mostrar contingut només si està autenticat -->
    <div v-if="authStore.isAuthenticated">
      <div class="flex items-center justify-center gap-2 mb-2.5">
        <div class="my-2.5 flex-1 max-w-[600px]">
          <input type="text" v-model="searchQuery" placeholder="Cerca receptes..." class="w-full p-2.5 border border-gray-300 rounded-[20px] focus:border-[#272751] focus:outline-none" />
        </div>
        <button class="bg-gradient-to-r from-[#22c55e] to-[#a3e635] border-none rounded-full w-9 h-9 flex items-center justify-center shadow-[0_2px_8px_0_rgba(163,230,53,0.10)] cursor-pointer transition-all duration-200 hover:shadow-[0_4px_16px_0_rgba(163,230,53,0.18)] hover:bg-gradient-to-r hover:from-[#16a34a] hover:to-[#bef264]" @click="drawerOpen = true" aria-label="Obrir filtres">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect y="4" width="24" height="2.5" rx="1.25" fill="#22c55e"/>
            <rect y="10.75" width="24" height="2.5" rx="1.25" fill="#22c55e"/>
            <rect y="17.5" width="24" height="2.5" rx="1.25" fill="#22c55e"/>
          </svg>
        </button>
      </div>

      <div v-if="loading" class="my-5 text-base text-gray-700">Carregant receptes...</div>
      <div v-else>
        <div v-if="filteredRecipes.length === 0" class="my-5 text-base text-gray-700">
          No hi ha receptes disponibles.
        </div>
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-5 mt-5 mb-[60px]">
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

      <!-- Drawer de filtres -->
      <transition name="drawer">
        <div v-if="drawerOpen" class="fixed inset-0 bg-black/18 z-[1000] flex justify-end" @click.self="drawerOpen = false">
          <div class="bg-white w-[90vw] max-w-[400px] h-screen shadow-[-2px_0_16px_rgba(34,197,94,0.10)] rounded-l-[18px] p-8 px-6 relative flex flex-col animate-[slideInDrawer_0.3s_cubic-bezier(.4,0,.2,1)]">
            <button class="absolute top-5 right-5 bg-transparent border-none text-3xl text-[#22c55e] cursor-pointer z-10" @click="drawerOpen = false" aria-label="Tancar filtres">&times;</button>
            <h2 class="text-2xl font-bold text-[#166534] mb-5 text-center">Filtres de receptes</h2>
            <div class="flex-1 overflow-y-auto">
              <Botones 
                :allRecipes="recipes"
                @filtradoPorCategoria="handleFiltradoPorCategoria"
                @filtradoPorCuisine="handleFiltradoPorCuisine"
                @filtradoPorTiempo="handleFiltradoPorTiempo"
                @filtradoPorIngrediente="handleFiltradoPorIngrediente"
                @filtradoPorDificultad="handleFiltradoPorDificultad"
                @closeDrawer="drawerOpen = false"
              />
            </div>
          </div>
        </div>
      </transition>
    </div>

    <!-- Mostrar missatge d'autenticació requerida si no hi ha token -->
    <div v-else class="flex justify-center items-center h-[50vh]">
      <div class="text-center p-8 bg-[#f8f9fa] rounded-lg shadow-[0_2px_8px_rgba(0,0,0,0.1)] max-w-[400px] w-full">
        <p class="mb-6 text-lg text-[#343a40]">Per veure i buscar receptes, has d'iniciar sessió</p>
        <button @click="goToLogin" class="px-6 py-3 bg-[#4CAF50] text-white border-none rounded text-base cursor-pointer transition-colors duration-300 hover:bg-[#45a049]">Iniciar Sessió</button>
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
      filteredRecipes: [],
      searchQuery: '',
      drawerOpen: false,
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
        const response = await communicationManager.fetchRecipes();
        this.recipes = Array.isArray(response) ? response : [];
        this.filteredRecipes = this.recipes;
        console.log('Recetas cargadas:', this.recipes); // Debug log
      } catch (error) {
        console.error('Error al obtener las recetas:', error);
        if (error.response?.status === 401) {
          this.authStore.clearAuth();
        }
        this.recipes = [];
        this.filteredRecipes = [];
      } finally {
        this.loading = false;
      }
    },
    // Manejadores para los distintos filtros
    handleFiltradoPorCategoria(filteredRecipes) {
      console.log('Recetas filtradas recibidas:', filteredRecipes); // Debug log
      this.filteredRecipes = filteredRecipes;
    },
    handleFiltradoPorCuisine(filteredRecipes) {
      this.filteredRecipes = filteredRecipes;
    },
    handleFiltradoPorTiempo(filteredRecipes) {
      this.filteredRecipes = filteredRecipes;
    },
    handleFiltradoPorIngrediente(filteredRecipes) {
      this.filteredRecipes = filteredRecipes;
    },
    handleFiltradoPorDificultad(filteredRecipes) {
      this.filteredRecipes = filteredRecipes;
    },
  },
  watch: {
    'authStore.isAuthenticated'(newVal) {
      if (newVal) {
        this.checkAuthAndLoadData();
      }
    },
    searchQuery(newVal) {
      if (!newVal) {
        this.filteredRecipes = this.recipes;
      } else {
        const searchTerm = newVal.toLowerCase();
        this.filteredRecipes = this.recipes.filter(recipe => 
          recipe.title.toLowerCase().includes(searchTerm) ||
          (recipe.description && recipe.description.toLowerCase().includes(searchTerm))
        );
      }
    }
  }
};
</script>

<style>
@keyframes slideInDrawer {
  from { transform: translateX(100%); }
  to { transform: translateX(0); }
}
.drawer-enter-active, .drawer-leave-active {
  transition: opacity 0.2s;
}
.drawer-enter-from, .drawer-leave-to {
  opacity: 0;
}
</style>
