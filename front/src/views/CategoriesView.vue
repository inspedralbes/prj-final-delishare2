<template>
    <div class="categories-container">
      <h1>Listado de Categorías</h1>
      
      <!-- Formulario para crear nueva categoría -->
      <div class="category-form">
        <h2>Crear Nueva Categoría</h2>
        <div class="form-group">
          <input 
            type="text" 
            v-model="newCategory.name" 
            placeholder="Nombre de la categoría"
            class="category-input"
          />
          <button 
            @click="createCategory" 
            class="create-btn"
            :disabled="!newCategory.name"
          >
            Crear
          </button>
        </div>
      </div>
      
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
        error: null,
        newCategory: {
          name: ''
        }
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
      
      async createCategory() {
        if (!this.newCategory.name) {
          return;
        }
        
        try {
          const createdCategory = await communicationManager.createCategory(this.newCategory);
          // Añadir la nueva categoría a la lista existente
          this.categories.push(createdCategory);
          // Limpiar el formulario
          this.newCategory.name = '';
          // Mostrar mensaje de éxito
          alert('Categoría creada correctamente');
        } catch (err) {
          console.error('Error creating category:', err);
          alert(err.message || 'Error al crear la categoría');
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
  /* Estilos para el formulario de creación */
  .category-form {
    margin-bottom: 30px;
    padding: 15px;
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }
  
  h2 {
    font-size: 1.2em;
    margin-bottom: 10px;
    color: #333;
  }
  
  .form-group {
    display: flex;
    gap: 10px;
  }
  
  .category-input {
    flex: 1;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
  }
  
  .create-btn {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
  }
  
  .create-btn:hover {
    background-color: #45a049;
  }
  
  .create-btn:disabled {
    background-color: #cccccc;
    cursor: not-allowed;
  }
  
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
  
  .categories-list {
    margin-top: 20px;
  }
  
  .category-items {
    list-style: none;
    padding: 0;
  }
  
  .category-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 15px;
    margin-bottom: 8px;
    background-color: #fff;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }
  
  .loading, .error, .no-categories {
    padding: 15px;
    text-align: center;
    background-color: #f9f9f9;
    border-radius: 4px;
  }
  
  .error {
    color: #f44336;
  }
  </style>