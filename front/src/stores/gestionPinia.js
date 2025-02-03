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

    // Verifica si una receta está guardada
    isRecipeSaved(recipeId) {
      return this.savedRecipes.some(recipe => recipe.id === recipeId);
    },

    // Verifica si una receta está en los likes
    isRecipeLiked(recipeId) {
      return this.likedRecipes.includes(recipeId);
    },

    // Agrega o elimina un like a una receta
    toggleLike(recipeId) {
      const index = this.likedRecipes.indexOf(recipeId);
      if (index !== -1) {
        // Si el like ya está presente, lo eliminamos
        this.likedRecipes.splice(index, 1);
      } else {
        // Si no está, lo añadimos
        this.likedRecipes.push(recipeId);
      }

      // guardar el estado actualizado en localStorage
      localStorage.setItem('likedRecipes', JSON.stringify(this.likedRecipes));
    }
  },

  getters: {
    // Getter para las recetas guardadas
    getSavedRecipes: (state) => state.savedRecipes,
    // Getter par los likes de las recetas
    getLikedRecipes: (state) => state.likedRecipes,
  }
});
