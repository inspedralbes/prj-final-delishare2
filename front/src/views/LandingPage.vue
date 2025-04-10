<template>
  <div class="page-container">
    <header class="header">
      <img src="@/assets/images/delishare.png" alt="Logotip de DeliShare" class="header-logo" />
    </header>
    <router-link to="/notifications">Ver Notificaciones</router-link>

    <div v-if="popupMessage" class="popup-notification">
      {{ popupMessage }}
    </div> <!-- Carrusel d'imatges -->
    <div class="carousel">
      <img :src="carouselImages[currentImage]" alt="Imatge del carrusel" class="carousel-image" />
    </div>

    <div class="toggle-buttons">
      <button :class="{ active: showLikes }" @click="showLikesRecipes">Més populars</button>
      <button :class="{ active: !showLikes }" @click="showRecentRecipes">Més recents</button>
    </div>

    <div v-if="showLikes" class="likes">
      <section class="recent-recipes">
        <h2>Més populars</h2>
        <div class="carousel-container">
          <div class="recipe-carousel">
            <RecipeCard v-for="(recipe, index) in displayedLikeRecipes" :key="index" :recipe-id="recipe.id"
              :title="recipe.title" :description="recipe.description || 'Sense descripció disponible'"
              :image="recipe.image" />
          </div>
        </div>
      </section>
    </div>

    <div v-else class="recents">
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
      sortedLikeRecipes: [],
      sortedRecentRecipes: [],
      displayedLikeRecipes: [],
      displayedRecentRecipes: [],
      showLikes: true,
      carouselImages: [
        new URL('@/assets/images/carrusel/image1.jpg', import.meta.url).href,
        new URL('@/assets/images/carrusel/image2.jpg', import.meta.url).href,
        new URL('@/assets/images/carrusel/image3.jpg', import.meta.url).href,
        new URL('@/assets/images/carrusel/image4.jpg', import.meta.url).href,
        new URL('@/assets/images/carrusel/image5.jpg', import.meta.url).href
      ],
      currentImage: 0,
    };
  },
  created() {
    this.startCarousel();
    this.fetchRecipes();
  },
  methods: {
    startCarousel() {
      setInterval(() => {
        this.currentImage = (this.currentImage + 1) % this.carouselImages.length;
      }, 3000);
    },
    async fetchRecipes() {
      try {
        const data = await communicationManager.fetchRecipes();
        this.recipes = data;
        this.sortedLikeRecipes = this.recipes.sort((a, b) => b.likes_count - a.likes_count);
        this.sortedRecentRecipes = [...this.recipes].sort(
          (a, b) => new Date(b.created_at) - new Date(a.created_at)
        );
        this.updateDisplayedLikeRecipes();
        this.updateDisplayedRecentRecipes();
      } catch (error) {
        console.error('Error fetching recipes:', error);
      }
    },
    showLikesRecipes() {
      this.showLikes = true;
    },
    showRecentRecipes() {
      this.showLikes = false;
    },
    updateDisplayedLikeRecipes() {
      this.displayedLikeRecipes = this.sortedLikeRecipes;
    },
    updateDisplayedRecentRecipes() {
      this.displayedRecentRecipes = this.sortedRecentRecipes;
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

.carousel-placeholder {
  width: 100%;
  height: 150px;
  background-color: #e6ebec;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 15px;
  border-radius: 8px;
  color: white;
  font-weight: bold;
}

.toggle-buttons {
  display: flex;
  justify-content: center;
  margin-top: 20px;
  margin-bottom: 20px;
}

.toggle-buttons button {
  background: #0c0636;
  color: white;
  border: none;
  padding: 10px 15px;
  margin: 0 5px;
  cursor: pointer;
  border-radius: 8px;
  font-size: 14px;
  font-weight: bold;
  transition: background 0.3s ease;
}

.toggle-buttons button.active {
  background: #0c0636;
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

@media (min-width: 600px) {
  .header-logo {
    width: 250px;
  }

  .toggle-buttons button {
    font-size: 16px;
    padding: 12px 20px;
    margin: 0 8px;
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
    display: flex;
    justify-content: center;
  }

  .toggle-buttons button {
    font-size: 18px;
    padding: 15px 25px;
  }

  .carousel-container {
    padding: 20px;
  }

  .recipe-carousel {
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
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
  }
}
</style>
