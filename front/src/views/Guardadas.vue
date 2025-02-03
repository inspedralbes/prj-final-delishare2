<template>
    <div>
      <h1>Recetas Guardadas</h1>
      <div v-if="loading">Cargando...</div>
      <div v-else>
        <ul>
          <li v-for="recipe in savedRecipes" :key="recipe.id">
            <router-link :to="`/info/${recipe.id}`">{{ recipe.title }}</router-link>
            <button @click="removeSavedRecipe(recipe.id)">Eliminar</button>
          </li>
        </ul>
        <p v-if="savedRecipes.length === 0">No tienes recetas guardadas.</p>
      </div>
    </div>
  </template>
  
  <script>
  import communicationManager from '@/services/communicationManager';
  import { ref, onMounted } from 'vue';
  
  export default {
    setup() {
      const savedRecipes = ref([]);
      const loading = ref(true);
  
      const fetchSavedRecipes = async () => {
        try {
          const response = await communicationManager.getSavedRecipes();  
          savedRecipes.value = response;
        } catch (error) {
          console.error('Error al obtener recetas guardadas:', error);
        } finally {
          loading.value = false;
        }
      };
  
      const removeSavedRecipe = async (recipeId) => {
        try {
          await communicationManager.toggleSaveRecipe(recipeId);  
          savedRecipes.value = savedRecipes.value.filter(r => r.id !== recipeId);  
        } catch (error) {
          console.error('Error al eliminar la receta:', error);
        }
      };
  
      onMounted(() => {
        fetchSavedRecipes();
      });
  
      return { savedRecipes, loading, removeSavedRecipe };
    }
  };
  </script>
  
  
  <style scoped>
  ul {
    list-style-type: none;
    padding: 0;
  }
  li {
    display: flex;
    justify-content: space-between;
    padding: 10px;
    border-bottom: 1px solid #ccc;
  }
  button {
    background-color: red;
    color: white;
    border: none;
    padding: 5px;
    cursor: pointer;
  }
  button:hover {
    background-color: darkred;
  }
  </style>
  