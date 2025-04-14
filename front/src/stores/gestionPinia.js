
import { defineStore } from 'pinia';
import communicationManager from '@/services/communicationManager';

export const useGestionPinia = defineStore('savedRecipes', {
  state: () => ({
    savedRecipes: [],
    likedRecipes: JSON.parse(localStorage.getItem('likedRecipes')) || [], 
    folders: [] 
  }),

  actions: {
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
        const index = folder.recipes.indexOf(recipeId);
        if (index !== -1) {
          folder.recipes.splice(index, 1);
        }

        await communicationManager.removeRecipeFromFolder(folderId, recipeId);
      }
    },

    async createFolder(folderName) {
      try {
        const newFolder = await communicationManager.createFolder(folderName);
        this.folders.push(newFolder); // Añade la nueva carpeta al estado
        return newFolder; // Devuelve la carpeta creada (opcional)
      } catch (error) {
        console.error("Error al crear carpeta:", error);
        throw error; // Puedes manejar el error en el componente
      }
    },
    isRecipeInFolder(folderId, recipeId) {
      const folder = this.folders.find(f => f.id === folderId);
      return folder ? folder.recipes.includes(recipeId) : false;
    },

    toggleSave(recipeId) {
      const index = this.savedRecipes.indexOf(recipeId);
      if (index > -1) {
        this.savedRecipes.splice(index, 1); 
      } else {
        this.savedRecipes.push(recipeId); 
      }
    },

    isRecipeSaved(recipeId) {
      return this.savedRecipes.some(recipe => recipe.id === recipeId);
    },

    isRecipeLiked(recipeId) {
      return this.likedRecipes.includes(recipeId);
    },

    toggleLike(recipeId) {
      const index = this.likedRecipes.indexOf(recipeId);
      if (index !== -1) {
        this.likedRecipes.splice(index, 1);
      } else {
        this.likedRecipes.push(recipeId);
      }
      localStorage.setItem('likedRecipes', JSON.stringify(this.likedRecipes));
    }
  },

  getters: {
    getSavedRecipes: (state) => state.savedRecipes,
    getLikedRecipes: (state) => state.likedRecipes,
    getFolders: (state) => state.folders
  }
});
