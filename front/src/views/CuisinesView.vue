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
                Gestió de Cuines
              </span>
              <span class="block text-2xl mt-3 text-lime-700 font-medium">
                Administra les cuines de receptes
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
            placeholder="Cerca cuines..." 
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
    <div class="max-w-7xl mx-auto px-6 py-3 sm:py-6 mb-24">
      <!-- Loading State -->
      <div v-if="loading" class="flex flex-col items-center justify-center py-12">
        <div class="relative">
          <div class="w-16 h-16 border-4 border-lime-300 border-dashed rounded-full animate-spin"></div>
        </div>
        <p class="mt-4 text-lime-700 font-medium animate-pulse">Carregant cuines...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="bg-red-50 rounded-xl p-8 text-center border border-red-200 shadow-lg hover:shadow-xl transition-all duration-300 motion-safe:animate-[shake_0.5s_ease-in-out]">
        <p class="text-red-600 mb-4 font-medium">{{ error }}</p>
        <button @click="obtenerCuines" class="bg-gradient-to-r from-green-500 via-lime-400 to-lime-300 text-lime-900 px-8 py-3 rounded-full hover:from-green-600 hover:via-lime-500 hover:to-lime-400 transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 font-medium">
          Tornar a intentar
        </button>
      </div>

      <template v-else>
        <!-- Create Cuisine Form -->
        <div class="mb-8 bg-white rounded-2xl shadow-lg p-6 max-w-3xl mx-auto">
          <h3 class="text-lg font-semibold text-lime-900 mb-4">Afegir Nova Cuina</h3>
          <div class="flex gap-4">
            <input 
              v-model="newCuisineName" 
              type="text" 
              placeholder="Nom de la cuina" 
              class="flex-1 px-4 py-2 text-sm text-lime-900 border-2 border-lime-300 rounded-lg focus:outline-none focus:ring-4 focus:ring-lime-300/50 focus:border-lime-400"
            />
            <button 
              @click="crearCuina" 
              :disabled="!newCuisineName.trim()"
              class="px-6 py-2 text-sm font-medium text-white bg-gradient-to-r from-lime-500 to-green-500 rounded-lg hover:from-lime-600 hover:to-green-600 transition-all duration-300 disabled:from-gray-300 disabled:to-gray-400 disabled:cursor-not-allowed"
            >
              Crear Cuina
            </button>
          </div>
        </div>

        <!-- No Results State -->
        <div v-if="filteredCuisines.length === 0" class="text-center py-12">
          <p class="text-lime-700 text-xl">No s'han trobat cuines.</p>
        </div>

        <!-- Cuisines Table -->
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
                <tr v-for="cuisine in filteredCuisines" :key="cuisine.id" class="hover:bg-lime-50/50 transition-colors duration-200">
                  <td class="px-6 py-4">
                    <div class="text-sm font-medium text-lime-900">{{ cuisine.country }}</div>
                  </td>
                  <td class="px-6 py-4 text-center">
                    <button 
                      @click="mostrarModalEliminar(cuisine.id)" 
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
      </template>
    </div>

    <!-- Confirmation Modal -->
    <div v-if="modalVisible" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 transform transition-all">
        <h3 class="text-xl font-bold text-lime-900 mb-4">Confirmar eliminació</h3>
        <p class="text-lime-700 mb-6">Estàs segur que vols eliminar aquesta cuina?</p>
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
  name: 'CuisinesView',
  components: {
    BotonesCrud
  },
  data() {
    return {
      cuisines: [],
      loading: false,
      error: null,
      successMessage: '',
      modalVisible: false,
      cuisineToDelete: null,
      newCuisineName: '',
      searchTerm: ''
    };
  },
  computed: {
    filteredCuisines() {
      if (!this.searchTerm) return this.cuisines;
      const searchLower = this.searchTerm.toLowerCase();
      return this.cuisines.filter(cuisine =>
        cuisine.country.toLowerCase().includes(searchLower)
      );
    }
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
        setTimeout(() => {
          this.successMessage = '';
        }, 2000);
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
        setTimeout(() => {
          this.successMessage = '';
        }, 2000);
      } catch (err) {
        this.error = err.message;
        console.error('Error creant la cuina:', err);
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>
  