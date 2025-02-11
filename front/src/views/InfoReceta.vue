<template>
  <div>
    <!-- Popup de notificacions -->
    <div v-if="popupMessage" class="popup-notification">
      {{ popupMessage }}
    </div>

    <div class="recipe-detail">
      <button class="back-button" @click="goBack">← Tornar</button>

      <h1 class="recipe-title">{{ recipe.title }}</h1>
      <p>
        <strong>Autor: </strong>
        <router-link :to="'/user/' + recipe.user_id" class="creator-link">
          @{{ recipe.creador }}
        </router-link>
      </p>

      <div class="recipe-image-container">
        <img :src="recipe.image" :alt="recipe.title" class="recipe-image" />
      </div>

      <div class="button-container">
        <!-- Botó per mostrar la selecció de carpeta -->
        <button @click="checkRecipeInFolder" :disabled="isButtonDisabled">
          <img
            :src="getSaveCarpeta"
            :alt="isSavedCarpeta ? 'Treure la recepta de la carpeta' : 'Desar la recepta a la carpeta'"
            class="button-icon"
          />
        </button>

        <!-- Modal per triar la carpeta -->
        <div
          v-if="showFolderSelection"
          :class="{ show: showFolderSelection }"
          class="save-options-modal"
        >
          <div class="save-options-content">
            <h3>On vols desar la recepta?</h3>
            <select v-model="selectedFolderId">
              <option
                v-for="folder in userFolders"
                :key="folder.id"
                :value="folder.id"
              >
                {{ folder.name }}
              </option>
            </select>
            <button @click="saveToFolder">Desar a la carpeta</button>
            <button @click="hideModal" class="cancel-button">Cancel·lar</button>
          </div>
        </div>

        <!-- Popup si la recepta ja està desada en una carpeta -->
        <div v-if="showFolderAlert" class="folder-alert">
          <div class="folder-alert-content">
            <p>La recepta ja està desada en una de les teves carpetes.</p>
            <button @click="addToAnotherFolder">Afegir a una altra carpeta</button>
            <button @click="cancelAddToFolder">Cancel·lar</button>
          </div>
        </div>

        <!-- Botó de desar/eliminar de guardades -->
        <button @click="saveToSavedRecipes(recipe.id)" :disabled="isButtonDisabled">
          <img
            :src="getSaveIcon"
            :alt="isSaved ? 'Treure la recepta de les guardades' : 'Desar la recepta a les guardades'"
            class="button-icon"
          />
        </button>
        <button @click="toggleLike(recipe.id)">
          <img
            :src="getLikeIcon"
            :alt="isLiked ? 'Treure el like' : 'Posar el like'"
            class="button-icon"
          />
        </button>
      </div>
    </div>

    <div class="recipe-info">
      <p class="recipe-description">
        {{ recipe.description || 'Sense descripció' }}
      </p>
      <button class="back-button" @click="toggleExtraInfo">
        {{ isExtraInfoVisible ? 'Amagar informació extra' : 'Informació extra' }}
      </button>
      <div v-if="isExtraInfoVisible" class="extra-info">
        <p><strong>Temps de preparació:</strong> {{ recipe.prep_time }} minuts</p>
        <p><strong>Temps de cocció:</strong> {{ recipe.cook_time }} minuts</p>
        <p><strong>Racions:</strong> {{ recipe.servings }}</p>
        <p><strong>Likes:</strong> {{ recipe.likes_count }} ❤️</p>
      </div>

      <div class="recipe-section">
        <h2>Ingredients</h2>
        <ul class="ingredients-list">
          <li v-for="(ingredient, index) in recipe.ingredients" :key="index">
            {{ ingredient }}
          </li>
        </ul>
      </div>
      <div class="recipe-section">
        <h2>Passos</h2>
        <ol class="steps-list">
          <li v-for="(step, index) in recipe.steps" :key="index">
            {{ step }}
          </li>
        </ol>
      </div>
    </div>

    <div class="recipe-section">
      <h2>Comentaris</h2>
      <ul class="comments-list">
        <li v-for="(comment, index) in comments" :key="index" class="comment-item">
          <div class="comment-header">
            <p class="comment-user">
              <strong>{{ comment.name || 'Usuari desconegut' }}:</strong>
            </p>
          </div>
          <p class="comment-text">{{ comment.comment }}</p>
        </li>
      </ul>

      <div class="comment-input-container">
        <textarea
          v-model="newComment"
          placeholder="Escriu un comentari..."
          class="comment-textarea"
        ></textarea>
        <button @click="addComment" class="comment-button">Comentar</button>
      </div>
    </div>
  </div>
</template>

