<template>
  <div class="min-h-screen bg-light">
    <header class="container-custom py-6">
      <img src="@/assets/images/delishare.png" alt="Logotip de DeliShare" class="h-16 mx-auto" />
    </header>

    <div v-if="popupMessage" class="fixed top-4 right-4 bg-primary text-white px-6 py-3 rounded-lg shadow-lg z-50">
      {{ popupMessage }}
    </div>

    <!-- Carrusel d'imatges -->
    <div class="relative h-64 md:h-96 overflow-hidden">
      <img :src="carouselImages[currentImage]" alt="Imatge del carrusel" 
           class="w-full h-full object-cover transition-opacity duration-500" />
    </div>

    <div class="container-custom py-8">
      <div class="flex justify-center space-x-4 mb-8">
        <button 
          @click="showPopularRecipes"
          :class="[
            'btn',
            activeTab === 'popular' ? 'btn-primary' : 'bg-gray-200 hover:bg-gray-300'
          ]"
        >
          Més populars
        </button>
        <button 
          @click="showRecentRecipes"
          :class="[
            'btn',
            activeTab === 'recent' ? 'btn-primary' : 'bg-gray-200 hover:bg-gray-300'
          ]"
        >
          Més recents
        </button>
        <button 
          @click="showRecommendedRecipes"
          :class="[
            'btn',
            activeTab === 'recommended' ? 'btn-primary' : 'bg-gray-200 hover:bg-gray-300'
          ]"
        >
          Per a tu
        </button>
      </div>

      <div v-if="activeTab === 'popular'" class="space-y-8">
        <section>
          <h2 class="text-2xl font-bold mb-6 text-dark">Més populars</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <RecipeCard 
              v-for="(recipe, index) in displayedPopularRecipes" 
              :key="index" 
              :recipe-id="recipe.id"
              :title="recipe.title" 
              :description="recipe.description || 'Sense descripció disponible'"
              :image="recipe.image" 
            />
          </div>
        </section>
      </div>

      <div v-else-if="activeTab === 'recent'" class="space-y-8">
        <section>
          <h2 class="text-2xl font-bold mb-6 text-dark">Més recents</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <RecipeCard 
              v-for="(recipe, index) in displayedRecentRecipes" 
              :key="index" 
              :recipe-id="recipe.id"
              :title="recipe.title" 
              :description="recipe.description || 'Sense descripció disponible'"
              :image="recipe.image" 
            />
          </div>
        </section>
      </div>

      <div v-else class="space-y-8">
        <section>
          <h2 class="text-2xl font-bold mb-6 text-dark">Recomanades per a tu</h2>
          <div v-if="recommendedRecipes.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <RecipeCard 
              v-for="(recipe, index) in recommendedRecipes" 
              :key="'rec-'+index" 
              :recipe-id="recipe.id"
              :title="recipe.title" 
              :description="recipe.description || 'Sense descripció disponible'"
              :image="recipe.image" 
            />
          </div>
          <div v-else class="bg-white rounded-lg shadow-md p-8 text-center max-w-2xl mx-auto">
            <p class="text-gray-600 mb-4">No tens recomanacions personalitzades encara.</p>
            <router-link 
              to="/formulario" 
              class="btn btn-primary inline-block"
            >
              Configura les teves preferències
            </router-link>
          </div>
        </section>
      </div>
    </div>
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
      recipes: [],
      recommendedRecipes: [],
      sortedPopularRecipes: [],
      sortedRecentRecipes: [],
      displayedPopularRecipes: [],
      displayedRecentRecipes: [],
      activeTab: 'popular',
      carouselImages: [
        new URL('@/assets/images/carrusel/image1.jpg', import.meta.url).href,
        new URL('@/assets/images/carrusel/image2.jpg', import.meta.url).href,
        new URL('@/assets/images/carrusel/image3.jpg', import.meta.url).href,
        new URL('@/assets/images/carrusel/image4.jpg', import.meta.url).href,
        new URL('@/assets/images/carrusel/image5.jpg', import.meta.url).href
      ],
      currentImage: 0,
      popupMessage: ''
    };
  },
  created() {
    this.startCarousel();
    this.fetchAllRecipes();
  },
  methods: {
    startCarousel() {
      setInterval(() => {
        this.currentImage = (this.currentImage + 1) % this.carouselImages.length;
      }, 3000);
    },
    async fetchAllRecipes() {
      try {
        const data = await communicationManager.fetchRecipes();
        this.recipes = data;
        this.sortedPopularRecipes = [...this.recipes].sort((a, b) => b.likes_count - a.likes_count);
        this.sortedRecentRecipes = [...this.recipes].sort(
          (a, b) => new Date(b.created_at) - new Date(a.created_at)
        );
        this.displayedPopularRecipes = this.sortedPopularRecipes;
        this.displayedRecentRecipes = this.sortedRecentRecipes;
      } catch (error) {
        console.error('Error fetching recipes:', error);
        this.popupMessage = 'Error al cargar las recetas';
      }
    },
    async fetchRecommendedRecipes() {
      try {
        const response = await communicationManager.getRecommendedRecipes();
        this.recommendedRecipes = response.recipes || [];
      } catch (error) {
        console.error('Error fetching recommended recipes:', error);
        this.popupMessage = 'Error al cargar recomendaciones';
        this.recommendedRecipes = [];
      }
    },
    showPopularRecipes() {
      this.activeTab = 'popular';
    },
    showRecentRecipes() {
      this.activeTab = 'recent';
    },
    async showRecommendedRecipes() {
      this.activeTab = 'recommended';
      if (this.recommendedRecipes.length === 0) {
        await this.fetchRecommendedRecipes();
      }
    }
  }
};
</script>