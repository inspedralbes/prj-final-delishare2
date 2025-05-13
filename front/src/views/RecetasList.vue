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
                Totes les Receptes
              </span>
              <span class="block text-2xl mt-3 text-lime-700 font-medium">
                Explora i gestiona totes les receptes
              </span>
            </h1>
          </div>
        </div>
      </div>
    </section>

    <!-- Search Section -->
    <div class="w-full px-6 -mt-12 sm:-mt-8 md:-mt-6 relative z-20 flex justify-center items-center gap-4">
      <div class="w-full sm:w-2/3 md:w-1/2 lg:w-1/3 transform hover:scale-105 transition-transform duration-300">
        <div class="relative">
          <input 
            type="text" 
            v-model="searchTerm" 
            placeholder="Cerca receptes..." 
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
    <div class="max-w-7xl mx-auto px-6 py-6 mb-16">


      <!-- Loading State -->
      <div v-if="loading" class="flex flex-col items-center justify-center py-12">
        <div class="relative">
          <div class="w-16 h-16 border-4 border-lime-300 border-dashed rounded-full animate-spin"></div>
          <span class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-3xl">🍳</span>
        </div>
        <p class="mt-4 text-lime-700 font-medium animate-pulse">Carregant receptes...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="bg-red-50 rounded-xl p-8 text-center border border-red-200 shadow-lg hover:shadow-xl transition-all duration-300 motion-safe:animate-[shake_0.5s_ease-in-out]">
        <div class="text-4xl mb-4">😕</div>
        <p class="text-red-600 mb-4 font-medium">{{ error }}</p>
        <button @click="fetchRecetas" class="bg-gradient-to-r from-green-500 via-lime-400 to-lime-300 text-lime-900 px-8 py-3 rounded-full hover:from-green-600 hover:via-lime-500 hover:to-lime-400 transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 font-medium">
          Tornar a intentar
        </button>
      </div>

      <!-- No Results State -->
      <div v-else-if="filteredRecetas.length === 0" class="text-center py-12">
        <div class="text-6xl mb-4">🔍</div>
        <p class="text-lime-700 text-xl">No hi ha receptes disponibles.</p>
      </div>

      <!-- Recipes Grid -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="receta in filteredRecetas" :key="receta.id" class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 hover:scale-105">
          <div class="p-6">
            <h2 class="text-xl font-bold text-lime-900 mb-2">{{ receta.title }}</h2>
            <div class="flex flex-wrap gap-2 mb-3">
              <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">{{ receta.category?.name }}</span>
              <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">{{ receta.cuisine?.country }}</span>
            </div>
            <p class="text-lime-700 text-sm mb-4">{{ truncateDescription(receta.description) }}</p>
            <div class="flex justify-between items-center">
              <span class="text-lime-600 text-sm">Per: {{ receta.user?.name }}</span>
              <button @click="showDeleteModal(receta)" class="px-3 py-1.5 text-xs rounded font-semibold bg-red-500 text-white hover:bg-red-600 transition">
                Eliminar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Confirmation Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 transform transition-all">
        <h3 class="text-xl font-bold text-lime-900 mb-4">Confirmar eliminació</h3>
        <p class="text-lime-700 mb-6">Estàs segur que vols eliminar la recepta "{{ recetaToDelete?.title }}"?</p>
        <div class="flex justify-end space-x-4">
          <button @click="cancelDelete" class="px-4 py-2 text-sm font-medium text-lime-700 hover:text-lime-900 transition-colors duration-200">
            Cancel·lar
          </button>
          <button @click="confirmDelete" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors duration-200">
            Eliminar
          </button>
        </div>
      </div>
    </div>

    <!-- Success Modal -->
    <div v-if="showSuccessModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
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
  name: 'RecetasList',
  components: {
    BotonesCrud
  },
  data() {
    return {
      recetas: [],
      loading: true,
      error: null,
      showModal: false,
      recetaToDelete: null,
      successMessage: null,
      showSuccessModal: false,
      searchTerm: '',
    }
  },
  computed: {
    filteredRecetas() {
      if (!this.searchTerm) return this.recetas;
      const searchLower = this.searchTerm.toLowerCase();
      return this.recetas.filter(receta =>
        receta.title.toLowerCase().includes(searchLower) ||
        receta.description?.toLowerCase().includes(searchLower) ||
        receta.category?.name.toLowerCase().includes(searchLower) ||
        receta.cuisine?.country.toLowerCase().includes(searchLower)
      );
    }
  },
  mounted() {
    this.fetchRecetas();
  },
  methods: {
    async fetchRecetas() {
      this.loading = true;
      this.error = null;
      try {
        this.recetas = await communicationManager.fetchRecipes();
      } catch (err) {
        this.error = err.message;
        console.error('Error fetching recetas:', err);
      } finally {
        this.loading = false;
      }
    },
    showDeleteModal(receta) {
      this.recetaToDelete = receta;
      this.showModal = true;
    },
    cancelDelete() {
      this.showModal = false;
      this.recetaToDelete = null;
    },
    async confirmDelete() {
      try {
        await communicationManager.deleteRecipe(this.recetaToDelete.id);
        this.recetas = this.recetas.filter(receta => receta.id !== this.recetaToDelete.id);
        this.showModal = false;
        this.successMessage = `La recepta "${this.recetaToDelete.title}" ha estat eliminada amb èxit.`;
        this.showSuccessModal = true;
        this.recetaToDelete = null;
        setTimeout(() => {
          this.showSuccessModal = false;
          this.successMessage = null;
        }, 1000);
      } catch (error) {
        alert('Ha ocorregut un error en eliminar la recepta.');
      }
    },
    truncateDescription(desc) {
      if (!desc) return '';
      return desc.length > 150 ? desc.substring(0, 150) + '...' : desc;
    }
  }
}
</script>