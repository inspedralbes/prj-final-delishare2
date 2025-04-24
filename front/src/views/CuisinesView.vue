<template>
    <div class="cuisines-container">
      <h1>Cuisines List</h1>
      
      <div v-if="loading" class="loading">Loading cuisines...</div>
      
      <div v-if="error" class="error">
        Error loading cuisines: {{ error }}
      </div>
      
      <div v-if="successMessage" class="success">
        {{ successMessage }}
      </div>
      
      <div v-if="cuisines.length > 0">
        <table class="cuisines-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Created At</th>
              <th>Updated At</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="cuisine in cuisines" :key="cuisine.id">
              <td>{{ cuisine.id }}</td>
              <td>{{ cuisine.country }}</td>
              <td>{{ formatDate(cuisine.created_at) }}</td>
              <td>{{ formatDate(cuisine.updated_at) }}</td>
              <td>
                <button @click="deleteCuisine(cuisine.id)" class="delete-btn">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <div v-else-if="!loading" class="no-cuisines">
        No cuisines found.
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
        successMessage: ''
      }
    },
    mounted() {
      this.fetchCuisines();
    },
    methods: {
      async fetchCuisines() {
        this.loading = true;
        this.error = null;
        this.successMessage = '';
        
        try {
          this.cuisines = await communicationManager.fetchCuisines();
        } catch (err) {
          this.error = err.message;
          console.error('Error fetching cuisines:', err);
        } finally {
          this.loading = false;
        }
      },
      async deleteCuisine(id) {
        if (confirm('Are you sure you want to delete this cuisine?')) {
          this.loading = true;
          this.error = null;
          
          try {
            const result = await communicationManager.deleteCuisine(id);
            this.successMessage = result.message;
            this.cuisines = this.cuisines.filter(cuisine => cuisine.id !== id);
          } catch (err) {
            this.error = err.message;
            console.error('Error deleting cuisine:', err);
          } finally {
            this.loading = false;
          }
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
  /* Tus estilos existentes se mantienen igual */
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
  </style>