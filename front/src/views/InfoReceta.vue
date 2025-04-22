<template>
  <div v-if="!authStore.token" class="auth-error-container">
    <div class="auth-error-message">
      <p>Debes iniciar sesión para ver los detalles de esta receta</p>
      <button @click="goToLogin">Ir a Login</button>
    </div>

  </div>

  <div v-else>
    <!-- Popup de notificacions -->
    <div v-if="popupMessage" class="popup-notification">
      {{ popupMessage }}
    </div>

    <div class="recipe-detail">
      <button class="back-button" @click="goBack">← </button>


      <h1 class="recipe-title">{{ recipe.title }}</h1>
      <p>
        <strong>Autor: </strong>
        <router-link :to="'/user/' + recipe.user_id" class="creator-link">
          @{{ recipe.creador }}
        </router-link>
      </p>

      <div class="media-container">
        <div class="recipe-image-container">
          <img :src="recipe.image" :alt="recipe.title" class="recipe-image" />
        </div>
      </div>

      <div class="button-container">
        <!-- Botón de descargar receta en PDF -->
        <button @click="downloadPDF" class="download-button" title="Descargar receta en PDF">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="black"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
            <polyline points="7 10 12 15 17 10"></polyline>
            <line x1="12" y1="15" x2="12" y2="3"></line>
          </svg>
        </button>

        <!-- Botó per mostrar la selecció de carpeta -->
        <button @click="checkRecipeInFolder" :disabled="isButtonDisabled">
          <img :src="getSaveCarpeta"
            :alt="isSavedCarpeta ? 'Treure la recepta de la carpeta' : 'Desar la recepta a la carpeta'"
            class="button-icon" />
        </button>

        <!-- Modal per triar la carpeta -->
        <div v-if="showFolderSelection" :class="{ show: showFolderSelection }" class="save-options-modal">
          <div class="save-options-content">
            <h3>On vols desar la recepta?</h3>
            <select v-model="selectedFolderId">
              <option v-for="folder in userFolders" :key="folder.id" :value="folder.id">
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
          <img :src="getSaveIcon"
            :alt="isSaved ? 'Treure la recepta de les guardades' : 'Desar la recepta a les guardades'"
            class="button-icon" />
        </button>
        <button @click="toggleLike(recipe.id)">
          <img :src="getLikeIcon" :alt="isLiked ? 'Treure el like' : 'Posar el like'" class="button-icon" />
        </button>
      </div>
    </div>
    <div class="recipe-section">

      <div class="recipe-info">
        <p class="recipe-description">
          {{ recipe.description || 'Sense descripció' }}
        </p>
      </div>
      <div class="recipe-section">

        <button class="back-button" @click="toggleExtraInfo">
          {{ isExtraInfoVisible ? 'Amagar informació extra' : 'Informació extra' }}
        </button>
        <div v-if="isExtraInfoVisible" class="extra-info">
          <p><strong>Temps de preparació:</strong> {{ recipe.prep_time }} minuts</p>
          <p><strong>Temps de cocció:</strong> {{ recipe.cook_time }} minuts</p>
          <p><strong>Racions:</strong> {{ recipe.servings }}</p>
          <p><strong>Likes:</strong> {{ recipe.likes_count }} ❤️</p>
        </div>
      </div>
      <div class="recipe-section">
        <h2>Ingredients</h2>
        <ul class="ingredients-list">
          <li v-for="(ingredient, index) in recipe.ingredients" :key="index">
            {{ ingredient.name }} - {{ ingredient.quantity }} {{ ingredient.unit }}
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
      <div class="recipe-section">
        <div v-if="recipe.nutrition">
          <h3>Informació Nutricional</h3>
          <p><strong>Calories:</strong> {{ recipe.nutrition.calories || 'N/A' }}</p>
          <p>
            <strong>Proteïnes:</strong>
            {{ recipe.nutrition.protein ? `${recipe.nutrition.protein}g` : 'N/A' }}
          </p>
          <p>
            <strong>Carbohidrats:</strong>
            {{ recipe.nutrition.carbs ? `${recipe.nutrition.carbs}g` : 'N/A' }}
          </p>
          <p>
            <strong>Greixos:</strong>
            {{ recipe.nutrition.fats ? `${recipe.nutrition.fats}g` : 'N/A' }}
          </p>
        </div>
        <!-- Mostrar video si existe -->
        <div v-if="recipe.video" class="video-container">
          <video controls class="recipe-video">
            <source :src="recipe.video" type="video/mp4">
            Tu navegador no soporta el elemento de video.
          </video>
        </div>
      </div>

    </div>

    <div class="recipe-section">

      <div class="comment-input-container">
        <textarea v-model="newComment" placeholder="Escriu un comentari..." class="comment-textarea"></textarea>
        <button @click="addComment" class="comment-button">Comentar</button>
      </div>
      <ul class="comments-list">
        <li v-for="(comment, index) in comments" :key="index" class="comment-item">
          <div class="comment-header">
            <p class="comment-user">
              <strong>{{ comment.name || 'Usuari desconegut' }} : {{ comment.comment }}</strong>
            </p>
          </div>
        </li>
      </ul>
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
import { useAuthStore } from '@/stores/authStore';

