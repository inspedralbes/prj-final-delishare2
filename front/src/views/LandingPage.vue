<template>
  <div class="page-container">
    <header class="header">
      <img src="@/assets/images/delishare-logo.jpg" alt="DeliShare Logo" class="header-logo">
    </header>

    <div class="carousel">
      <div class="carousel-images" :style="{ transform: `translateX(-${currentSlide * 100}%)` }">
        <img v-for="(image, index) in carouselImages" 
             :key="index" 
             v-show="currentSlide === index" 
             :src="image.src" 
             :alt="image.alt">
      </div>
      <button class="carousel-arrow left" @click="moveSlide('left')">←</button>
      <button class="carousel-arrow right" @click="moveSlide('right')">→</button>
    </div>

    <div class="likes">
      <section class="recent-recipes">
        <h2>Más Likes</h2>
        <div class="carousel-container">
          <button class="carousel-arrow left" @click="moveSlide('left', 'likes')">←</button>
          <div class="recipe-carousel">
            <div class="recipe-card" v-for="(recipe, index) in displayedLikeRecipes" :key="index">
              <RecipeCard
                :recipeId="recipe.id"
                :title="recipe.title"
                :description="recipe.description"
                :image="recipe.image"
              />
            </div>
          </div>
          <button class="carousel-arrow right" @click="moveSlide('right', 'likes')">→</button>
        </div>
      </section>
    </div>

    <div class="recents">
      <section class="recent-recipes">
        <h2>Más Recientes</h2>
        <div class="carousel-container">
          <button class="carousel-arrow left" @click="moveSlide('left', 'recents')">←</button>
          <div class="recipe-carousel">
            <div class="recipe-card" v-for="(recipe, index) in displayedRecentRecipes" :key="index">
              <RecipeCard
                :recipeId="recipe.id"
                :title="recipe.title"
                :description="recipe.description"
                :image="recipe.image"
              />
            </div>
          </div>
          <button class="carousel-arrow right" @click="moveSlide('right', 'recents')">→</button>
        </div>
      </section>
    </div>
  </div>
</template>

<script>
import RecipeCard from '@/components/RecipeCard.vue'; // Importa el componente

import communicationManager from '@/services/communicationManager';

export default {
  components: {
    RecipeCard // Registra el componente
  },
  data() {
    return {
      currentSlide: 0,
      currentSlideLikes: 0,
      currentSlideRecents: 0,
      recipes: [],
      sortedLikeRecipes: [],
      sortedRecentRecipes: [],
      displayedLikeRecipes: [],
      displayedRecentRecipes: [],
      recipesPerPage: 3,
      totalRecipesToShow: 9,
    };
  },

  async created() {
    try {
      const data = await communicationManager.fetchRecipes();
      this.recipes = data;

      this.sortedLikeRecipes = this.recipes
        .map(recipe => ({ ...recipe, totalLikes: recipe.likes_count || 0 }))
        .sort((a, b) => b.totalLikes - a.totalLikes)
        .slice(0, this.totalRecipesToShow);

      this.sortedRecentRecipes = [...this.recipes]
        .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
        .slice(0, this.totalRecipesToShow);

      this.updateDisplayedLikeRecipes();
      this.updateDisplayedRecentRecipes();
    } catch (error) {
      console.error('Error fetching recipes from the backend:', error);
    }
  },

  methods: {
    moveSlide(direction, type) {
      if (type === 'likes') {
        const maxSlideIndex = Math.ceil(this.sortedLikeRecipes.length / this.recipesPerPage) - 1;
        this.currentSlideLikes = direction === 'left' ? Math.max(0, this.currentSlideLikes - 1) : Math.min(maxSlideIndex, this.currentSlideLikes + 1);
        this.updateDisplayedLikeRecipes();
      } else if (type === 'recents') {
        const maxSlideIndex = Math.ceil(this.sortedRecentRecipes.length / this.recipesPerPage) - 1;
        this.currentSlideRecents = direction === 'left' ? Math.max(0, this.currentSlideRecents - 1) : Math.min(maxSlideIndex, this.currentSlideRecents + 1);
        this.updateDisplayedRecentRecipes();
      }
    },

    updateDisplayedLikeRecipes() {
      const startIndex = this.currentSlideLikes * this.recipesPerPage;
      this.displayedLikeRecipes = this.sortedLikeRecipes.slice(startIndex, startIndex + this.recipesPerPage);
    },

    updateDisplayedRecentRecipes() {
      const startIndex = this.currentSlideRecents * this.recipesPerPage;
      this.displayedRecentRecipes = this.sortedRecentRecipes.slice(startIndex, startIndex + this.recipesPerPage);
    }
  }
};
</script>

<style scoped>
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

.carousel {
  position: relative;
  overflow: hidden;
  height: 220px;
}

.carousel-images {
  display: flex;
  transition: transform 0.5s ease-in-out;
}

.carousel-images img {
  width: 100%;
  height: 220px;
  object-fit: cover;
}

.carousel-container {
  display: flex;
  align-items: center;
  position: relative;
  justify-content: center;
  width: 100%;
}

.recipe-carousel {
  display: flex;
  overflow: hidden;
  width: calc(100% - 50px);
  margin-bottom: 20px;
  align-items: center;
}

.carousel-arrow {
  background-color: transparent;
  border: none;
  font-size: 1em;
  cursor: pointer;
  color: #63c132;
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 1;
}

.carousel-arrow:hover {
  color: #358600;
}

.carousel-arrow.left {
  left: 0;
}

.carousel-arrow.right {
  right: 0;
}

h2 {
  text-align: center;
  color: #358600;
  font-size: 1.5em;
  margin-bottom: 20px;
}

.recents {
  margin-bottom: 40px;
}

.recipe-link {
  text-decoration: none;
  color: inherit;
  display: block;
  width: 100%;
  height: 100%;
}
</style>
