<template>
    <div class="categories-container">
      <h1>Llista de Categories</h1>
      
      <div v-if="loading" class="loading">Carregant categories...</div>
      
      <div v-if="error" class="error">
        Error en carregar les categories: {{ error }}
      </div>
      
      <div v-if="successMessage" class="success">
        {{ successMessage }}
      </div>
      
      <!-- Llista de categories existents -->
      <div v-if="categories.length > 0">
        <table class="categories-table">
          <thead>
            <tr>
              <th>Nom</th>
              <th>Accions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="category in categories" :key="category.id">
              <td>{{ category.name }}</td>
              <td>
                <button @click="mostrarModalEliminar(category.id)" class="delete-btn">Eliminar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <div v-else-if="!loading" class="no-categories">
        No s'han trobat categories.
      </div>
      
      <!-- Formulari per Crear una Nova Categoria -->
      <div class="create-category-form">
        <h3>Afegir Nova Categoria</h3>
        <input v-model="newCategory.name" type="text" placeholder="Nom de la categoria" />
        <button @click="createCategory" :disabled="!newCategory.name">Crear Categoria</button>
      </div>
      
      <!-- Modal -->
      <div v-if="modalVisible" class="modal-backdrop">
        <div class="modal">
          <h3>Confirmar eliminació</h3>
          <p>Estàs segur que vols eliminar aquesta categoria?</p>
          <div class="modal-actions">
            <button @click="confirmarEliminar" class="confirm">Sí</button>
            <button @click="modalVisible = false" class="cancel">No</button>
          </div>
        </div>
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
        successMessage: '',
        modalVisible: false,
        categoryToDelete: null,
        newCategory: {
          name: ''
        }
      }
    },
    created() {
      this.obtenirCategories();
    },
    methods: {
      async obtenirCategories() {
        this.loading = true;
        this.error = null;
        this.successMessage = '';
        
        try {
          this.categories = await communicationManager.fetchCategories();
        } catch (err) {
          this.error = err.message;
          console.error('Error obtenint les categories:', err);
        } finally {
          this.loading = false;
        }
      },
      
      mostrarModalEliminar(id) {
        this.categoryToDelete = id;
        this.modalVisible = true;
      },
      
      async confirmarEliminar() {
        this.loading = true;
        this.error = null;
        
        try {
          await communicationManager.deleteCategory(this.categoryToDelete);
          this.successMessage = 'Categoria eliminada correctament';
          this.categories = this.categories.filter(category => category.id !== this.categoryToDelete);
        } catch (err) {
          this.error = err.message;
          console.error('Error eliminant la categoria:', err);
        } finally {
          this.loading = false;
          this.modalVisible = false;
          this.categoryToDelete = null;
        }
      },
      
      async createCategory() {
        if (!this.newCategory.name.trim()) {
          this.error = 'El nom de la categoria no pot estar buit.';
          return;
        }
        
        this.loading = true;
        this.error = null;
        this.successMessage = '';
        
        try {
          const result = await communicationManager.createCategory(this.newCategory);
          this.successMessage = `Categoria creada: ${result.name}`;
          this.categories.push(result); // Afegir la nova categoria a la llista
          this.newCategory.name = ''; // Netejar el camp d'entrada
        } catch (err) {
          this.error = err.message;
          console.error('Error creant la categoria:', err);
        } finally {
          this.loading = false;
        }
      }
    }
  }
  </script>
  
  <style scoped>
  .categories-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
  }
  
  .categories-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
  }
  
  .categories-table th,
  .categories-table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
  }
  
  .categories-table th {
    background-color: #f2f2f2;
  }
  
  .loading {
    color: #666;
    font-style: italic;
  }
  
  .error {
    color: #d32f2f;
    background-color: #fde0e0;
    padding: 10px;
    border-radius: 4px;
    margin: 10px 0;
  }
  
  .success {
    color: #388e3c;
    background-color: #edf7ed;
    padding: 10px;
    border-radius: 4px;
    margin: 10px 0;
  }
  
  .no-categories {
    color: #666;
    font-style: italic;
    margin-top: 20px;
  }
  
  .create-category-form {
    margin-top: 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  
  .create-category-form input {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
  }
  
  .create-category-form button {
    padding: 10px;
    background-color: #4caf50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
  }
  
  .create-category-form button:hover {
    background-color: #388e3c;
  }
  
  .create-category-form button:disabled {
    background-color: #cccccc;
    cursor: not-allowed;
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
  
  /* Modal Styles */
  .modal-backdrop {
    position: fixed;
    top: 0; left: 0;
    width: 100vw; height: 100vh;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex; align-items: center; justify-content: center;
    z-index: 1000;
  }
  
  .modal {
    background: white;
    padding: 20px;
    border-radius: 8px;
    width: 300px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    text-align: center;
  }
  
  .modal-actions {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 20px;
  }
  
  .confirm {
    background: #4caf50;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
  }
  
  .cancel {
    background: #f44336;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
  }
  </style>