<script>
import folder1 from '@/assets/images/folder1.png';
import folder2 from '@/assets/images/folder2.png';
import guardarRecetas from '@/assets/images/guardarRecetas.png';
import guardados from '@/assets/images/guardados.png';
import unlike from '@/assets/images/unlike.png';
import like from '@/assets/images/like.png';
import { useGestionPinia } from '@/stores/gestionPinia';
import communicationManager from '@/services/communicationManager';
import UserProfile from '@/components/UserProfile.vue';

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
        creador: '',
      },
      comments: [],
      newComment: '',
      selectedFolderId: null,
      userFolders: [],
      isExtraInfoVisible: false,
      isButtonDisabled: false,
      isSavedCarpeta: false, // Estat per controlar si la recepta està desada a la carpeta
      showFolderSelection: false,
      showFolderAlert: false, // Variable per mostrar el popup d'alerta
      popupMessage: '', // Missatge a mostrar en el popup
    };
  },
  components: {
    UserProfile,
  },
  computed: {
    getSaveCarpeta() {
      return this.isSavedCarpeta ? folder2 : folder1;
    },
    getSaveIcon() {
      return this.isSaved ? guardarRecetas : guardados;
    },
    getLikeIcon() {
      return this.isLiked ? unlike : like;
    },
    isSaved() {
      const gestionPinia = useGestionPinia();
      // Verifica si la recepta està desada al store
      return gestionPinia.getSavedRecipes.includes(this.recipe.id);
    },
    isLiked() {
      const gestionPinia = useGestionPinia();
      return gestionPinia.isRecipeLiked(this.recipe.id);
    },
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
    // Mètode per mostrar el popup de notificacions
    showPopup(message) {
      this.popupMessage = message;
      setTimeout(() => {
        this.popupMessage = '';
      }, 5000); // S'oculta als 5 segons
    },
    checkRecipeInFolder() {
      const gestionPinia = useGestionPinia();
      // Verifica si la recepta ja està desada en alguna carpeta
      const folder = gestionPinia.folders.find((f) =>
        f.recipes.includes(this.recipe.id)
      );
      if (folder) {
        this.showFolderAlert = true;
      } else {
        this.showFolderSelection = true;
      }
    },
    addToAnotherFolder() {
      // Tanca el popup i mostra el modal per triar una altra carpeta
      this.showFolderAlert = false;
      this.showFolderSelection = true;
      this.selectedFolderId = null;
    },
    cancelAddToFolder() {
      // Tanca el popup sense fer canvis
      this.showFolderAlert = false;
    },
    hideModal() {
      this.showFolderSelection = false;
    },
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
    async saveToSavedRecipes() {
      const recipeId = this.recipe.id;
      try {
        // Guardem o eliminem la recepta segons el seu estat actual
        const wasSaved = this.isSaved;
        await communicationManager.toggleSaveRecipe(recipeId);

        // Actualitzem l'estat al store
        const savedRecipesStore = useGestionPinia();
        savedRecipesStore.toggleSave(recipeId);

        if (wasSaved) {
          this.showPopup('Recepta eliminada de les guardades');
        } else {
          this.showPopup('Recepta desada a les teves guardades');
        }
      } catch (error) {
        console.error('Error al guardar la recepta:', error);
      }
    },
    async toggleSave(recipeId) {
      this.isButtonDisabled = true;
      try {
        const gestionPinia = useGestionPinia();
        gestionPinia.toggleSave(recipeId);
        this.showPopup('Estat de desat actualitzat');
      } catch (error) {
        console.error('Error al guardar o treure la recepta:', error);
      } finally {
        this.isButtonDisabled = false;
      }
    },
    async addComment() {
      if (!this.newComment.trim()) return;

      try {
        const user = await communicationManager.getUser();
        await communicationManager.addComment(this.recipe.id, this.newComment);

        this.comments.push({
          comment: this.newComment,
          name: user.name,
        });

        this.newComment = '';
        // Mostra popup en afegir un comentari
        this.showPopup('Comentari afegit');
      } catch (error) {
        console.error('Error en afegir el comentari:', error);
      }
    },
    async toggleLike(recipeId) {
      const gestionPinia = useGestionPinia();
      await gestionPinia.toggleLike(recipeId);
      // Actualitzem el comptador segons el nou estat
      if (this.isLiked) {
        this.recipe.likes_count++;
        this.showPopup('Has posat el like');
      } else {
        this.recipe.likes_count--;
        this.showPopup('Like tret');
      }
    },
    toggleExtraInfo() {
      this.isExtraInfoVisible = !this.isExtraInfoVisible;
    },
    // Desar recepta a la carpeta
    async saveToFolder() {
      if (!this.selectedFolderId) {
        this.showPopup('Si us plau, selecciona una carpeta');
        return;
      }

      const gestionPinia = useGestionPinia();
      const isInFolder = gestionPinia.isRecipeInFolder(
        this.selectedFolderId,
        this.recipe.id
      );

      if (isInFolder) {
        this.showPopup('La recepta ja està desada en aquesta carpeta');
        this.showFolderSelection = false;
        return;
      }

      try {
        const recipeId = this.recipe.id;
        await communicationManager.saveRecipeToFolder(
          this.selectedFolderId,
          recipeId
        );
        gestionPinia.addToFolder(this.selectedFolderId, recipeId);
        this.isSavedCarpeta = gestionPinia.isRecipeInFolder(
          this.selectedFolderId,
          recipeId
        );
        const folderName = this.userFolders.find(
          (f) => f.id === this.selectedFolderId
        ).name;
        this.showPopup(`Recepta desada a la carpeta: ${folderName}`);
        this.showFolderSelection = false;
      } catch (error) {
        console.error('Error al guardar la recepta a la carpeta:', error);
      }
    },
  },
  watch: {
    // Actualitza l'estat de isSavedCarpeta quan es canvia la carpeta seleccionada
    selectedFolderId(newFolderId) {
      const savedRecipesStore = useGestionPinia();
      this.isSavedCarpeta = savedRecipesStore.isRecipeInFolder(
        newFolderId,
        this.recipe.id
      );
    },
  },
};
</script>

