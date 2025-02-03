<template>
    <div class="recipe-detail">
      <button class="back-button" @click="goBack">← Volver</button>
  
      <!-- Botón de guardar (solo disponible una vez) -->
      <button v-if="!isSaved && !isButtonDisabled" @click="toggleSave(recipe.id)" :disabled="isButtonDisabled">
        Guardar receta
      </button>
  
      <!-- Botón de like -->
      <button v-if="!isLiked" @click="likeRecipe(recipe.id)">
        Dar like 
      </button>
      <button v-if="isLiked" @click="unlikeRecipe(recipe.id)">
        Quitar like 
      </button>
  
      <h1 class="recipe-title">{{ recipe.title }}</h1>
  
      <p><strong>Creador:</strong> {{ recipe.creador }}</p> 
  
      <div class="recipe-image-container">
        <img :src="recipe.image" :alt="recipe.title" class="recipe-image">
      </div>
  
      <div class="recipe-info">
        <p class="recipe-description"><strong>Descripción:</strong> {{ recipe.description || 'Sin descripción' }}</p>
        <div class="recipe-section">
          <h2>Ingredientes</h2>
          <ul class="ingredients-list">
            <li v-for="(ingredient, index) in recipe.ingredients" :key="index">{{ ingredient }}</li>
          </ul>
        </div>
        <div class="recipe-section">
          <h2>Pasos</h2>
          <ol class="steps-list">
            <li v-for="(step, index) in recipe.steps" :key="index">{{ step }}</li>
          </ol>
        </div>
        <div class="recipe-meta">
          <p><strong>Tiempo de preparación:</strong> {{ recipe.prep_time }} mins</p>
          <p><strong>Tiempo de cocción:</strong> {{ recipe.cook_time }} mins</p>
          <p><strong>Porciones:</strong> {{ recipe.servings }} personas</p>
          <p><strong>Likes:</strong> {{ recipe.likes_count }} ❤️</p>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import { useSavedRecipesStore } from '@/stores/gestionPinia';
  import { computed, onMounted } from 'vue';
  import communicationManager from '@/services/communicationManager';
  
  export default {
    data() {
      return {
        recipe: {
          title: '',
          image: '',
          description: '',
          ingredients: [],
          steps: [],
          prep_time: 0,
          cook_time: 0,
          servings: 0,
          likes_count: 0,
        },
        isButtonDisabled: false,
      };
    },
    computed: {
      isSaved() {
        const savedRecipesStore = useSavedRecipesStore();
        return savedRecipesStore.isRecipeSaved(this.recipe.id);
      },
      isLiked() {
        const savedRecipesStore = useSavedRecipesStore();
        return savedRecipesStore.isRecipeLiked(this.recipe.id);
      }
    },
    async created() {
      const recipeId = this.$route.params.recipeId;
      try {
        const data = await communicationManager.fetchRecipeDetails(recipeId);
        this.recipe = data;
        const savedRecipesStore = useSavedRecipesStore();
        await savedRecipesStore.loadSavedRecipes();
      } catch (error) {
        console.error('Error fetching recipe details:', error);
      }
    },
    methods: {
      goBack() {
        this.$router.go(-1);
      },
      async likeRecipe(recipeId) {
        try {
          const response = await communicationManager.likeRecipe(recipeId);
          if (response.message === 'Ya has dado like a esta receta.') {
            alert('Ya has dado like a esta receta.');
          } else {
            this.recipe.likes_count++; 
            const savedRecipesStore = useSavedRecipesStore();
            await savedRecipesStore.toggleLike(recipeId); 
          }
        } catch (error) {
          console.error('Error dando like:', error);
        }
      },
      async unlikeRecipe(recipeId) {
        try {
          const response = await communicationManager.unlikeRecipe(recipeId);
          if (response.message === 'No has dado like a esta receta.') {
            alert('No has dado like a esta receta.');
          } else {
            this.recipe.likes_count--; 
            const savedRecipesStore = useSavedRecipesStore();
            await savedRecipesStore.toggleLike(recipeId); 
          }
        } catch (error) {
          console.error('Error quitando like:', error);
        }
      },
      async toggleSave(recipeId) {
        this.isButtonDisabled = true;
        const savedRecipesStore = useSavedRecipesStore();
        try {
          await savedRecipesStore.toggleSave(recipeId);
        } catch (error) {
          console.error('Error al guardar o quitar la receta:', error);
        } finally {
          this.isButtonDisabled = false; 
        }
      }
    }
  };
  </script>
  
  <style scoped>
  button {
    background-color: #63c132;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    margin-right: 10px;
    transition: background-color 0.3s ease;
  }
  
  button:hover {
    background-color: #358600;
  }
  
  button:disabled {
    background-color: #ccc;
    cursor: not-allowed;
  }
  
  .recipe-detail {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    height: 80vh;
    overflow-y: auto; 
  
  .recipe-image {
    max-width: 100%;
    height: auto;
    width: 70%;
    margin: 0 auto;
    display: block;
  }
  
  .recipe-info {
    padding-bottom: 20px; 
  }
  
  .recipe-meta {
    margin-top: 20px; 
  }
  }
  </style>
  