<template>
  <!-- Main container with lime background -->
  <div class="min-h-screen bg-lime-50 flex flex-col pb-24">
    <!-- Hero section with animated background and search title -->
    <section class="relative overflow-hidden">
      <div class="bg-gradient-to-br from-lime-100 via-lime-200 to-green-200 py-16 relative">
        <div class="absolute inset-0 bg-white/30 backdrop-blur-sm"></div>
        <!-- Animated circles decoration -->
        <div class="absolute inset-0 overflow-hidden">
          <div class="absolute -left-10 -top-10 w-40 h-40 bg-yellow-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
          <div class="absolute -right-10 -top-10 w-40 h-40 bg-lime-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
          <div class="absolute -bottom-10 left-20 w-40 h-40 bg-green-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
        </div>

        <div class="max-w-7xl mx-auto px-6 relative z-10">
          <div class="text-center">
            <h1 class="text-4xl tracking-tight font-extrabold text-lime-900 sm:text-5xl md:text-6xl">
              <span class="block bg-gradient-to-r from-lime-900 via-lime-700 to-green-800 bg-clip-text text-transparent">
                Cercar Receptes
              </span>
              <span class="block text-2xl mt-3 text-lime-700 font-medium">
                Explora, filtra i descobreix receptes delicioses
              </span>
            </h1>
          </div>
        </div>
      </div>
    </section>

    <!-- Search section with floating elements and filter button -->
    <div v-if="authStore.isAuthenticated" class="w-full px-6 -mt-12 sm:-mt-8 relative z-20 flex justify-center">
      <div class="w-full sm:w-5/6 md:w-4/5 lg:w-3/4 xl:w-2/3 2xl:w-1/2 transform hover:scale-105 transition-transform duration-300">
        <div class="relative flex items-center gap-3">
          <div class="relative flex-1">
            <input 
              type="text" 
              v-model="searchQuery" 
              placeholder="Cerca receptes per títol..." 
              class="w-full pl-10 pr-6 py-3 text-base text-lime-900 border-2 border-lime-300 rounded-full focus:outline-none focus:ring-4 focus:ring-lime-300/50 focus:border-lime-400 bg-white/80 backdrop-blur-sm shadow-lg" 
            />
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="w-5 h-5 text-lime-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
          </div>
          <button 
            class="bg-gradient-to-r from-[#22c55e] to-[#a3e635] border-none rounded-full w-10 h-10 flex items-center justify-center shadow-[0_2px_8px_0_rgba(163,230,53,0.10)] cursor-pointer transition-all duration-200 hover:shadow-[0_4px_16px_0_rgba(163,230,53,0.18)] hover:bg-gradient-to-r hover:from-[#16a34a] hover:to-[#bef264] flex-shrink-0 -mt-3"
            @click="drawerOpen = true" 
            aria-label="Obrir filtres"
          >
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect y="4" width="24" height="2.5" rx="1.25" fill="white"/>
              <rect y="10.75" width="24" height="2.5" rx="1.25" fill="white"/>
              <rect y="17.5" width="24" height="2.5" rx="1.25" fill="white"/>
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Main content section - only visible when authenticated -->
    <div v-if="authStore.isAuthenticated" class="flex-1">
      
      <div v-if="loading" class="my-5 text-base text-gray-700">Carregant receptes...</div>
      <div v-else>
        <div v-if="filteredRecipes.length === 0" class="my-5 text-base text-gray-700">
          No hi ha receptes disponibles.
        </div>
        <div v-else class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-5 mt-0 md:mt-5 mb-[60px] px-6">
          <RecipeCard
            v-for="(recipe, i) in filteredRecipes"
            :key="recipe.id"
            :recipeId="recipe.id"
            :title="recipe.title"
            :description="recipe.description || 'Descripció no disponible'"
            :image="recipe.image"
            :class="['text-base p-4 rounded-xl shadow-md md:text-sm md:p-3 md:rounded-lg h-[280px]',
              i === filteredRecipes.length - 1 ? 'mb-8' : ''
            ]"
            imageClass="h-52 w-full object-cover rounded-lg mb-2"
          />
        </div>
      </div>

      <!-- Drawer de filtros -->
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

    <!-- Authentication modal - shown when user is not logged in -->
    <teleport to="body">
      <transition name="modal">
        <div v-if="!authStore.isAuthenticated" class="pointer-events-none fixed left-0 right-0 top-0 bottom-20 flex items-center justify-center z-50">
          <div class="pointer-events-auto bg-white rounded-2xl shadow-lg p-8 text-center w-full max-w-xs md:max-w-md">
            <p class="text-lime-900 text-lg font-semibold mb-6">Per veure i cercar receptes, has d'iniciar sessió</p>
            <button @click="goToLogin" class="w-full py-3 bg-gradient-to-r from-green-500 via-lime-400 to-lime-300 text-lime-900 rounded-xl font-semibold shadow hover:from-green-600 hover:via-lime-500 hover:to-lime-400 hover:brightness-110 transition-all duration-200 text-base">Iniciar Sessió</button>
          </div>
        </div>
      </transition>
    </teleport>
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
    // Navigates to login page with current path as redirect
    goToLogin() {
      this.router.push({ 
        name: 'login',
        query: { redirect: this.$route.fullPath }
      });
    },

    // Checks authentication status and loads recipe data if authenticated
    async checkAuthAndLoadData() {
      if (this.authStore.isAuthenticated) {
        await this.fetchRecipes();
      }
    },

    // Fetches all recipes from the API
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

    // Handlers for different filter types
    // Updates filtered recipes based on category filter
    handleFiltradoPorCategoria(filteredRecipes) {
      console.log('Recetas filtradas recibidas:', filteredRecipes); // Debug log
      this.filteredRecipes = filteredRecipes;
    },

    // Updates filtered recipes based on cuisine filter
    handleFiltradoPorCuisine(filteredRecipes) {
      this.filteredRecipes = filteredRecipes;
    },

    // Updates filtered recipes based on time filter
    handleFiltradoPorTiempo(filteredRecipes) {
      this.filteredRecipes = filteredRecipes;
    },

    // Updates filtered recipes based on ingredient filter
    handleFiltradoPorIngrediente(filteredRecipes) {
      this.filteredRecipes = filteredRecipes;
    },

    // Updates filtered recipes based on difficulty filter
    handleFiltradoPorDificultad(filteredRecipes) {
      this.filteredRecipes = filteredRecipes;
    },
  },
  watch: {
    // Watches for authentication status changes
    'authStore.isAuthenticated'(newVal) {
      if (newVal) {
        this.checkAuthAndLoadData();
      }
    },

    // Watches for search query changes and filters recipes accordingly
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

@keyframes blob {
  0% {
    transform: translate(0px, 0px) scale(1);
  }
  33% {
    transform: translate(30px, -50px) scale(1.1);
  }
  66% {
    transform: translate(-20px, 20px) scale(0.9);
  }
  100% {
    transform: translate(0px, 0px) scale(1);
  }
}

.animate-blob {
  animation: blob 7s infinite;
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}
</style>
