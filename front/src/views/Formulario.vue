<template>
  <div class="recommendation-form">
    <h2>Mis Preferencias Culinarias</h2>

    <form @submit.prevent="submitPreferences">
      <!-- Sección de Cocinas -->
      <div class="form-section">
        <h3>Selecciona tus cocinas favoritas</h3>
        <div v-for="cuisine in cuisines" :key="'cuisine-' + cuisine.id" class="checkbox-item">
          <input type="checkbox" :id="'cuisine-' + cuisine.id" :value="cuisine.id" v-model="selectedCuisines">
          <label :for="'cuisine-' + cuisine.id">{{ cuisine.name }}</label>
        </div>
      </div>

      <!-- Sección de Categorías -->
      <div class="form-section">
        <h3>Selecciona tus categorías favoritas</h3>
        <div v-for="category in categories" :key="'category-' + category.id" class="checkbox-item">
          <input type="checkbox" :id="'category-' + category.id" :value="category.id" v-model="selectedCategories">
          <label :for="'category-' + category.id">{{ category.name }}</label>
        </div>
      </div>

      <!-- Botón de Envío -->
      <button type="submit" :disabled="isLoading">
        {{ isLoading ? 'Guardando...' : 'Guardar Preferencias' }}
      </button>

      <!-- Mensajes de Feedback -->
      <div v-if="message" class="feedback" :class="{ 'success': isSuccess, 'error': !isSuccess }">
        {{ message }}
      </div>
    </form>
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

<style scoped>
.recommendation-form {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.form-section {
  margin-bottom: 30px;
  padding: 20px;
  background: #f8f9fa;
  border-radius: 8px;
}

.checkbox-item {
  margin: 10px 0;
  display: flex;
  align-items: center;
}

.checkbox-item input {
  margin-right: 10px;
}

button {
  padding: 10px 20px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
}

button:disabled {
  background-color: #cccccc;
  cursor: not-allowed;
}

.feedback {
  margin-top: 20px;
  padding: 10px;
  border-radius: 4px;
}

.success {
  background-color: #d4edda;
  color: #155724;
}

.error {
  background-color: #f8d7da;
  color: #721c24;
}
</style>