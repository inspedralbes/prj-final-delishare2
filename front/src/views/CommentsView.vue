<template>
  <div class="comments-container">
    <h1 class="title">Comentarios de Recetas</h1>
    
    <div v-if="loading" class="loading">Cargando comentarios...</div>
    
    <div v-if="error" class="error">
      Error al cargar los comentarios: {{ error }}
    </div>

    <div v-if="comments.length > 0" class="comments-table-container">
      <table class="comments-table">
        <thead>
          <tr>
            <th>Usuario</th>
            <th>Receta</th>
            <th>Comentario</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="comment in comments" :key="comment.id">
            <td>{{ comment.user_name }}</td>
            <td>{{ comment.recipe_title }}</td>
            <td>{{ comment.comment }}</td>
            <td>
              <button @click="confirmDelete(comment)" class="delete-btn">Eliminar</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-else-if="!loading" class="no-comments">
      No hay comentarios disponibles.
    </div>

    <!-- Modal de confirmación -->
    <div v-if="showConfirmModal" class="modal-overlay">
      <div class="modal">
        <div class="modal-header">
          <h3>Confirmar eliminación</h3>
          <button @click="showConfirmModal = false" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
          <p>¿Estás seguro de eliminar el comentario de {{ currentComment?.user_name }}?</p>
        </div>
        <div class="modal-footer">
          <button @click="showConfirmModal = false" class="btn btn-secondary">Cancelar</button>
          <button @click="executeDelete" class="btn btn-danger">Eliminar</button>
        </div>
      </div>
    </div>

    <!-- Modal de éxito/error -->
    <div v-if="showResultModal" class="modal-overlay">
      <div class="modal">
        <div class="modal-header" :class="{ 'success': !resultError, 'error': resultError }">
          <h3>{{ resultError ? 'Error' : 'Éxito' }}</h3>
          <button @click="showResultModal = false" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
          <p>{{ resultMessage }}</p>
        </div>
        <div class="modal-footer">
          <button @click="showResultModal = false" class="btn btn-primary">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import communicationManager from '../services/communicationManager';

export default {
  name: 'CommentsView',
  data() {
    return {
      comments: [],
      loading: true,
      error: null,
      showConfirmModal: false,
      showResultModal: false,
      currentComment: null,
      resultMessage: '',
      resultError: false
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
        this.error = err.message || 'Error al cargar los comentarios';
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

        // Actualizar la lista de comentarios después de eliminar
        await this.fetchComments();
        
        // Mostrar modal de éxito
        this.resultMessage = 'Comentario eliminado correctamente';
        this.resultError = false;
        this.showResultModal = true;

      } catch (error) {
        console.error('Error deleting comment:', error);
        
        // Manejo seguro del mensaje de error
        let errorMessage = 'Error al eliminar el comentario';
        if (error && error.response && error.response.data) {
          errorMessage = error.response.data.message || errorMessage;
        } else if (error.message) {
          errorMessage = error.message;
        }
        
        this.resultMessage = errorMessage;
        this.resultError = true;
        this.showResultModal = true;
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