<style scoped>
/* Estils generals del component */
.recipe-detail {
  max-width: 800px;
  margin: 20px auto;
  padding: 20px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.back-button {
  background: transparent;
  color: #004080;
  border: 2px solid #004080;
  padding: 8px 16px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.back-button:hover {
  background: #004080;
  color: #fff;
}

.recipe-image-container {
  display: flex;
  justify-content: center;
  margin: 20px 0;
}

.recipe-image {
  max-width: 100%;
  border-radius: 12px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.extra-info {
  background: #f5f5f5;
  padding: 10px;
  border-radius: 8px;
}

button {
  background-color: #ffffff;
  color: white;
  border: none;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.button-container {
  display: flex;
  width: 100%;
  gap: 15px;
  margin-top: 15px;
}

.button-container button {
  background: transparent;
  border: none;
  cursor: pointer;
  transition: transform 0.2s ease;
  display: flex;
}

.button-icon {
  width: 25px;
  height: 25px;
}

@keyframes slideIn {
  from {
    transform: translateY(-50px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.save-options-content h3 {
  font-size: 18px;
  margin-bottom: 20px;
}

.save-options-content button {
  margin-top: 10px;
  background-color: #3498db;
  color: white;
  padding: 10px;
  border: none;
  border-radius: 5px;
}

.save-options-content button:hover {
  background-color: #3498db;
}

.cancel-button:hover {
  background-color: #bbb;
}

.save-options-content select {
  width: 100%;
  padding: 8px;
  margin-bottom: 20px;
  border-radius: 5px;
  border: 1px solid #ddd;
}

.save-options-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.save-options-content {
  background: #fff;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.folder-alert {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.folder-alert-content {
  background: #fff;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.folder-alert button {
  margin-top: 10px;
  background-color: #3498db;
  color: white;
  padding: 10px;
  border: none;
  border-radius: 5px;
}

.folder-alert button:hover {
  background-color: #2980b9;
}

.recipe-section {
  margin-top: 20px;
  padding: 20px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.comments-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.comment-item {
  background: #f9f9f9;
  padding: 10px;
  border-radius: 8px;
  margin-bottom: 10px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.comment-header {
  display: flex;
  align-items: center;
}

.comment-user {
  font-weight: bold;
  color: #004080;
}

.comment-text {
  margin-top: 5px;
  font-size: 14px;
  color: #333;
}

.comment-input-container {
  display: flex;
  flex-direction: column;
  margin-top: 1px;
}

.comment-textarea {
  height: 50px;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #ccc;
  resize: none;
  font-size: 14px;
}

.comment-button {
  background-color: #004080;
  color: white;
  padding: 10px;
  border-radius: 10px;
  cursor: pointer;
  font-size: 16px;
  border: none;
  margin-top: 5px;
  margin-bottom: 50px;
  transition: background-color 0.3s ease;
}

.comment-button:hover {
  background-color: #005bb5;
}

.creator-link {
  color: #3498db;
  text-decoration: none;
  font-weight: bold;
  transition: color 0.3s ease, text-decoration 0.3s ease;
}

.creator-link:hover {
  color: #1abc9c;
  text-decoration: underline;
}

/* Estils per al popup de notificacions */
.popup-notification {
  position: fixed;
  top: 20px;
  right: 20px;
  background: #3498db;
  color: #fff;
  padding: 10px 20px;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  animation: fadeIn 0.5s ease;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
