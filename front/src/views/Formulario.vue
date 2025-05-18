<template>
  <div class="min-h-screen bg-lime-50 flex flex-col">
    <!-- Hero Section with animated background -->
    <section class="relative overflow-hidden">
      <div class="bg-gradient-to-br from-lime-100 via-lime-200 to-green-200 py-16 relative">
        <div class="absolute inset-0 bg-white/30 backdrop-blur-sm"></div>
        <!-- Animated circles decoration -->
        <div class="absolute inset-0 overflow-hidden">
          <div class="absolute -left-10 -top-10 w-40 h-40 bg-yellow-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
          <div class="absolute -right-10 -top-10 w-40 h-40 bg-lime-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
          <div class="absolute -bottom-10 left-20 w-40 h-40 bg-green-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
        </div>

        <div class="max-w-7xl mx-auto px-6 relative z-10">
          <div class="text-center">
            <h1 class="text-4xl tracking-tight font-extrabold text-lime-900 sm:text-5xl md:text-6xl">
              <span class="block bg-gradient-to-r from-lime-900 via-lime-700 to-green-800 bg-clip-text text-transparent">
                Mis Preferencias Culinarias
              </span>
              <span class="block text-2xl mt-3 text-lime-700 font-medium">
                Personaliza tus recomendaciones
              </span>
            </h1>
          </div>
        </div>
      </div>
    </section>

    <!-- Form Section -->
    <div class="max-w-4xl mx-auto px-4 sm:px-8 -mt-12 pt-12 pb-24">
      <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg p-6 sm:p-8">
        <form @submit.prevent="submitPreferences" class="space-y-8">
          <!-- Sección de Cocinas -->
          <div class="bg-gradient-to-br from-lime-50 to-green-50 rounded-xl p-6 sm:p-8 shadow-sm">
            <h3 class="text-lg sm:text-xl font-semibold text-lime-900 mb-6">Selecciona tus cocinas favoritas</h3>
            <div class="grid grid-cols-2 gap-4">
              <div v-for="cuisine in cuisines" :key="'cuisine-' + cuisine.id" 
                   @click="toggleCuisine(cuisine.id)"
                   :class="[
                     'flex items-center justify-center p-4 rounded-lg cursor-pointer transition-all duration-300 transform hover:scale-105',
                     selectedCuisines.includes(cuisine.id) 
                       ? 'bg-gradient-to-r from-lime-400 to-green-500 text-white shadow-md' 
                       : 'bg-white text-gray-700 shadow-sm hover:shadow-md'
                   ]">
                <span class="text-sm sm:text-base font-medium text-center">{{ cuisine.name }}</span>
              </div>
            </div>
          </div>

          <!-- Sección de Categorías -->
          <div class="bg-gradient-to-br from-lime-50 to-green-50 rounded-xl p-6 sm:p-8 shadow-sm">
            <h3 class="text-lg sm:text-xl font-semibold text-lime-900 mb-6">Selecciona tus categorías favoritas</h3>
            <div class="grid grid-cols-2 gap-4">
              <div v-for="category in categories" :key="'category-' + category.id" 
                   @click="toggleCategory(category.id)"
                   :class="[
                     'flex items-center justify-center p-4 rounded-lg cursor-pointer transition-all duration-300 transform hover:scale-105',
                     selectedCategories.includes(category.id) 
                       ? 'bg-gradient-to-r from-lime-400 to-green-500 text-white shadow-md' 
                       : 'bg-white text-gray-700 shadow-sm hover:shadow-md'
                   ]">
                <span class="text-sm sm:text-base font-medium text-center">{{ category.name }}</span>
              </div>
            </div>
          </div>

          <!-- Botón de Envío -->
          <div class="flex justify-center pt-6">
            <button type="submit" 
                    :disabled="isLoading"
                    class="px-8 py-4 bg-gradient-to-r from-lime-400 to-green-500 text-white font-medium rounded-lg shadow-md hover:from-lime-500 hover:to-green-600 focus:outline-none focus:ring-2 focus:ring-lime-300 focus:ring-opacity-50 transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed">
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
    toggleCuisine(id) {
      const index = this.selectedCuisines.indexOf(id);
      if (index === -1) {
        this.selectedCuisines.push(id);
      } else {
        this.selectedCuisines.splice(index, 1);
      }
    },
    toggleCategory(id) {
      const index = this.selectedCategories.indexOf(id);
      if (index === -1) {
        this.selectedCategories.push(id);
      } else {
        this.selectedCategories.splice(index, 1);
      }
    },
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

<style scoped>
@keyframes blob {
  0% {
    transform: translate(0px, 0px) scale(1);
  }
  33% {
    transform: translate(30px, -50px) scale(1.1);
  }
  66% {
    transform: translate(-20px, 20px) scale(0.9);
  }
  100% {
    transform: translate(0px, 0px) scale(1);
  }
}

.animate-blob {
  animation: blob 7s infinite;
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}
</style>