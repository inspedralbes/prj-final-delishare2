// src/stores/savedRecipesStore.js
import { defineStore } from 'pinia';
import communicationManager from '@/services/communicationManager';

export const useSavedRecipesStore = defineStore('savedRecipes', {
  state: () => ({
    savedRecipes: [],  
    likedRecipes: JSON.parse(localStorage.getItem('likedRecipes')) || []
  }),

  actions: {
    // Método para cargar las recetas guardadas desde el backend
    async loadSavedRecipes() {
      try {
        const recipes = await communicationManager.getSavedRecipes();
        this.savedRecipes = recipes; 
      } catch (error) {
        console.error('Error al cargar recetas guardadas:', error);
      }
    },

    // Método para añadir o eliminar una receta de las guardadas
    async toggleSave(recipeId) {
      try {
        await communicationManager.toggleSaveRecipe(recipeId); 
        const index = this.savedRecipes.findIndex(recipe => recipe.id === recipeId);
        
        if (index !== -1) {
          // Si la receta ya está guardada, la eliminamos
          this.savedRecipes.splice(index, 1);
        } else {
          // Si no está guardada, la añadimos
          const recipeDetails = await communicationManager.fetchRecipeDetails(recipeId);
          this.savedRecipes.push(recipeDetails);
        }
      } catch (error) {
        console.error('Error al guardar o quitar la receta:', error);
      }
    },
    async toggleLike(recipeId) {
      // Verificar si la receta ya está en el estado de "likedRecipes"
      const isLiked = this.likedRecipes.includes(recipeId);

      try {
        if (isLiked) {
          // Si la receta ya está "likeada", la quitamos
          await communicationManager.unlikeRecipe(recipeId);
          this.likedRecipes = this.likedRecipes.filter(id => id !== recipeId);
        } else {
          // Si no está "likeada", la agregamos
          await communicationManager.likeRecipe(recipeId);
          this.likedRecipes.push(recipeId);
        }

        // Actualizamos el almacenamiento local para mantener los cambios persistentes
        localStorage.setItem('likedRecipes', JSON.stringify(this.likedRecipes));
      } catch (error) {
        console.error('Error toggling like:', error);
      }
    },

    // Verifica si una receta está guardada
    isRecipeSaved(recipeId) {
      return this.savedRecipes.some(recipe => recipe.id === recipeId);
    },

    isRecipeLiked(recipeId) {
      return this.likedRecipes.includes(recipeId);
    }
  },

  getters: {
    // Getter para las recetas guardadas
    getSavedRecipes: (state) => state.savedRecipes,
    // Getter par los likes de las recetas
    getLikedRecipes: (state) => state.likedRecipes,
  }
});
