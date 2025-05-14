<template>
  <div class="max-w-4xl mx-auto p-4 sm:p-8 mb-20 sm:mb-24 md:mb-28">
    <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8">
      <h2 class="text-2xl sm:text-3xl font-bold text-lime-900 mb-6 text-center">Mis Preferencias Culinarias</h2>

      <form @submit.prevent="submitPreferences" class="space-y-8">
        <!-- Sección de Cocinas -->
        <div class="bg-gradient-to-br from-lime-50 to-green-50 rounded-xl p-6 shadow-sm">
          <h3 class="text-lg sm:text-xl font-semibold text-lime-900 mb-4">Selecciona tus cocinas favoritas</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <div v-for="cuisine in cuisines" :key="'cuisine-' + cuisine.id" 
                 class="flex items-center space-x-3 p-3 bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-300">
              <input type="checkbox" 
                     :id="'cuisine-' + cuisine.id" 
                     :value="cuisine.id" 
                     v-model="selectedCuisines"
                     class="w-4 h-4 text-lime-600 border-lime-300 rounded focus:ring-lime-500">
              <label :for="'cuisine-' + cuisine.id" 
                     class="text-sm sm:text-base text-gray-700 cursor-pointer hover:text-lime-900">
                {{ cuisine.name }}
              </label>
            </div>
          </div>
        </div>

        <!-- Sección de Categorías -->
        <div class="bg-gradient-to-br from-lime-50 to-green-50 rounded-xl p-6 shadow-sm">
          <h3 class="text-lg sm:text-xl font-semibold text-lime-900 mb-4">Selecciona tus categorías favoritas</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <div v-for="category in categories" :key="'category-' + category.id" 
                 class="flex items-center space-x-3 p-3 bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-300">
              <input type="checkbox" 
                     :id="'category-' + category.id" 
                     :value="category.id" 
                     v-model="selectedCategories"
                     class="w-4 h-4 text-lime-600 border-lime-300 rounded focus:ring-lime-500">
              <label :for="'category-' + category.id" 
                     class="text-sm sm:text-base text-gray-700 cursor-pointer hover:text-lime-900">
                {{ category.name }}
              </label>
            </div>
          </div>
        </div>

        <!-- Botón de Envío -->
        <div class="flex justify-center mb-8">
          <button type="submit" 
                  :disabled="isLoading"
                  class="px-6 py-3 bg-gradient-to-r from-lime-400 to-green-500 text-white font-medium rounded-lg shadow-md hover:from-lime-500 hover:to-green-600 focus:outline-none focus:ring-2 focus:ring-lime-300 focus:ring-opacity-50 transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed">
            <span class="flex items-center justify-center">
              <svg v-if="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ isLoading ? 'Guardando...' : 'Guardar Preferencias' }}
            </span>
          </button>
        </div>

        <!-- Mensajes de Feedback -->
        <div v-if="message" 
             :class="[
               'p-4 rounded-lg text-center font-medium transition-all duration-300',
               isSuccess ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
             ]">
          {{ message }}
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import communicationManager from "../services/communicationManager";

export default {
  name: 'RecommendationForm',
  data() {
    return {
      cuisines: [],
      categories: [],
      selectedCuisines: [],
      selectedCategories: [],
      isLoading: false,
      message: '',
      isSuccess: false
    };
  },
  async created() {
    await this.fetchOptions();
    await this.loadUserPreferences();
  },
  methods: {
    async fetchOptions() {
      try {
        const response = await communicationManager.fetchCuisinesAndCategories();
        this.cuisines = response.data.cuisines;
        this.categories = response.data.categories;
      } catch (error) {
        console.error('Error fetching options:', error);
        this.showMessage('Error al cargar las opciones', false);
      }
    },
    async loadUserPreferences() {
      try {
        const response = await communicationManager.fetchUserPreferences();
        if (response.data) {
          this.selectedCuisines = response.data.cuisines || [];
          this.selectedCategories = response.data.categories || [];
        }
      } catch (error) {
        console.error('Error loading preferences:', error);
      }
    },
    async submitPreferences() {
      this.isLoading = true;
      this.message = '';

      try {
        await communicationManager.savePreferences({
          cuisine_ids: this.selectedCuisines,
          category_ids: this.selectedCategories
        });

        this.showMessage('Preferencias guardadas correctamente', true);
        this.$router.push({ name: 'LandingPage' });
      } catch (error) {
        console.error('Error saving preferences:', error);
        this.showMessage('Error al guardar las preferencias', false);
      } finally {
        this.isLoading = false;
      }
    },
    showMessage(msg, success) {
      this.message = msg;
      this.isSuccess = success;
      setTimeout(() => {
        this.message = '';
      }, 5000);
    }
  }
};
</script>