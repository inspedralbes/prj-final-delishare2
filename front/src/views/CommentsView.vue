<template>
  <div class="min-h-screen bg-lime-50 flex flex-col">
    <!-- Sección Hero con fondo animado y título -->
    <section class="relative overflow-hidden">
      <div class="bg-gradient-to-br from-lime-100 via-lime-200 to-green-200 py-16 relative">
        <!-- Overlay con efecto blur -->
        <div class="absolute inset-0 bg-white/30 backdrop-blur-sm"></div>
        <!-- Decoración de círculos animados -->
        <div class="absolute inset-0 overflow-hidden">
          <!-- Círculos decorativos con animación -->
          <div class="absolute -left-10 -top-10 w-40 h-40 bg-yellow-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 motion-safe:animate-[blob_7s_infinite]"></div>
          <div class="absolute -right-10 -top-10 w-40 h-40 bg-lime-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 motion-safe:animate-[blob_7s_infinite_2s]"></div>
          <div class="absolute -bottom-10 left-20 w-40 h-40 bg-green-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 motion-safe:animate-[blob_7s_infinite_4s]"></div>
        </div>

        <!-- Contenedor del título -->
        <div class="max-w-7xl mx-auto px-6 relative z-10">
          <div class="text-center">
            <h1 class="text-4xl tracking-tight font-extrabold text-lime-900 sm:text-5xl md:text-6xl">
              <span class="block bg-gradient-to-r from-lime-900 via-lime-700 to-green-800 bg-clip-text text-transparent">
                Gestió de Comentaris
              </span>
              <span class="block text-2xl mt-3 text-lime-700 font-medium">
                Administra els comentaris de les receptes
              </span>
            </h1>
          </div>
        </div>
      </div>
    </section>

    <!-- Sección de búsqueda y botones CRUD -->
    <div class="w-full px-6 -mt-12 sm:-mt-8 relative z-20 flex justify-center items-center gap-4">
      <!-- Contenedor del buscador -->
      <div class="w-full sm:w-2/3 md:w-1/2 lg:w-1/3 transform hover:scale-105 transition-transform duration-300">
        <div class="relative">
          <input 
            type="text" 
            v-model="searchTerm" 
            placeholder="Cerca comentaris..." 
            class="w-full pl-10 pr-6 py-2.5 text-sm text-lime-900 border-2 border-lime-300 rounded-full focus:outline-none focus:ring-4 focus:ring-lime-300/50 focus:border-lime-400 bg-white/80 backdrop-blur-sm shadow-lg" 
          />
          <!-- Icono de búsqueda -->
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="w-4 h-4 text-lime-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
        </div>
      </div>
      <!-- Botones CRUD -->
      <div class="-mt-2 sm:mt-0">
        <BotonesCrud />
      </div>
    </div>

    <!-- Contenido principal -->
    <div class="max-w-7xl mx-auto px-6 py-3 sm:py-6">
      <!-- Estado de carga -->
      <div v-if="loading" class="flex flex-col items-center justify-center py-12">
        <!-- Spinner de carga -->
        <div class="relative">
          <div class="w-16 h-16 border-4 border-lime-300 border-dashed rounded-full animate-spin"></div>
          <span class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-3xl">💬</span>
        </div>
        <p class="mt-4 text-lime-700 font-medium animate-pulse">Carregant comentaris...</p>
      </div>

      <!-- Estado de error -->
      <div v-else-if="error" class="bg-red-50 rounded-xl p-8 text-center border border-red-200 shadow-lg hover:shadow-xl transition-all duration-300 motion-safe:animate-[shake_0.5s_ease-in-out]">
        <div class="text-4xl mb-4">😕</div>
        <p class="text-red-600 mb-4 font-medium">{{ error }}</p>
        <button @click="fetchComments" class="bg-gradient-to-r from-green-500 via-lime-400 to-lime-300 text-lime-900 px-8 py-3 rounded-full hover:from-green-600 hover:via-lime-500 hover:to-lime-400 transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 font-medium">
          Tornar a intentar
        </button>
      </div>

      <!-- Estado sin resultados -->
      <div v-else-if="filteredComments.length === 0" class="text-center py-12">
        <div class="text-6xl mb-4">🔍</div>
        <p class="text-lime-700 text-xl">No s'han trobat comentaris.</p>
      </div>

      <!-- Tabla de comentarios -->
      <div v-else class="bg-white rounded-2xl shadow-lg overflow-hidden max-w-4xl mx-auto">
        <div class="overflow-x-auto">
          <table class="w-full">
            <!-- Encabezados de la tabla -->
            <thead>
              <tr class="bg-gradient-to-r from-lime-50 to-green-50">
                <th class="px-4 py-2 text-left text-xs font-semibold text-lime-900">Usuari</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-lime-900">Recepta</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-lime-900">Comentari</th>
                <th class="px-4 py-2 text-center text-xs font-semibold text-lime-900">Accions</th>
              </tr>
            </thead>
            <!-- Cuerpo de la tabla -->
            <tbody class="divide-y divide-lime-100">
              <tr v-for="comment in filteredComments" :key="comment.id" class="hover:bg-lime-50/50 transition-colors duration-200">
                <td class="px-4 py-3">
                  <div class="text-sm font-medium text-lime-900">{{ comment.user_name }}</div>
                </td>
                <td class="px-4 py-3">
                  <div class="text-sm text-lime-700">{{ comment.recipe_title }}</div>
                </td>
                <td class="px-4 py-3">
                  <div class="text-sm text-lime-700">{{ comment.comment }}</div>
                </td>
                <td class="px-4 py-3 text-center">
                  <button 
                    @click="confirmDelete(comment)" 
                    class="inline-flex items-center justify-center w-8 h-8 text-xs font-medium text-red-600 hover:text-red-800 transition-colors duration-200"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal de confirmación -->
    <div v-if="showConfirmModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 transform transition-all">
        <h3 class="text-xl font-bold text-lime-900 mb-4">Confirmar eliminació</h3>
        <p class="text-lime-700 mb-6">Estàs segur que vols eliminar el comentari de {{ currentComment?.user_name }}?</p>
        <div class="flex justify-end space-x-4">
          <button 
            @click="showConfirmModal = false" 
            class="px-4 py-2 text-sm font-medium text-lime-700 hover:text-lime-900 transition-colors duration-200"
          >
            Cancel·lar
          </button>
          <button 
            @click="executeDelete" 
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors duration-200"
          >
            Eliminar
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de éxito -->
    <div v-if="showResultModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 transform transition-all">
        <div class="text-center">
          <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <h3 class="text-xl font-bold text-lime-900 mb-2">¡Èxit!</h3>
          <p class="text-lime-700">{{ resultMessage }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import communicationManager from '../services/communicationManager';
import BotonesCrud from '@/components/BotonesCrud.vue';

export default {
  name: 'CommentsView',
  components: {
    BotonesCrud
  },
  data() {
    return {
      comments: [], // Lista de comentarios
      loading: true, // Estado de carga
      error: null, // Mensaje de error
      showConfirmModal: false, // Control de visibilidad del modal de confirmación
      showResultModal: false, // Control de visibilidad del modal de resultado
      currentComment: null, // Comentario seleccionado para eliminar
      resultMessage: '', // Mensaje de resultado
      resultError: false, // Estado de error en la operación
      searchTerm: '' // Término de búsqueda
    }
  },
  computed: {
    /**
     * Filtra los comentarios según el término de búsqueda
     * @returns {Array} Lista de comentarios filtrados
     */
    filteredComments() {
      if (!this.searchTerm) return this.comments;
      const searchLower = this.searchTerm.toLowerCase();
      return this.comments.filter(comment =>
        comment.user_name.toLowerCase().includes(searchLower) ||
        comment.recipe_title.toLowerCase().includes(searchLower) ||
        comment.comment.toLowerCase().includes(searchLower)
      );
    }
  },
  created() {
    this.fetchComments();
  },
  methods: {
    /**
     * Obtiene todos los comentarios del servidor
     * Maneja estados de carga y errores
     */
    async fetchComments() {
      try {
        this.loading = true;
        this.error = null;
        
        this.comments = await communicationManager.getAllComments();
      } catch (err) {
        console.error('Error fetching comments:', err);
        this.error = err.message || 'Error al carregar els comentaris';
      } finally {
        this.loading = false;
      }
    },
    
    /**
     * Formatea una fecha a formato local
     * @param {string} dateString - Fecha a formatear
     * @returns {string} Fecha formateada
     */
    formatDate(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
    },
 
    /**
     * Muestra el modal de confirmación para eliminar un comentario
     * @param {Object} comment - Comentario a eliminar
     */
    confirmDelete(comment) {
      this.currentComment = comment;
      this.showConfirmModal = true;
    },

    /**
     * Ejecuta la eliminación del comentario
     * Maneja estados de carga, errores y mensajes de éxito
     */
    async executeDelete() {
      this.showConfirmModal = false;
      
      try {
        await communicationManager.deleteComment(
          this.currentComment.recipe_id,
          this.currentComment.comment
        );

        await this.fetchComments();
        
        this.resultMessage = 'Comentari eliminat correctament';
        this.resultError = false;
        this.showResultModal = true;

        setTimeout(() => {
          this.showResultModal = false;
          this.resultMessage = '';
        }, 2000);

      } catch (error) {
        console.error('Error deleting comment:', error);
        
        let errorMessage = 'Error al eliminar el comentari';
        if (error && error.response && error.response.data) {
          errorMessage = error.response.data.message || errorMessage;
        } else if (error.message) {
          errorMessage = error.message;
        }
        
        this.resultMessage = errorMessage;
        this.resultError = true;
        this.showResultModal = true;

        setTimeout(() => {
          this.showResultModal = false;
          this.resultMessage = '';
        }, 2000);
      }
    }
  }
}
</script>