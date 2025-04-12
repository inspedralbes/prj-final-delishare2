<template>
  <div class="page-container">
    <header class="header">
      <img src="@/assets/images/delishare.png" alt="Logotip de DeliShare" class="header-logo" />
    </header>
    <router-link to="/notifications">Ver Notificaciones</router-link>

    <div v-if="popupMessage" class="popup-notification">
      {{ popupMessage }}
    </div>

    <!-- Carrusel d'imatges -->
    <div class="carousel">
      <img :src="carouselImages[currentImage]" alt="Imatge del carrusel" class="carousel-image" />
    </div>

    <div class="toggle-buttons">
      <button :class="{ active: activeTab === 'popular' }" @click="showPopularRecipes">Més populars</button>
      <button :class="{ active: activeTab === 'recent' }" @click="showRecentRecipes">Més recents</button>
      <button :class="{ active: activeTab === 'recommended' }" @click="showRecommendedRecipes">Per a tu</button>
    </div>

    <div v-if="activeTab === 'popular'" class="popular">
      <section class="recent-recipes">
        <h2>Més populars</h2>
        <div class="carousel-container">
          <div class="recipe-carousel">
            <RecipeCard v-for="(recipe, index) in displayedPopularRecipes" :key="index" :recipe-id="recipe.id"
              :title="recipe.title" :description="recipe.description || 'Sense descripció disponible'"
              :image="recipe.image" />
          </div>
        </div>
      </section>
    </div>

    <div v-else-if="activeTab === 'recent'" class="recents">
      <section class="recent-recipes">
        <h2>Més recents</h2>
        <div class="carousel-container">
          <div class="recipe-carousel">
            <RecipeCard v-for="(recipe, index) in displayedRecentRecipes" :key="index" :recipe-id="recipe.id"
              :title="recipe.title" :description="recipe.description || 'Sense descripció disponible'"
              :image="recipe.image" />
          </div>
        </div>
      </section>
    </div>

    <div v-else class="recommended">
      <section class="recent-recipes">
        <h2>Recomanades per a tu</h2>
        <div v-if="recommendedRecipes.length > 0" class="carousel-container">
          <div class="recipe-carousel">
            <RecipeCard v-for="(recipe, index) in recommendedRecipes" :key="'rec-'+index" :recipe-id="recipe.id"
              :title="recipe.title" :description="recipe.description || 'Sense descripció disponible'"
              :image="recipe.image" />
          </div>
        </div>
        <div v-else class="no-recommendations">
          <p>No tens recomanacions personalitzades encara.</p>
          <router-link to="/preferences" class="preferences-link">
            Configura les teves preferències
          </router-link>
        </div>
      </section>
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

<style scoped>
* {
  font-family: 'Times New Roman', Times, serif;
}

.page-container {
  text-align: center;
  padding: 20px;
  background-color: #fdfdff;
}

.header-logo {
  width: 200px;
  height: auto;
}

.carousel-image {
  width: 100%;
  max-height: 200px;
  object-fit: cover;
  border-radius: 10px;
}

.carousel {
  width: 100%;
  height: 250px;
  display: flex;
  justify-content: center;
  align-items: center;
  overflow: hidden;
  margin-bottom: 20px;
}

.toggle-buttons {
  display: flex;
  justify-content: center;
  margin-top: 20px;
  margin-bottom: 20px;
  gap: 10px;
  flex-wrap: wrap;
}

.toggle-buttons button {
  background: #0c0636;
  color: white;
  border: none;
  padding: 10px 15px;
  cursor: pointer;
  border-radius: 8px;
  font-size: 14px;
  font-weight: bold;
  transition: all 0.3s ease;
  min-width: 120px;
}

.toggle-buttons button.active {
  background: #322b5f;
  transform: scale(1.05);
}

.toggle-buttons button:hover {
  background: #322b5f;
}

.carousel-container {
  display: flex;
  justify-content: center;
  padding: 10px;
}

.recipe-carousel {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 10px;
  margin-bottom: 40px;
}

.no-recommendations {
  padding: 20px;
  background-color: #f8f9fa;
  border-radius: 8px;
  margin: 20px auto;
  max-width: 500px;
}

.preferences-link {
  display: inline-block;
  margin-top: 15px;
  padding: 8px 16px;
  background-color: #0c0636;
  color: white;
  text-decoration: none;
  border-radius: 4px;
  transition: background-color 0.3s;
}

.preferences-link:hover {
  background-color: #322b5f;
}

.popup-notification {
  position: fixed;
  top: 20px;
  right: 20px;
  padding: 10px 20px;
  background-color: #f8d7da;
  color: #721c24;
  border-radius: 5px;
  z-index: 1000;
  animation: fadeInOut 3s ease-in-out;
}

@keyframes fadeInOut {
  0% { opacity: 0; }
  10% { opacity: 1; }
  90% { opacity: 1; }
  100% { opacity: 0; }
}

@media (min-width: 600px) {
  .header-logo {
    width: 250px;
  }

  .toggle-buttons button {
    font-size: 16px;
    padding: 12px 20px;
  }

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

  .header-logo {
    width: 300px;
  }

  .toggle-buttons {
    gap: 15px;
  }

  .toggle-buttons button {
    font-size: 18px;
    padding: 15px 25px;
    min-width: 150px;
  }

  .carousel-container {
    padding: 20px;
  }

  .recipe-carousel {
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
  }
}
</style>