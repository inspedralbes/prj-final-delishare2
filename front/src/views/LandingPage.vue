<template>
  <div class="min-h-screen bg-lime-50 flex flex-col">
    <!-- Hero with integrated header -->
    <section class="relative">
      <div class="absolute top-0 left-0 right-0 z-20 flex items-center justify-between px-4 py-2">
        <img src="@/assets/images/delishare.png" alt="DeliShare" class="h-10" />
        <router-link to="/notifications" class="text-lime-700 hover:text-lime-500 bg-white/80 p-1 rounded-full">
          <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
          </svg>
        </router-link>
      </div>
      <img :src="carouselImages[currentImage]" alt="Carrusel"
        class="w-full h-64 md:h-56 lg:h-72 xl:h-80 object-cover rounded-b-xl transition-all duration-300" />
      <div class="absolute inset-0 bg-gradient-to-t from-lime-500/70 to-transparent rounded-b-xl"></div>
      <div class="absolute bottom-6 left-0 w-full px-6 text-white">
        <h1 class="text-3xl md:text-2xl lg:text-3xl font-bold leading-tight drop-shadow">Descobreix noves receptes</h1>
        <p class="text-xl md:text-base leading-snug drop-shadow">Comparteix i explora receptes delicioses amb la
          comunitat</p>
      </div>
      <!-- Carousel indicators -->
      <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-3">
        <button v-for="(_, idx) in carouselImages" :key="idx" @click="currentImage = idx"
          :class="['w-4 h-4 md:w-3 md:h-3 rounded-full', currentImage === idx ? 'bg-lime-400' : 'bg-white/40']"></button>
      </div>
    </section>

    <!-- Tabs -->
    <div class="flex justify-center gap-4 mt-8 mb-6 px-6 md:gap-2 md:mt-4 md:mb-2 md:px-2">
      <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id" :class="[
        'flex-1 py-2 px-2 text-sm rounded-full font-semibold transition-all duration-300',
        'md:py-4 md:px-4 md:text-base',
        activeTab === tab.id ? 'bg-gradient-to-r from-green-500 via-lime-400 to-lime-300 text-lime-900 shadow-lg hover:shadow-xl hover:brightness-110' : 'bg-gradient-to-r from-green-100 via-lime-50 to-lime-100 text-lime-700 hover:from-green-200 hover:via-lime-100 hover:to-lime-200 hover:shadow-md'
      ]">
        {{ tab.label }}
      </button>
    </div>

    <!-- Recipes -->
    <main class="flex-1 px-6 pb-32 md:px-4 md:pb-24">
      <div v-if="activeTab === 'popular'">
        <h2 class="text-2xl md:text-xl font-bold mb-6 md:mb-2 text-lime-700">Més populars</h2>
        <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-5">
          <RecipeCard v-for="(recipe, i) in displayedPopularRecipes.slice(0, 20)" :key="i" :recipe-id="recipe.id"
            :title="recipe.title" :description="recipe.description || 'Sense descripció disponible'"
            :image="recipe.image" :class="['text-base p-4 rounded-xl shadow-md md:text-sm md:p-3 md:rounded-lg h-[280px]',
              i === displayedPopularRecipes.length - 1 ? 'mb-8' : ''
            ]" imageClass="h-52 w-full object-cover rounded-lg mb-2" />
        </div>
      </div>

      <div v-else-if="activeTab === 'recent'">
        <h2 class="text-2xl md:text-xl font-bold mb-6 md:mb-2 text-lime-700">Més recents</h2>
        <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-5">
          <RecipeCard v-for="(recipe, i) in displayedRecentRecipes.slice(0, 20)" :key="i" :recipe-id="recipe.id"
            :title="recipe.title" :description="recipe.description || 'Sense descripció disponible'"
            :image="recipe.image" :class="['text-base p-4 rounded-xl shadow-md md:text-sm md:p-3 md:rounded-lg h-[280px]',
              i === displayedRecentRecipes.length - 1 ? 'mb-8' : ''
            ]" imageClass="h-52 w-full object-cover rounded-lg mb-2" />
        </div>
      </div>

      <div v-else>
        <h2 class="text-2xl md:text-xl font-bold mb-6 md:mb-2 text-lime-700">Recomanades per a tu</h2>
        <div v-if="recommendedRecipes.length"
          class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-5">
          <RecipeCard v-for="(recipe, i) in recommendedRecipes.slice(0, 20)" :key="i" :recipe-id="recipe.id"
            :title="recipe.title" :description="recipe.description || 'Sense descripció disponible'"
            :image="recipe.image" :class="['text-base p-4 rounded-xl shadow-md md:text-sm md:p-3 md:rounded-lg h-[280px]',
              i === recommendedRecipes.length - 1 ? 'mb-8' : ''
            ]" imageClass="h-52 w-full object-cover rounded-lg mb-2" />
        </div>
        <div v-else
          class="bg-white rounded-2xl shadow-lg p-8 text-center mt-8 md:rounded-xl md:shadow-md md:p-6 md:mt-4">
          <p class="text-gray-600 mb-4 text-xl md:text-base">No tens recomanacions personalitzades encara.</p>
          <router-link to="/formulario"
            class="btn btn-primary w-full text-sm md:text-xl py-2 md:py-4 bg-gradient-to-r from-green-500 via-lime-400 to-lime-300 text-lime-900 hover:from-green-600 hover:via-lime-500 hover:to-lime-400 hover:shadow-xl hover:brightness-110 transition-all duration-300 shadow-lg">Configura
            les teves preferències</router-link>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import communicationManager from '@/services/communicationManager';
