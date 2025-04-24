<template>
    <div class="cuisines-container">
      <h1>Llista de Cuines</h1>
  
      <div v-if="loading" class="loading">Carregant cuines...</div>
  
      <div v-if="error" class="error">
        Error en carregar les cuines: {{ error }}
      </div>
  
      <div v-if="successMessage" class="success">
        {{ successMessage }}
      </div>
  
      <div v-if="cuisines.length > 0">
        <table class="cuisines-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nom</th>
              <th>Creat el</th>
              <th>Actualitzat el</th>
              <th>Accions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="cuisine in cuisines" :key="cuisine.id">
              <td>{{ cuisine.id }}</td>
              <td>{{ cuisine.country }}</td>
              <td>{{ formatDate(cuisine.created_at) }}</td>
              <td>{{ formatDate(cuisine.updated_at) }}</td>
              <td>
                <button @click="mostrarModalEliminar(cuisine.id)" class="delete-btn">Eliminar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
  
      <div v-else-if="!loading" class="no-cuisines">
        No s'han trobat cuines.
      </div>
  
      <!-- Modal -->
      <div v-if="modalVisible" class="modal-backdrop">
        <div class="modal">
          <h3>Confirmar eliminació</h3>
          <p>Estàs segur que vols eliminar aquesta cuina?</p>
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
    name: 'CuisinesView',
    data() {
      return {
        cuisines: [],
        loading: false,
        error: null,
        successMessage: '',
        modalVisible: false,
        cuisineToDelete: null,
      };
    },
    mounted() {
      this.obtenerCuines();
    },
    methods: {
      async obtenerCuines() {
        this.loading = true;
        this.error = null;
        this.successMessage = '';
  
        try {
          this.cuisines = await communicationManager.fetchCuisines();
        } catch (err) {
          this.error = err.message;
          console.error('Error obtenint les cuines:', err);
        } finally {
          this.loading = false;
        }
      },
      mostrarModalEliminar(id) {
        this.cuisineToDelete = id;
        this.modalVisible = true;
      },
      async confirmarEliminar() {
        this.loading = true;
        this.error = null;
  
        try {
          const resultat = await communicationManager.deleteCuisine(this.cuisineToDelete);
          this.successMessage = resultat.message;
          this.cuisines = this.cuisines.filter(cuisine => cuisine.id !== this.cuisineToDelete);
        } catch (err) {
          this.error = err.message;
          console.error('Error eliminant la cuina:', err);
        } finally {
          this.loading = false;
          this.modalVisible = false;
          this.cuisineToDelete = null;
        }
      },
      formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString();
      }
    }
  }
  </script>
   
  <style scoped>
  .cuisines-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
  }
  
  .cuisines-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
  }
  
  .cuisines-table th,
  .cuisines-table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
  }
  
  .cuisines-table th {
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
  
  .no-cuisines {
    color: #666;
    font-style: italic;
    margin-top: 20px;
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
  }
  
  .cancel {
    background: #f44336;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
  }
  </style>
  