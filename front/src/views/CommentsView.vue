<template>
  <div class="min-h-screen bg-lime-50 flex flex-col">
    <!-- Hero Section with animated background -->
    <section class="relative overflow-hidden">
      <div class="bg-gradient-to-br from-lime-100 via-lime-200 to-green-200 py-16 relative">
        <div class="absolute inset-0 bg-white/30 backdrop-blur-sm"></div>
        <!-- Animated circles decoration -->
        <div class="absolute inset-0 overflow-hidden">
          <div class="absolute -left-10 -top-10 w-40 h-40 bg-yellow-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 motion-safe:animate-[blob_7s_infinite]"></div>
          <div class="absolute -right-10 -top-10 w-40 h-40 bg-lime-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 motion-safe:animate-[blob_7s_infinite_2s]"></div>
          <div class="absolute -bottom-10 left-20 w-40 h-40 bg-green-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 motion-safe:animate-[blob_7s_infinite_4s]"></div>
        </div>

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

    <!-- Search Section -->
    <div class="w-full px-6 -mt-12 sm:-mt-8 relative z-20 flex justify-center items-center gap-4">
      <div class="w-full sm:w-2/3 md:w-1/2 lg:w-1/3 transform hover:scale-105 transition-transform duration-300">
        <div class="relative">
          <input 
            type="text" 
            v-model="searchTerm" 
            placeholder="Cerca comentaris..." 
            class="w-full pl-10 pr-6 py-2.5 text-sm text-lime-900 border-2 border-lime-300 rounded-full focus:outline-none focus:ring-4 focus:ring-lime-300/50 focus:border-lime-400 bg-white/80 backdrop-blur-sm shadow-lg" 
          />
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="w-4 h-4 text-lime-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
        </div>
      </div>
      <div class="-mt-2 sm:mt-0">
        <BotonesCrud />
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-6 py-3 sm:py-6">
      <!-- Loading State -->
      <div v-if="loading" class="flex flex-col items-center justify-center py-12">
        <div class="relative">
          <div class="w-16 h-16 border-4 border-lime-300 border-dashed rounded-full animate-spin"></div>
          <span class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-3xl">💬</span>
        </div>
        <p class="mt-4 text-lime-700 font-medium animate-pulse">Carregant comentaris...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="bg-red-50 rounded-xl p-8 text-center border border-red-200 shadow-lg hover:shadow-xl transition-all duration-300 motion-safe:animate-[shake_0.5s_ease-in-out]">
        <div class="text-4xl mb-4">😕</div>
        <p class="text-red-600 mb-4 font-medium">{{ error }}</p>
        <button @click="fetchComments" class="bg-gradient-to-r from-green-500 via-lime-400 to-lime-300 text-lime-900 px-8 py-3 rounded-full hover:from-green-600 hover:via-lime-500 hover:to-lime-400 transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 font-medium">
          Tornar a intentar
        </button>
      </div>

      <!-- No Results State -->
      <div v-else-if="filteredComments.length === 0" class="text-center py-12">
        <div class="text-6xl mb-4">🔍</div>
        <p class="text-lime-700 text-xl">No s'han trobat comentaris.</p>
      </div>

      <!-- Comments Table -->
      <div v-else class="bg-white rounded-2xl shadow-lg overflow-hidden max-w-4xl mx-auto">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-gradient-to-r from-lime-50 to-green-50">
                <th class="px-4 py-2 text-left text-xs font-semibold text-lime-900">Usuari</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-lime-900">Recepta</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-lime-900">Comentari</th>
                <th class="px-4 py-2 text-center text-xs font-semibold text-lime-900">Accions</th>
              </tr>
            </thead>
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

    <!-- Confirmation Modal -->
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

    <!-- Success Modal -->
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
      comments: [],
      loading: true,
      error: null,
      showConfirmModal: false,
      showResultModal: false,
      currentComment: null,
      resultMessage: '',
      resultError: false,
      searchTerm: ''
    }
  },
  computed: {
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
    
    formatDate(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
    },
 
    confirmDelete(comment) {
      this.currentComment = comment;
      this.showConfirmModal = true;
    },

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

<style scoped>
.comments-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
  position: relative;
}

.title {
  text-align: center;
  margin-bottom: 30px;
  color: #333;
}

.loading, .error, .no-comments {
  text-align: center;
  padding: 20px;
  font-size: 18px;
}

.error {
  color: #d32f2f;
}

.comments-table-container {
  overflow-x: auto;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  border-radius: 8px;
}

.comments-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
  background: white;
}

.comments-table th, .comments-table td {
  padding: 12px 15px;
  text-align: left;
  border-bottom: 1px solid #e0e0e0;
}

.comments-table th {
  background-color: #f5f5f5;
  font-weight: 600;
  color: #333;
}

.comments-table tr:hover {
  background-color: #f9f9f9;
}

.delete-btn {
  background-color: #f44336;
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.delete-btn:hover {
  background-color: #d32f2f;
}

/* Estilos para los modales */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal {
  background-color: white;
  border-radius: 8px;
  width: 90%;
  max-width: 500px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  overflow: hidden;
  animation: modalFadeIn 0.3s ease-out;
}

.modal-header {
  padding: 16px 20px;
  background-color: #f5f5f5;
  border-bottom: 1px solid #e0e0e0;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header.success {
  background-color: #4caf50;
  color: white;
}

.modal-header.error {
  background-color: #f44336;
  color: white;
}

.modal-close {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: inherit;
}

.modal-body {
  padding: 20px;
}

.modal-footer {
  padding: 16px 20px;
  border-top: 1px solid #e0e0e0;
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

.btn {
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s;
  border: none;
}

.btn-primary {
  background-color: #2196f3;
  color: white;
}

.btn-primary:hover {
  background-color: #0d8bf2;
}

.btn-secondary {
  background-color: #e0e0e0;
  color: #333;
}

.btn-secondary:hover {
  background-color: #d0d0d0;
}

.btn-danger {
  background-color: #f44336;
  color: white;
}

.btn-danger:hover {
  background-color: #d32f2f;
}

@keyframes modalFadeIn {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@media (max-width: 768px) {
  .comments-table th, .comments-table td {
    padding: 8px 10px;
    font-size: 14px;
  }
  
  .delete-btn {
    padding: 4px 8px;
    font-size: 12px;
  }
  
  .modal {
    width: 95%;
  }
}
</style>