import RecipeCard from '@/components/RecipeCard.vue';

export default {
  name: 'LandingPage',
  components: {
    RecipeCard
  },
  data() {
    return {
      // Lista de todas las recetas cargadas
      recipes: [],
      // Recetas recomendadas para el usuario
      recommendedRecipes: [],
      // Recetas ordenadas por popularidad
      sortedPopularRecipes: [],
      // Recetas ordenadas por fecha
      sortedRecentRecipes: [],
      // Recetas populares mostradas actualmente
      displayedPopularRecipes: [],
      // Recetas recientes mostradas actualmente
      displayedRecentRecipes: [],
      // Pestaña activa actualmente
      activeTab: 'popular',
      // Tipo de vista (grid/list)
      viewType: 'grid',
      // Configuración de las pestañas disponibles
      tabs: [
        { id: 'popular', label: 'Més populars' },
        { id: 'recent', label: 'Més recents' },
        { id: 'recommended', label: 'Per a tu' }
      ],
      // Imágenes para el carrusel
      carouselImages: [
        new URL('@/assets/images/carrusel/image1.jpg', import.meta.url).href,
        new URL('@/assets/images/carrusel/image2.jpg', import.meta.url).href,
        new URL('@/assets/images/carrusel/image3.jpg', import.meta.url).href,
        new URL('@/assets/images/carrusel/image4.jpg', import.meta.url).href,
        new URL('@/assets/images/carrusel/image5.jpg', import.meta.url).href
      ],
      // Índice de la imagen actual del carrusel
      currentImage: 0,
      // Mensaje para el popup de notificaciones
      popupMessage: ''
    };
  },
  created() {
    this.startCarousel();
    this.fetchAllRecipes();
    this.fetchRecommendedRecipes();
  },
  methods: {
    /**
     * Inicia el carrusel automático
     * Cambia la imagen cada 5 segundos
     */
    startCarousel() {
      setInterval(() => {
        this.currentImage = (this.currentImage + 1) % this.carouselImages.length;
      }, 5000);
    },

    /**
     * Obtiene todas las recetas y las ordena
     * Maneja errores y muestra notificaciones
     */
    async fetchAllRecipes() {
      try {
        const data = await communicationManager.fetchRecipes();
        this.recipes = data;
        // Ordenar por likes
        this.sortedPopularRecipes = [...this.recipes].sort((a, b) => b.likes_count - a.likes_count);
        // Ordenar por fecha
        this.sortedRecentRecipes = [...this.recipes].sort(
          (a, b) => new Date(b.created_at) - new Date(a.created_at)
        );
        this.displayedPopularRecipes = this.sortedPopularRecipes;
        this.displayedRecentRecipes = this.sortedRecentRecipes;
      } catch (error) {
        console.error('Error fetching recipes:', error);
        this.popupMessage = 'Error al cargar las recetas';
        setTimeout(() => {
          this.popupMessage = '';
        }, 3000);
      }
    },

    /**
     * Obtiene las recetas recomendadas para el usuario
     * Maneja errores y muestra notificaciones
     */
    async fetchRecommendedRecipes() {
      try {
        const response = await communicationManager.getRecommendedRecipes();
        this.recommendedRecipes = response || [];
      } catch (error) {
        console.error('Error fetching recommended recipes:', error);
        this.popupMessage = 'Error al cargar recomendaciones';
        setTimeout(() => {
          this.popupMessage = '';
        }, 3000);
        this.recommendedRecipes = [];
      }
    },

    /**
     * Cambia a la vista de recetas populares
     */
    showPopularRecipes() {
      this.activeTab = 'popular';
    },

    /**
     * Cambia a la vista de recetas recientes
     */
    showRecentRecipes() {
      this.activeTab = 'recent';
    },

    /**
     * Cambia a la vista de recetas recomendadas
     * Carga las recomendaciones si no están disponibles
     */
    async showRecommendedRecipes() {
      this.activeTab = 'recommended';
      if (this.recommendedRecipes.length === 0) {
        await this.fetchRecommendedRecipes();
      }
    }
  }
};
</script>
