<template>
  <div v-if="!authStore.token" class="min-h-screen bg-gradient-to-br from-lime-100 via-lime-200 to-green-200 flex items-center justify-center p-4">
    <div class="bg-white/90 backdrop-blur-sm rounded-2xl p-8 max-w-md w-full shadow-xl text-center">
      <p class="text-lime-900 text-lg mb-6">Per veure els detalls d'aquesta recepta, has d'iniciar sessió</p>
      <button @click="goToLogin" class="bg-gradient-to-r from-lime-500 via-green-500 to-emerald-500 text-white px-6 py-3 rounded-lg hover:from-lime-600 hover:via-green-600 hover:to-emerald-600 transition-all duration-300 shadow-lg hover:shadow-xl">
        Ir a Login
      </button>
    </div>
  </div>

  <div v-else class="min-h-screen bg-gradient-to-br from-lime-100 via-lime-200 to-green-200">
    <!-- Popup de notificaciones -->
    <div v-if="popupMessage" class="fixed top-5 right-5 bg-gradient-to-r from-lime-500 to-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in">
      {{ popupMessage }}
    </div>

    <div class="max-w-4xl mx-auto px-4 py-8 mb-24">
      <!-- Botón de retroceso -->
      <button @click="goBack" class="mb-6 flex items-center text-lime-900 hover:text-lime-700 transition-colors duration-200">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Tornar enrere
      </button>

      <!-- Información principal de la receta -->
      <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl overflow-hidden mb-8">
        <div class="p-6">
          <h1 class="text-3xl font-bold bg-gradient-to-r from-lime-900 via-green-800 to-emerald-900 bg-clip-text text-transparent mb-4">{{ recipe.title }}</h1>
          <p class="text-lime-700 mb-6">
            <span class="font-semibold">Autor:</span>
            <router-link :to="'/user/' + recipe.user_id" class="text-lime-600 hover:text-lime-800 transition-colors duration-200">
              @{{ recipe.creador }}
            </router-link>
          </p>

          <!-- Imagen de la receta -->
          <div class="relative rounded-xl overflow-hidden mb-6 shadow-lg">
            <img :src="recipe.image" :alt="recipe.title" class="w-full h-[300px] object-cover" />
          </div>

          <!-- Botones de acción -->
          <div class="flex flex-wrap gap-4 mb-6">
            <button @click="downloadPDF" class="w-10 h-10 bg-gradient-to-r from-lime-500 to-green-500 text-white rounded-lg hover:from-lime-600 hover:to-green-600 transition-all duration-300 shadow-md hover:shadow-lg">
              <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
              </svg>
            </button>

            <button @click="checkRecipeInFolder" :disabled="isButtonDisabled" class="w-10 h-10 bg-gradient-to-r from-lime-500 to-green-500 text-white rounded-lg hover:from-lime-600 hover:to-green-600 transition-all duration-300 shadow-md hover:shadow-lg">
              <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
              </svg>
            </button>

            <button @click="saveToSavedRecipes(recipe.id)" :disabled="isButtonDisabled" class="w-10 h-10 bg-gradient-to-r from-lime-500 to-green-500 text-white rounded-lg hover:from-lime-600 hover:to-green-600 transition-all duration-300 shadow-md hover:shadow-lg">
              <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
              </svg>
            </button>

            <button @click="toggleLike(recipe.id)" class="w-10 h-10 bg-gradient-to-r from-lime-500 to-green-500 text-white rounded-lg hover:from-lime-600 hover:to-green-600 transition-all duration-300 shadow-md hover:shadow-lg">
              <svg class="w-6 h-6 mx-auto" :fill="!isLiked ? 'white' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
              </svg>
            </button>
          </div>

          <!-- Descripción -->
          <div class="prose max-w-none mb-8">
            <p class="text-lime-800">{{ recipe.description || 'Sense descripció' }}</p>
          </div>

          <!-- Información extra -->
          <div class="mb-8">
            <button @click="toggleExtraInfo" class="w-full flex items-center justify-between px-4 py-3 bg-gradient-to-r from-lime-100 to-green-100 rounded-lg hover:from-lime-200 hover:to-green-200 transition-all duration-300 shadow-md">
              <span class="font-medium text-lime-900">Informació extra</span>
              <svg class="w-5 h-5 transform transition-transform duration-200" :class="{ 'rotate-180': isExtraInfoVisible }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <div v-if="isExtraInfoVisible" class="mt-4 p-4 bg-gradient-to-r from-lime-50 to-green-50 rounded-lg shadow-inner">
              <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center">
                  <p class="text-sm text-lime-600">Temps de preparació</p>
                  <p class="font-semibold text-lime-900">{{ recipe.prep_time }} min</p>
                </div>
                <div class="text-center">
                  <p class="text-sm text-lime-600">Temps de cocció</p>
                  <p class="font-semibold text-lime-900">{{ recipe.cook_time }} min</p>
                </div>
                <div class="text-center">
                  <p class="text-sm text-lime-600">Racions</p>
                  <p class="font-semibold text-lime-900">{{ recipe.servings }}</p>
                </div>
                <div class="text-center">
                  <p class="text-sm text-lime-600">Likes</p>
                  <p class="font-semibold text-lime-900">{{ recipe.likes_count }} ❤️</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Ingredientes -->
          <div class="mb-8">
            <h2 class="text-2xl font-bold bg-gradient-to-r from-lime-900 via-green-800 to-emerald-900 bg-clip-text text-transparent mb-4">Ingredients</h2>
            <ul class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <li v-for="(ingredient, index) in recipe.ingredients" :key="index" 
                  class="flex items-center p-3 bg-gradient-to-r from-lime-50 to-green-50 rounded-lg shadow-sm">
                <span class="w-2 h-2 bg-gradient-to-r from-lime-500 to-green-500 rounded-full mr-3"></span>
                <span class="text-lime-900">{{ ingredient.name }} - {{ ingredient.quantity }} {{ ingredient.unit }}</span>
              </li>
            </ul>
          </div>

          <!-- Pasos -->
          <div class="mb-8">
            <h2 class="text-2xl font-bold bg-gradient-to-r from-lime-900 via-green-800 to-emerald-900 bg-clip-text text-transparent mb-4">Passos</h2>
            <ol class="space-y-4">
              <li v-for="(step, index) in recipe.steps" :key="index" 
                  class="flex p-4 bg-gradient-to-r from-lime-50 to-green-50 rounded-lg shadow-sm">
                <span class="flex-shrink-0 w-8 h-8 bg-gradient-to-r from-lime-500 to-green-500 text-white rounded-full flex items-center justify-center mr-4">
                  {{ index + 1 }}
                </span>
                <span class="text-lime-900">{{ step }}</span>
              </li>
            </ol>
          </div>

          <!-- Información nutricional -->
          <div v-if="recipe.nutrition" class="mb-8">
            <h3 class="text-2xl font-bold bg-gradient-to-r from-lime-900 via-green-800 to-emerald-900 bg-clip-text text-transparent mb-4">Informació Nutricional</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div class="p-4 bg-gradient-to-r from-lime-50 to-green-50 rounded-lg text-center shadow-sm">
                <p class="text-sm text-lime-600">Calories</p>
                <p class="font-semibold text-lime-900">{{ recipe.nutrition.calories || 'N/A' }}</p>
              </div>
              <div class="p-4 bg-gradient-to-r from-lime-50 to-green-50 rounded-lg text-center shadow-sm">
                <p class="text-sm text-lime-600">Proteïnes</p>
                <p class="font-semibold text-lime-900">{{ recipe.nutrition.protein ? `${recipe.nutrition.protein}g` : 'N/A' }}</p>
              </div>
              <div class="p-4 bg-gradient-to-r from-lime-50 to-green-50 rounded-lg text-center shadow-sm">
                <p class="text-sm text-lime-600">Carbohidrats</p>
                <p class="font-semibold text-lime-900">{{ recipe.nutrition.carbs ? `${recipe.nutrition.carbs}g` : 'N/A' }}</p>
              </div>
              <div class="p-4 bg-gradient-to-r from-lime-50 to-green-50 rounded-lg text-center shadow-sm">
                <p class="text-sm text-lime-600">Greixos</p>
                <p class="font-semibold text-lime-900">{{ recipe.nutrition.fats ? `${recipe.nutrition.fats}g` : 'N/A' }}</p>
              </div>
            </div>
          </div>

          <!-- Video -->
          <div v-if="recipe.video" class="mb-8">
            <h3 class="text-2xl font-bold bg-gradient-to-r from-lime-900 via-green-800 to-emerald-900 bg-clip-text text-transparent mb-4">Video</h3>
            <div class="relative rounded-xl overflow-hidden shadow-lg">
              <video controls class="w-full">
                <source :src="recipe.video" type="video/mp4">
                Tu navegador no soporta el elemento de video.
              </video>
            </div>
          </div>

          <!-- Comentarios -->
          <div class="mb-8">
            <h3 class="text-2xl font-bold bg-gradient-to-r from-lime-900 via-green-800 to-emerald-900 bg-clip-text text-transparent mb-4">Comentaris</h3>
            <div class="mb-6">
              <textarea 
                v-model="newComment" 
                placeholder="Escriu un comentari..." 
                class="w-full p-4 border-2 border-lime-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-300 focus:border-lime-400 resize-none shadow-sm"
                rows="3"
              ></textarea>
              <button 
                @click="addComment" 
                class="mt-3 px-6 py-2 bg-gradient-to-r from-lime-500 via-green-500 to-emerald-500 text-white rounded-lg hover:from-lime-600 hover:via-green-600 hover:to-emerald-600 transition-all duration-300 shadow-md hover:shadow-lg"
              >
                Comentar
              </button>
            </div>
            <div class="space-y-4">
              <div v-for="(comment, index) in comments" :key="index" 
                   class="p-4 bg-gradient-to-r from-lime-50 to-green-50 rounded-lg shadow-sm">
                <p class="font-medium text-lime-900">
                  <span class="text-lime-600">{{ comment.name || 'Usuari desconegut' }}</span>
                  <span class="text-lime-800">: {{ comment.comment }}</span>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de selección de carpeta -->
    <div v-if="showFolderSelection" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white/90 backdrop-blur-sm rounded-2xl p-8 max-w-md w-full mx-4 shadow-xl">
        <h3 class="text-xl font-bold bg-gradient-to-r from-lime-900 via-green-800 to-emerald-900 bg-clip-text text-transparent mb-4">On vols desar la recepta?</h3>
        <select 
          v-model="selectedFolderId" 
          class="w-full p-3 border-2 border-lime-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-300 focus:border-lime-400 mb-4 shadow-sm"
        >
          <option v-for="folder in userFolders" :key="folder.id" :value="folder.id">
            {{ folder.name }}
          </option>
        </select>
        <div class="flex justify-end gap-4">
          <button 
            @click="hideModal" 
            class="px-4 py-2 text-lime-700 hover:text-lime-900 transition-colors duration-200"
          >
            Cancel·lar
          </button>
          <button 
            @click="saveToFolder" 
            class="px-4 py-2 bg-gradient-to-r from-lime-500 via-green-500 to-emerald-500 text-white rounded-lg hover:from-lime-600 hover:via-green-600 hover:to-emerald-600 transition-all duration-300 shadow-md hover:shadow-lg"
          >
            Desar
          </button>
        </div>
      </div>
    </div>

    <!-- Alerta de carpeta -->
    <div v-if="showFolderAlert" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white/90 backdrop-blur-sm rounded-2xl p-8 max-w-md w-full mx-4 shadow-xl">
        <p class="text-lime-900 mb-6">La recepta ja està desada en una de les teves carpetes.</p>
        <div class="flex justify-end gap-4">
          <button 
            @click="cancelAddToFolder" 
            class="px-4 py-2 text-lime-700 hover:text-lime-900 transition-colors duration-200"
          >
            Cancel·lar
          </button>
          <button 
            @click="addToAnotherFolder" 
            class="px-4 py-2 bg-gradient-to-r from-lime-500 via-green-500 to-emerald-500 text-white rounded-lg hover:from-lime-600 hover:via-green-600 hover:to-emerald-600 transition-all duration-300 shadow-md hover:shadow-lg"
          >
            Afegir a una altra carpeta
          </button>
        </div>
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
      if (!this.recipe.id) {
        this.showPopup('No se pudo obtener el ID de la receta');
        return;
      }

      try {
        this.showPopup('Generando PDF...');
        await communicationManager.downloadPDF(this.recipe.id);
        this.showPopup('PDF descargado con éxito');
      } catch (error) {
        console.error('Error detallado:', error);
        console.error('Response:', error.response);
        console.error('Status:', error.response?.status);
        console.error('Data:', error.response?.data);
        
        let errorMessage = 'Error al generar el PDF';
        
        // Intentar obtener el mensaje de error más específico
        if (error.response?.status === 500) {
          errorMessage += ': Error interno del servidor. Por favor, intenta de nuevo más tarde.';
        } else if (error.response?.data?.message) {
          errorMessage += `: ${error.response.data.message}`;
        } else if (error.response?.status === 401) {
          errorMessage += ': No autorizado';
        } else if (error.response?.status === 404) {
          errorMessage += ': Receta no encontrada';
        } else if (error.response?.status) {
          errorMessage += `: ${error.response.status}`;
        } else {
          errorMessage += ': Error desconocido. Por favor, intenta de nuevo.';
        }
        
        this.showPopup(errorMessage);
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
      if (!this.newComment.trim()) {
        this.showPopup('El comentario no puede estar vacío');
        return;
      }

      try {
        // Obtener el usuario actual
        const user = await communicationManager.getUser();
        if (!user) {
          throw new Error('No se pudo obtener el usuario');
        }

        // Añadir el comentario
        const response = await communicationManager.addComment(this.recipe.id, this.newComment);
        if (!response || !response.success) {
          throw new Error('Error al añadir el comentario');
        }

        // Actualizar lista de comentarios
        this.comments.unshift({
          id: response.data?.id || Date.now(), // Usar ID del servidor o timestamp como fallback
          comment: this.newComment,
          name: user.name || 'Usuario',
          created_at: new Date().toISOString(),
          user_id: user.id
        });

        // Limpiar el campo de entrada
        this.newComment = '';

        // Mostrar mensaje de éxito
        this.showPopup('Comentario añadido con éxito');

        // Actualizar los comentarios del servidor
        await this.pollComments();

      } catch (error) {
        console.error('Error al añadir comentario:', error);
        this.showPopup(error.message || 'Error al añadir el comentario');
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
        this.showPopup('La recepta ya está desada en esta carpeta');
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

<style>
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.3s ease-out;
}
</style>