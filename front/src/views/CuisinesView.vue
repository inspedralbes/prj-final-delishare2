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
              <th>Nom</th>
              <th>Accions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="cuisine in cuisines" :key="cuisine.id">
              <td>{{ cuisine.country }}</td>
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
  
      <!-- Formulario para Crear una Nueva Cocina -->
      <div class="create-cuisine-form">
        <h3>Afegir Nova Cuina</h3>
        <input v-model="newCuisineName" type="text" placeholder="Nom de la cuina" />
        <button @click="crearCuina" :disabled="!newCuisineName.trim()">Crear Cuina</button>
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
        newCuisineName: '', // Nuevo campo para el nombre de la cuina
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
      async crearCuina() {
        if (!this.newCuisineName.trim()) {
          this.error = 'El nom de la cuina no pot estar buit.';
          return;
        }
  
        this.loading = true;
        this.error = null;
        this.successMessage = '';
  
        try {
          const newCuisine = {
            country: this.newCuisineName,
          };
          const result = await communicationManager.createCuisine(newCuisine);
          this.successMessage = `Cuina creada: ${result.country}`;
          this.cuisines.push(result); 
          this.newCuisineName = ''; 
        } catch (err) {
          this.error = err.message;
          console.error('Error creant la cuina:', err);
        } finally {
          this.loading = false;
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
  
  .create-cuisine-form button:disabled {
  background-color: #cccccc;
  cursor: not-allowed;
}

  
  .create-cuisine-form input {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
  }
  
  .create-cuisine-form button {
    padding: 10px;
    background-color: #4caf50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
  }
  
  .create-cuisine-form button:hover {
    background-color: #388e3c;
  }

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
  