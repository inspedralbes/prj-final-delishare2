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
                <button @click="deleteComment(comment)" class="delete-btn">Eliminar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
  
      <div v-else-if="!loading" class="no-comments">
        No hay comentarios disponibles.
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
        error: null
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
      async deleteComment(comment) {
        if (!confirm(`¿Estás seguro de eliminar el comentario de ${comment.user_name}?`)) {
          return;
        }
        
        try {
          // Nota: Necesitarás implementar este endpoint en tu backend
          await communicationManager.deleteComment(comment.id);
          this.comments = this.comments.filter(c => c.id !== comment.id);
          this.$toast.success('Comentario eliminado correctamente');
        } catch (error) {
          console.error('Error deleting comment:', error);
          this.$toast.error('Error al eliminar el comentario');
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
  
  @media (max-width: 768px) {
    .comments-table th, .comments-table td {
      padding: 8px 10px;
      font-size: 14px;
    }
    
    .delete-btn {
      padding: 4px 8px;
      font-size: 12px;
    }
  }
  </style>