
// src/stores/gestionPinia.js
import { defineStore } from 'pinia';
import communicationManager from '@/services/communicationManager';

export const useGestionPinia = defineStore('savedRecipes', {
  state: () => ({
    savedRecipes: [],
    likedRecipes: JSON.parse(localStorage.getItem('likedRecipes')) || [], // Se carga desde localStorage
    folders: [] // Almacenará las carpetas y las recetas en ellas
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

    // Método para agregar una receta a una carpeta
    async addToFolder(folderId, recipeId) {
      let folder = this.folders.find(f => f.id === folderId);

      // Si la carpeta no existe, la creamos
      if (!folder) {
        folder = { id: folderId, recipes: [] };
        this.folders.push(folder);
      }

      if (!folder.recipes.includes(recipeId)) {
        folder.recipes.push(recipeId);

      }
    },

    async removeFromFolder(folderId, recipeId) {
      const folder = this.folders.find(f => f.id === folderId);

      if (folder) {
        // Eliminamos la receta de la carpeta si existe
        const index = folder.recipes.indexOf(recipeId);
        if (index !== -1) {
          folder.recipes.splice(index, 1);
        }

        // Actualizamos el backend después de quitar la receta
        await communicationManager.removeRecipeFromFolder(folderId, recipeId);
      }
    },

    // Método para verificar si una receta está guardada en una carpeta
    isRecipeInFolder(folderId, recipeId) {
      const folder = this.folders.find(f => f.id === folderId);
      return folder ? folder.recipes.includes(recipeId) : false;
    },

    // Método para guardar recetas en favoritos
    toggleSave(recipeId) {
      const index = this.savedRecipes.indexOf(recipeId);
      if (index > -1) {
        this.savedRecipes.splice(index, 1); // Si ya está guardada, la eliminamos
      } else {
        this.savedRecipes.push(recipeId); // Si no está guardada, la agregamos
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

      // Guardar el estado actualizado en localStorage
      localStorage.setItem('likedRecipes', JSON.stringify(this.likedRecipes));
    }
  },

  getters: {
    // Getter para las recetas guardadas
    getSavedRecipes: (state) => state.savedRecipes,
    // Getter para los likes de las recetas
    getLikedRecipes: (state) => state.likedRecipes,
    // Getter para las carpetas y sus recetas
    getFolders: (state) => state.folders
  }
});