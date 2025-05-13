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
                Gestió de Categories
              </span>
              <span class="block text-2xl mt-3 text-lime-700 font-medium">
                Administra les categories de receptes
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
            placeholder="Cerca categories..." 
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
          <span class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-3xl">📋</span>
        </div>
        <p class="mt-4 text-lime-700 font-medium animate-pulse">Carregant categories...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="bg-red-50 rounded-xl p-8 text-center border border-red-200 shadow-lg hover:shadow-xl transition-all duration-300 motion-safe:animate-[shake_0.5s_ease-in-out]">
        <div class="text-4xl mb-4">😕</div>
        <p class="text-red-600 mb-4 font-medium">{{ error }}</p>
        <button @click="obtenirCategories" class="bg-gradient-to-r from-green-500 via-lime-400 to-lime-300 text-lime-900 px-8 py-3 rounded-full hover:from-green-600 hover:via-lime-500 hover:to-lime-400 transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 font-medium">
          Tornar a intentar
        </button>
      </div>

      <!-- No Results State -->
      <div v-else-if="filteredCategories.length === 0" class="text-center py-12">
        <div class="text-6xl mb-4">🔍</div>
        <p class="text-lime-700 text-xl">No s'han trobat categories.</p>
      </div>

      <!-- Categories Table -->
      <div v-else class="bg-white rounded-2xl shadow-lg overflow-hidden max-w-3xl mx-auto">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-gradient-to-r from-lime-50 to-green-50">
                <th class="px-6 py-3 text-left text-xs font-semibold text-lime-900">Nom</th>
                <th class="px-6 py-3 text-center text-xs font-semibold text-lime-900">Accions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-lime-100">
              <tr v-for="category in filteredCategories" :key="category.id" class="hover:bg-lime-50/50 transition-colors duration-200">
                <td class="px-6 py-4">
                  <div class="text-sm font-medium text-lime-900">{{ category.name }}</div>
                </td>
                <td class="px-6 py-4 text-center">
                  <button 
                    @click="mostrarModalEliminar(category.id)" 
                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-600 hover:text-red-800 transition-colors duration-200"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Eliminar
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Create Category Form -->
      <div class="mt-8 mb-24 bg-white rounded-2xl shadow-lg p-6 max-w-3xl mx-auto">
        <h3 class="text-lg font-semibold text-lime-900 mb-4">Afegir Nova Categoria</h3>
        <div class="flex gap-4">
          <input 
            v-model="newCategory.name" 
            type="text" 
            placeholder="Nom de la categoria" 
            class="flex-1 px-4 py-2 text-sm text-lime-900 border-2 border-lime-300 rounded-lg focus:outline-none focus:ring-4 focus:ring-lime-300/50 focus:border-lime-400"
          />
          <button 
            @click="createCategory" 
            :disabled="!newCategory.name"
            class="px-6 py-2 text-sm font-medium text-white bg-gradient-to-r from-lime-500 to-green-500 rounded-lg hover:from-lime-600 hover:to-green-600 transition-all duration-300 disabled:from-gray-300 disabled:to-gray-400 disabled:cursor-not-allowed"
          >
            Crear Categoria
          </button>
        </div>
      </div>
    </div>

    <!-- Confirmation Modal -->
    <div v-if="modalVisible" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 transform transition-all">
        <h3 class="text-xl font-bold text-lime-900 mb-4">Confirmar eliminació</h3>
        <p class="text-lime-700 mb-6">Estàs segur que vols eliminar aquesta categoria?</p>
        <div class="flex justify-end space-x-4">
          <button 
            @click="modalVisible = false" 
            class="px-4 py-2 text-sm font-medium text-lime-700 hover:text-lime-900 transition-colors duration-200"
          >
            Cancel·lar
          </button>
          <button 
            @click="confirmarEliminar" 
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors duration-200"
          >
            Eliminar
          </button>
        </div>
      </div>
    </div>

    <!-- Success Modal -->
    <div v-if="successMessage" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 transform transition-all">
        <div class="text-center">
          <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <h3 class="text-xl font-bold text-lime-900 mb-2">¡Èxit!</h3>
          <p class="text-lime-700">{{ successMessage }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import communicationManager from '@/services/communicationManager';
import BotonesCrud from '@/components/BotonesCrud.vue';

export default {
  name: 'CategoriesView',
  components: {
    BotonesCrud
  },
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
      },
      searchTerm: ''
    }
  },
  computed: {
    filteredCategories() {
      if (!this.searchTerm) return this.categories;
      const searchLower = this.searchTerm.toLowerCase();
      return this.categories.filter(category =>
        category.name.toLowerCase().includes(searchLower)
      );
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
        setTimeout(() => {
          this.successMessage = '';
        }, 2000);
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
        this.categories.push(result);
        this.newCategory.name = '';
        setTimeout(() => {
          this.successMessage = '';
        }, 2000);
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