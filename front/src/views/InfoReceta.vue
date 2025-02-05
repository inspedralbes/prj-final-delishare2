<template>
  <div class="recipe-detail">
    <button class="back-button" @click="goBack">← Volver</button>

    <!-- Botón de guardar receta normal -->
    <button @click="saveToSavedRecipes(recipe.id)">Guardar receta en Guardadas</button>

    <!-- Botón de guardar receta en carpeta -->
    <button @click="showFolderSelection = true">Guardar receta en mi carpeta</button>

    <!-- Selector de carpetas (mostrar solo si se activa la opción) -->
    <div v-if="showFolderSelection">
      <select v-model="selectedFolderId">
        <option v-for="folder in userFolders" :key="folder.id" :value="folder.id">{{ folder.name }}</option>
      </select>
      <button @click="saveToFolder">Guardar en carpeta</button>
      <button @click="showFolderSelection = false">Cancelar</button>
    </div>

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

    <!-- Sección de comentarios -->
    <div class="recipe-section">
      <h2>Comentarios</h2>
      <ul class="comments-list">
        <li v-for="(comment, index) in comments" :key="index">
          <p><strong>{{ comment.name || 'Usuario desconocido' }}:</strong> {{ comment.comment }}</p>
        </li>
      </ul>

      <textarea v-model="newComment" placeholder="Escribe un comentario..."></textarea>
      <button @click="addComment">Comentar</button>
    </div>
  </div>
</template>

<script>
import { useSavedRecipesStore } from '@/stores/gestionPinia';
import { ref, computed } from 'vue';
import communicationManager from '@/services/communicationManager';

export default {
  data() {
    return {
      recipe: {
        id: null,
        title: '',
        image: '',
        description: '',
        ingredients: [],
        steps: [],
        prep_time: 0,
        cook_time: 0,
        servings: 0,
        likes_count: 0,
        creador: '',  // Añadido para manejar el creador de la receta
      },
      comments: [],
      newComment: '',
      showFolderSelection: false,
      selectedFolderId: null,
      userFolders: [],
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
      await this.loadComments();
      this.userFolders = await communicationManager.fetchUserFolders();
    } catch (error) {
      console.error('Error fetching recipe details:', error);
    }
  },
  methods: {
    goBack() {
      this.$router.go(-1);
    },

    async loadComments() {
      try {
        const data = await communicationManager.fetchComments(this.recipe.id);
        this.comments = data;
      } catch (error) {
        console.error('Error fetching comments:', error);
      }
    },

    async addComment() {
      if (!this.newComment.trim()) return;

      try {
        await communicationManager.addComment(this.recipe.id, this.newComment);
        this.comments.push({ comment: this.newComment, name: 'Usuario desconocido' });
        this.newComment = '';  // Limpiar campo
      } catch (error) {
        console.error('Error adding comment:', error);
      }
    },

    async saveToSavedRecipes() {
      const recipeId = this.recipe.id;  // Usamos el ID de la receta actual
      try {
        // Llama al método correcto para guardar o quitar la receta
        await communicationManager.toggleSaveRecipe(recipeId);
        alert('Receta guardada en tus favoritos');
      } catch (error) {
        console.error('Error al guardar receta:', error);
      }
    },

    async saveToFolder() {
      if (!this.selectedFolderId) {
        alert('Por favor, selecciona una carpeta');
        return;
      }

      try {
        // Usamos el ID de la receta y la carpeta seleccionada
        const recipeId = this.recipe.id;  // Usamos el ID de la receta actual
        await communicationManager.saveRecipeToFolder(this.selectedFolderId, recipeId);
        alert(`Receta guardada en la carpeta: ${this.userFolders.find(f => f.id === this.selectedFolderId).name}`);
        this.showFolderSelection = false; // Cerrar selector de carpetas
      } catch (error) {
        console.error('Error al guardar receta en carpeta:', error);
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
}

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

.comments-list {
  list-style-type: none;
  padding: 0;
}

.comments-list li {
  margin-bottom: 10px;
}

textarea {
  width: 100%;
  padding: 10px;
  margin-top: 10px;
  border-radius: 8px;
  border: 1px solid #ccc;
}
</style>