export default {
  setup() {
    const authStore = useAuthStore();
    return { authStore };
  },
  data() {
    return {
      recipe: {
        id: null,
        title: '',
        image: '',
        video: null, // Añadido campo para el video
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
      isSavedCarpeta: false,
      showFolderSelection: false,
      showFolderAlert: false,
      popupMessage: '',
      likesInterval: null,
      commentsInterval: null,
      lastCommentUpdate: null
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
      return gestionPinia.getSavedRecipes.includes(this.recipe.id);
    },
    isLiked() {
      const gestionPinia = useGestionPinia();
      return gestionPinia.isRecipeLiked(this.recipe.id);
    },
  },

  methods: {
    async downloadPDF() {
      try {
        const recipeId = this.recipe.id;
        const response = await communicationManager.downloadPDF(recipeId);

        const blob = new Blob([response], { type: 'application/pdf' });
        const url = window.URL.createObjectURL(blob);

        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `${this.recipe.title || 'recepta'}.pdf`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        this.showPopup('PDF descarregat amb èxit');
      } catch (error) {
        console.error('Error al descarregar el PDF:', error);
        this.showPopup('Error al descarregar el PDF');
      }
    },
    async pollComments() {
      try {
        const response = await communicationManager.fetchComments(this.recipe.id);

        // Verificar si hay cambios en los comentarios
        if (this.comments.length !== response.length ||
          JSON.stringify(this.comments) !== JSON.stringify(response)) {
          this.comments = response;
          // Mostrar notificación si es un nuevo comentario (opcional)
          if (this.comments.length > 0 &&
            (!this.lastCommentUpdate ||
              new Date(this.comments[0].updated_at) > new Date(this.lastCommentUpdate))) {
            this.showPopup('Nuevo comentario disponible');
          }
          this.lastCommentUpdate = this.comments[0]?.updated_at;
        }
      } catch (error) {
        console.error('Error al obtener comentarios:', error);
      }
    },
    setupPolling() {
      // Polling inicial
      this.pollComments();

      // Configurar intervalo para polling cada 3 segundos
      this.commentsInterval = setInterval(async () => {
        await this.pollComments();
      }, 3000);
    },

    clearPolling() {
      if (this.commentsInterval) {
        clearInterval(this.commentsInterval);
      }
    },


    clearPolling() {
      if (this.commentsInterval) {
        clearInterval(this.commentsInterval);
      }
    },

    setupAuthWatcher() {
      this.$watch(
        () => this.authStore.token,
        (newToken) => {
          if (newToken && this.$route.params.recipeId) {
            this.loadRecipeData();
          } else if (!newToken) {
            this.redirectToLogin();
          }
        }
      );
    },

    async checkAuthAndLoadData() {
      if (!this.authStore.token) {
        this.redirectToLogin();
        return;
      }
      await this.loadRecipeData();
    },

    redirectToLogin() {
      this.$router.push({
        name: 'LandingPage',
        query: {
          authError: 'Debes iniciar sesión para ver detalles de recetas',
          redirect: this.$route.fullPath
        }
      });
    },

    async loadRecipeData() {
      try {
        const recipeId = String(this.$route.params.recipeId);
        const data = await communicationManager.fetchRecipeDetails(recipeId);
        this.recipe = data;
        await this.loadComments();
        this.userFolders = await communicationManager.fetchUserFolders();
      } catch (error) {
        console.error("Error loading recipe:", error);
        if (error.message.includes('Unauthorized')) {
          this.authStore.clearAuth();
          this.redirectToLogin();
        }
      }
    },

    goToLogin() {
      this.$router.push({
        name: 'login',
        query: { redirect: this.$route.fullPath }
      });
    },

    showPopup(message) {
      this.popupMessage = message;
      setTimeout(() => {
        this.popupMessage = '';
      }, 5000);
    },

    checkRecipeInFolder() {
      const gestionPinia = useGestionPinia();
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
      this.showFolderAlert = false;
      this.showFolderSelection = true;
      this.selectedFolderId = null;
    },

    cancelAddToFolder() {
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
    async checkNotifications() {
      try {
        const response = await communicationManager.fetchNotifications(); // crea esta función en tu servicio
        response.forEach((notif) => {
          this.showPopup(notif.message);
        });
      } catch (error) {
        console.error('Error al obtener notificaciones:', error);
      }
    },

    async saveToSavedRecipes() {
      const recipeId = this.recipe.id;
      try {
        const wasSaved = this.isSaved;
        await communicationManager.toggleSaveRecipe(recipeId);

        const savedRecipesStore = useGestionPinia();
        savedRecipesStore.toggleSave(recipeId);

        this.showPopup(wasSaved ?
          'Recepta eliminada de les guardades' :
          'Recepta desada a les teves guardades');
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
        const response = await communicationManager.addComment(this.recipe.id, this.newComment);

        // Actualizar lista de comentarios inmediatamente después de añadir uno nuevo
        this.comments.unshift({
          comment: this.newComment,
          name: user.name,
          updated_at: new Date().toISOString()
        });

        // Enviar notificación
        await communicationManager.createNotification({
          user_id: this.recipe.user_id,
          recipe_id: this.recipe.id,
          type: 'comment',
        });

        this.newComment = '';
        this.showPopup('Comentario añadido');

        // Forzar actualización inmediata
        await this.pollComments();
      } catch (error) {
        console.error('Error al añadir comentario:', error);
        this.showPopup('Error al añadir comentario');
      }
    },


    async toggleLike(recipeId) {
      try {
        const response = await communicationManager.toggleLike(recipeId);

        const gestionPinia = useGestionPinia();
        gestionPinia.toggleLike(recipeId);

        this.recipe.likes_count = response.likes_count;

        this.showPopup(response.liked ? 'Has posat el like' : 'Like tret');

        if (!this.likesInterval) {
          this.likesInterval = setInterval(async () => {
            const likesData = await communicationManager.getLikes(recipeId);
            this.recipe.likes_count = likesData.likes_count;
          }, 30000);
        }
      } catch (error) {
        console.error('Error al dar like:', error);
      }
    },

    toggleExtraInfo() {
      this.isExtraInfoVisible = !this.isExtraInfoVisible;
    },

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
        this.isSavedCarpeta = true;

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
  created() {
    this.setupPolling();
  },
  beforeUnmount() {
    this.clearPolling();
  },
  async created() {
    this.setupPolling();
    this.notificationsInterval = setInterval(this.checkNotifications, 10000); // cada 10s
    this.setupAuthWatcher();
    await this.checkAuthAndLoadData();
  },
  beforeUnmount() {
    if (this.notificationsInterval) {
      clearInterval(this.notificationsInterval);
    }

    if (this.likesInterval) {
      clearInterval(this.likesInterval);
    }
  },
  watch: {
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
.auth-error-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

.auth-error-message {
  background-color: #f8d7da;
  border: 1px solid #f5c6cb;
  color: #721c24;
  padding: 20px;
  border-radius: 5px;
  text-align: center;
  max-width: 400px;
}

.auth-error-message button {
  background-color: #721c24;
  color: white;
  border: none;
  padding: 8px 15px;
  border-radius: 4px;
  margin-top: 10px;
  cursor: pointer;
}

.auth-error {
  padding: 20px;
  background: #ffebee;
  color: #c62828;
  text-align: center;
  margin: 20px;
  border-radius: 4px;
}

.auth-error a {
  color: #d32f2f;
  font-weight: bold;
}

* {
  font-family: 'Times New Roman', Times, serif;
}

.recipe-description {
  margin-left: 10px;
}

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
  color: #0c0636;
  border: 2px solid #0c0636;
  padding: 8px 16px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.back-button:hover {
  background: #004080;
  color: #fff;
}

.media-container {
  margin: 20px 0;
}

.recipe-image-container {
  display: flex;
  justify-content: center;
}

.recipe-image {
  max-width: 100%;
  max-height: 400px;
  border-radius: 12px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.video-container {
  width: 100%;
  display: flex;
  justify-content: center;
}

.recipe-video {
  max-width: 100%;
  max-height: 400px;
  border-radius: 12px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.extra-info {
  background: transparent;
  color: #004080;
  border: 2px solid #004080;
  padding: 10px;
  border-radius: 8px;
  margin-top: 10px;
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
  margin-top: 10px;
  padding: 20px;
  background: #fff;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.comments-list {
  list-style: none;
  padding: 0;
  margin: 0;
  margin-bottom: 30px;

}

.comment-item {
  background: #f9f9f9;
  border-radius: 8px;
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
  background-color: #0c0636;
  color: white;
  padding: 10px;
  border-radius: 10px;
  cursor: pointer;
  font-size: 16px;
  border: none;
  margin-top: 10px;
  transition: background-color 0.3s ease;
}

.comment-button:hover {
  background-color: #322b5f;
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