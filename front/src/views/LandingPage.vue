<template>
  <div class="page-container">
    <header class="header">
      <img src="@/assets/images/del.png" alt="DeliShare Logo" class="header-logo">
    </header>

    <!-- Contenedor para el carrusel futuro -->
    <div class="carousel-placeholder">
      <p>Aquí irá el carrusel de imágenes</p>
    </div>

    <div class="toggle-buttons">
      <button :class="{'active': showLikes}" @click="showLikesRecipes">Más Likeadas</button>
      <button :class="{'active': !showLikes}" @click="showRecentRecipes">Más Recientes</button>
    </div>

    <div v-if="showLikes" class="likes">
      <section class="recent-recipes">
        <h2>Más Likes</h2>
        <div class="carousel-container">
          <div class="recipe-carousel">
            <div class="recipe-card" v-for="(recipe, index) in displayedLikeRecipes" :key="index">
              <router-link :to="{ name: 'InfoReceta', params: { recipeId: recipe.id } }" class="recipe-link">
                <img :src="recipe.image" :alt="recipe.title" class="recipe-image">
                <div class="recipe-info">
                  <p class="recipe-title">{{ recipe.title }}</p>
                </div>
              </router-link>
            </div>
          </div>
        </div>
      </section>
    </div>

    <div v-if="!showLikes" class="recents">
      <section class="recent-recipes">
        <h2>Más Recientes</h2>
        <div class="carousel-container">
          <div class="recipe-carousel">
            <div class="recipe-card" v-for="(recipe, index) in displayedRecentRecipes" :key="index">
              <router-link :to="{ name: 'InfoReceta', params: { recipeId: recipe.id } }" class="recipe-link">
                <img :src="recipe.image" :alt="recipe.title" class="recipe-image">
                <div class="recipe-info">
                  <p class="recipe-title">{{ recipe.title }}</p>
                </div>
              </router-link>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script>
import communicationManager from '@/services/communicationManager';

export default {
  data() {
    return {
      recipes: [],
      sortedLikeRecipes: [],
      sortedRecentRecipes: [],
      displayedLikeRecipes: [],
      displayedRecentRecipes: [],
      showLikes: true,
    };
  },

  async created() {
    try {
      const data = await communicationManager.fetchRecipes();
      this.recipes = data;

      this.sortedLikeRecipes = this.recipes
        .map(recipe => ({ ...recipe, totalLikes: recipe.likes_count || 0 }))
        .sort((a, b) => b.totalLikes - a.totalLikes);

      this.sortedRecentRecipes = [...this.recipes]
        .sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

      this.updateDisplayedLikeRecipes();
      this.updateDisplayedRecentRecipes();
    } catch (error) {
      console.error('Error fetching recipes from the backend:', error);
    }
  },

  methods: {
    showLikesRecipes() {
      this.showLikes = true;
      this.updateDisplayedLikeRecipes();
    },

    showRecentRecipes() {
      this.showLikes = false;
      this.updateDisplayedRecentRecipes();
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
.page-container {
  text-align: center;
  padding: 20px;
  background-color: #fdfdff;
}

.header-logo {
  width: 300px;
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
  margin-bottom: 20px;
}

.toggle-buttons button {
  background: #0c0636;
  color: white;
  border: none;
  padding: 10px 20px;
  margin: 0 10px;
  cursor: pointer;
  border-radius: 20px;
  font-size: 16px;
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
  gap: 15px;
  margin-bottom: 40px;
}

.recipe-card {
  width: 100%;
  max-width: 150px;
  height: 160px;
  display: flex;
  flex-direction: column;
  align-items: center;
  border: 2px solid #2f8394;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease-in-out;
  background: white;
}

.recipe-card:hover {
  transform: scale(1.05);
}

.recipe-image {
  width: 100%;
  height: 130px;
  object-fit: cover;
}

.recipe-info {
  text-align: center;
  padding: 2px;
  width: 100%;
  color: black;
  background-color: #f4f4f4;
}

.recipe-title {
  font-weight: bold;
  font-size: 14px;
  margin: 0;
}
</style>
