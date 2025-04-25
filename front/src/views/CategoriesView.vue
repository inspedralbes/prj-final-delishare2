<template>
    <div class="categories-container">
      <h1>Listado de Categorías</h1>
      
      <!-- Lista de categorías existentes -->
      <div class="categories-list">
        <div v-if="loading" class="loading">Cargando categorías...</div>
        <div v-else-if="error" class="error">{{ error }}</div>
        <div v-else-if="categories.length === 0" class="no-categories">
          No hay categorías registradas.
        </div>
        <ul v-else class="category-items">
          <li v-for="category in categories" :key="category.id" class="category-item">
            <span>{{ category.name }}</span>
            <button @click="deleteCategory(category.id)" class="delete-btn">Eliminar</button>
          </li>
        </ul>
      </div>
    </div>
  </template>
  
  <script>
  import communicationManager from '@/services/communicationManager';
  
  export default {
    name: 'CategoriesView',
    data() {
      return {
        categories: [],
        loading: false,
        error: null
      }
    },
    created() {
      this.fetchCategories();
    },
    methods: {
      async fetchCategories() {
        this.loading = true;
        this.error = null;
        try {
          this.categories = await communicationManager.fetchCategories();
        } catch (err) {
          this.error = err.message;
          console.error('Error fetching categories:', err);
        } finally {
          this.loading = false;
        }
      },
      async deleteCategory(id) {
        if (!confirm('¿Estás seguro de que deseas eliminar esta categoría?')) {
          return;
        }
        
        try {
          await communicationManager.deleteCategory(id);
          // Actualizar la lista después de eliminar
          this.categories = this.categories.filter(category => category.id !== id);
          // Mostrar mensaje de éxito
          alert('Categoría eliminada correctamente');
        } catch (err) {
          console.error('Error deleting category:', err);
          alert(err.message || 'Error al eliminar la categoría');
        }
      }
    }
  }
  </script>
  
  <style scoped>
  /* Tus estilos existentes */
  .delete-btn {
    background-color: #f44336;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
  }
  
  .delete-btn:hover {
    background-color: #d32f2f;
  }
  </style>