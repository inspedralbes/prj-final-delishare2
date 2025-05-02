<template>
  <div class="user-profile">
    <button class="back-button" @click="goBack">← </button>
    <h2>Perfil d'Usuari</h2>
    <p><strong>Nom:</strong> {{ user.name }}</p>
    <p><strong>Correu electrònic:</strong> {{ user.email }}</p>

    <!-- Botones para las secciones -->
    <div class="profile-buttons">
      <button class="btn" @click="showSection('recipes')" :class="{ active: activeSection === 'recipes' }">
        <svg width="20" height="20" viewBox="0 0 24 24"
          style="vertical-align: middle; margin-right: 6px; background: transparent; fill: none;"
          xmlns="http://www.w3.org/2000/svg">
          <path
            d="M14 5.6C14 5.03995 14 4.75992 14.109 4.54601C14.2049 4.35785 14.3578 4.20487 14.546 4.10899C14.7599 4 15.0399 4 15.6 4H18.4C18.9601 4 19.2401 4 19.454 4.10899C19.6422 4.20487 19.7951 4.35785 19.891 4.54601C20 4.75992 20 5.03995 20 5.6V8.4C20 8.96005 20 9.24008 19.891 9.45399C19.7951 9.64215 19.6422 9.79513 19.454 9.89101C19.2401 10 18.9601 10 18.4 10H15.6C15.0399 10 14.7599 10 14.546 9.89101C14.3578 9.79513 14.2049 9.64215 14.109 9.45399C14 9.24008 14 8.96005 14 8.4V5.6Z"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          <path
            d="M4 5.6C4 5.03995 4 4.75992 4.10899 4.54601C4.20487 4.35785 4.35785 4.20487 4.54601 4.10899C4.75992 4 5.03995 4 5.6 4H8.4C8.96005 4 9.24008 4 9.45399 4.10899C9.64215 4.20487 9.79513 4.35785 9.89101 4.54601C10 4.75992 10 5.03995 10 5.6V8.4C10 8.96005 10 9.24008 9.89101 9.45399C9.79513 9.64215 9.64215 9.79513 9.45399 9.89101C9.24008 10 8.96005 10 8.4 10H5.6C5.03995 10 4.75992 10 4.54601 9.89101C4.35785 9.79513 4.20487 9.64215 4.10899 9.45399C4 9.24008 4 8.96005 4 8.4V5.6Z"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          <path
            d="M4 15.6C4 15.0399 4 14.7599 4.10899 14.546C4.20487 14.3578 4.35785 14.2049 4.54601 14.109C4.75992 14 5.03995 14 5.6 14H8.4C8.96005 14 9.24008 14 9.45399 14.109C9.64215 14.2049 9.79513 14.3578 9.89101 14.546C10 14.7599 10 15.0399 10 15.6V18.4C10 18.9601 10 19.2401 9.89101 19.454C9.79513 19.6422 9.64215 19.7951 9.45399 19.891C9.24008 20 8.96005 20 8.4 20H5.6C5.03995 20 4.75992 20 4.54601 19.891C4.35785 19.7951 4.20487 19.6422 4.10899 19.454C4 19.2401 4 18.9601 4 18.4V15.6Z"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          <path
            d="M14 15.6C14 15.0399 14 14.7599 14.109 14.546C14.2049 14.2049 14.3578 14.2049 14.546 14.1089C14.7599 14 15.0399 14 15.6 14H18.4C18.9601 14 19.2401 14 19.454 14.1089C19.6422 14.2049 19.7951 14.3578 19.891 14.546C20 14.7599 20 15.0399 20 15.6V18.4C20 18.9601 20 19.2401 19.891 19.4539C19.7951 19.6422 19.6422 19.7951 19.454 19.891C19.2401 20 18.9601 20 18.4 20H15.6C15.0399 20 14.7599 20 14.546 19.891C14.3578 19.7951 14.2049 19.6422 14.109 19.4539C14 19.2401 14 18.9601 14 18.4V15.6Z"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        Receptes
      </button>

      <button class="btn" @click="showSection('folders')" :class="{ active: activeSection === 'folders' }">
        <svg width="20" height="20" viewBox="0 0 24 24"
          style="vertical-align: middle; margin-right: 6px; background: transparent; fill: none;"
          xmlns="http://www.w3.org/2000/svg">
          <path
            d="M3 8.2C3 7.07989 3 6.51984 3.21799 6.09202C3.40973 5.71569 3.71569 5.40973 4.09202 5.21799C4.51984 5 5.0799 5 6.2 5H9.67452C10.1637 5 10.4083 5 10.6385 5.05526C10.8425 5.10425 11.0376 5.18506 11.2166 5.29472C11.4184 5.4184 11.5914 5.59135 11.9373 5.93726L12.0627 6.06274C12.4086 6.40865 12.5816 6.5816 12.7834 6.70528C12.9624 6.81494 13.1575 6.89575 13.3615 6.94474C13.5917 7 13.8363 7 14.3255 7H17.8C18.9201 7 19.4802 7 19.908 7.21799C20.2843 7.40973 20.5903 7.71569 20.782 8.09202C21 8.51984 21 9.0799 21 10.2V15.8C21 16.9201 21 17.4802 20.782 17.908C20.5903 18.2843 20.2843 18.5903 19.908 18.782C19.4802 19 18.9201 19 17.8 19H6.2C5.07989 19 4.51984 19 4.09202 18.782C3.71569 18.5903 3.40973 18.2843 3.21799 17.908C3 17.4802 3 16.9201 3 15.8V8.2Z"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        Carpetes
      </button>

      <!-- Botón de Lives -->
      <button class="btn live-btn" @click="showSection('lives')" :class="{ active: activeSection === 'lives' }">
        <svg width="20" height="20" viewBox="0 0 24 24"
          style="vertical-align: middle; margin-right: 6px; background: transparent; fill: none;"
          xmlns="http://www.w3.org/2000/svg">
          <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
          <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
          <g id="SVGRepo_iconCarrier">
            <path
              d="M22 12C22 14.7578 20.8836 17.2549 19.0782 19.064M2 12C2 9.235 3.12222 6.73208 4.93603 4.92188M19.1414 5.00003C19.987 5.86254 20.6775 6.87757 21.1679 8.00003M5 19.1415C4.08988 18.2493 3.34958 17.1845 2.83209 16"
              stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
            <path
              d="M16.2849 8.04397C17.3458 9.05877 18 10.4488 18 11.9822C18 13.5338 17.3302 14.9386 16.2469 15.9564M7.8 16C6.68918 14.9789 6 13.556 6 11.9822C6 10.4266 6.67333 9.01843 7.76162 8"
              stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
            <path
              d="M13.6563 10.4511C14.5521 11.1088 15 11.4376 15 12C15 12.5624 14.5521 12.8912 13.6563 13.5489C13.4091 13.7304 13.1638 13.9014 12.9384 14.0438C12.7407 14.1688 12.5168 14.298 12.2849 14.4249C11.3913 14.914 10.9444 15.1586 10.5437 14.8878C10.1429 14.617 10.1065 14.0502 10.0337 12.9166C10.0131 12.596 10 12.2817 10 12C10 11.7183 10.0131 11.404 10.0337 11.0834C10.1065 9.94977 10.1429 9.38296 10.5437 9.1122C10.9444 8.84144 11.3913 9.08599 12.2849 9.57509C12.5168 9.70198 12.7407 9.83123 12.9384 9.95619C13.1638 10.0986 13.4091 10.2696 13.6563 10.4511Z"
              stroke="currentColor" stroke-width="1.5"></path>
          </g>
        </svg>
        Lives
      </button>
    </div>

    <!-- Mostrar receptes publicades -->
    <div v-if="activeSection === 'recipes'">
      <p><strong>Receptes publicades:</strong></p>
      <div class="recipes">
        <RecipeCard v-for="recipe in user.recipes" :key="recipe.id" :recipeId="recipe.id" :title="recipe.title"
          :description="recipe.description" :image="recipe.image" />
      </div>
      <p v-if="!user.recipes || user.recipes.length === 0">Aquest usuari no té receptes publicades.</p>
    </div>

    <!-- Mostrar carpetes de l'usuari i les seves receptes -->
    <div v-else-if="activeSection === 'folders'">
      <div v-if="user.folders && user.folders.length > 0">
        <p><strong>Carpetes:</strong></p>
        <div v-for="folder in user.folders" :key="folder.id">
          <p><strong>{{ folder.name }}</strong></p>
          <div class="recipes">
            <div v-for="recipe in user.recipes_in_folders[folder.name]" :key="recipe.id">
              <RecipeCard :recipeId="recipe.id" :title="recipe.title" :description="recipe.description"
                :image="recipe.image" />
            </div>
          </div>
        </div>
      </div>
      <p v-if="!user.folders || user.folders.length === 0">Aquest usuari no té carpetes.</p>
    </div>

    <!-- Mostrar Lives programados -->
    <div v-else-if="activeSection === 'lives'">
      <p><strong>Lives programados:</strong></p>
      <div v-if="loading" class="loading">Cargando...</div>
      <div v-else-if="!isChef" class="no-lives">
        <p>Este usuario no es chef y por lo tanto no tiene lives programados.</p>
      </div>
      <div v-else-if="scheduledLives.length === 0" class="no-lives">
        <p>Este chef no tiene lives programados actualmente.</p>
      </div>
      <div v-else class="live-cards">
        <div v-for="live in scheduledLives" :key="live.id" class="live-card">
          <div class="live-info">
            <h4>{{ live.recipe.title }}</h4>
            <p>📅 {{ formatDate(live.dia) }} 🕒 {{ live.hora }}</p>
            <p class="live-description">{{ live.recipe.description }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Error -->
    <p v-if="error" class="error">{{ error }}</p>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue';
import RecipeCard from '@/components/RecipeCard.vue';
import communicationManager from '@/services/communicationManager';

export default {
  props: {
    userId: {
      type: String,
      required: true,
    },
  },
  components: {
    RecipeCard,
  },
  setup(props) {
    const user = ref({
      name: '',
      email: '',
      recipes: [],
      folders: [],
      recipes_in_folders: {},
      role: '',
    });
    const error = ref(null);
    const activeSection = ref('recipes');
    const scheduledLives = ref([]);
    const loading = ref(false);

    // Comprobación mejorada del rol de chef
    const isChef = computed(() => {
      // Verificamos tanto el role del usuario como si es el propio perfil
      return user.value.role?.toLowerCase() === 'chef';
    });

    const fetchUserData = async () => {
      try {
        const response = await communicationManager.getUserInfo(props.userId);
        console.log('Datos del usuario recibidos:', response);
        
        user.value = {
          name: response.user.name,
          email: response.user.email,
          role: response.user.role, // Aseguramos que role se asigne correctamente
          recipes: response.recipes || [],
          folders: response.folders || [],
          recipes_in_folders: response.recipes_in_folders || {},
        };
        
        console.log('Datos del usuario después de procesar:', user.value);
      } catch (err) {
        error.value = 'Hubo un error al cargar los datos del usuario.';
        console.error('Error al cargar datos del usuario:', err);
      }
    };

    const fetchChefLives = async () => {
      if (!isChef.value) {
        console.log('Usuario no es chef, no se cargarán lives');
        scheduledLives.value = [];
        return;
      }
      
      loading.value = true;
      try {
        const response = await communicationManager.getChefLives(props.userId);
        console.log('Respuesta de lives:', response);
        
        // Manejo robusto de la respuesta
        scheduledLives.value = Array.isArray(response?.lives) ? response.lives : 
                              Array.isArray(response) ? response : [];
      } catch (err) {
        console.error('Error al cargar los lives:', err);
        error.value = 'Error al cargar los lives programados';
        scheduledLives.value = [];
      } finally {
        loading.value = false;
      }
    };

    const showSection = async (section) => {
      activeSection.value = section;
      if (section === 'lives') {
        await fetchChefLives();
      }
    };

    const formatDate = (dateString) => {
      try {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString).toLocaleDateString('es-ES', options);
      } catch (e) {
        console.error('Error formateando fecha:', e);
        return dateString;
      }
    };

    onMounted(() => {
      console.log('Componente montado, cargando datos...');
      fetchUserData();
    });

    return {
      user,
      error,
      activeSection,
      scheduledLives,
      loading,
      formatDate,
      isChef,
      showSection
    };
  },

  methods: {
    goBack() {
      this.$router.go(-1);
    }
  },
};
</script>

<style scoped>
.user-profile {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.back-button {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  margin-bottom: 1rem;
}

.profile-buttons {
  display: flex;
  gap: 10px;
  margin: 20px 0;
}

.btn {
  padding: 10px 15px;
  border: 1px solid #ddd;
  border-radius: 5px;
  background-color: #f8f8f8;
  cursor: pointer;
  display: flex;
  align-items: center;
}

.btn:hover {
  background-color: #e8e8e8;
}

.btn.active {
  background-color: #e0e0e0;
  border-color: #ccc;
}

.live-btn {
  background-color: #ff4757;
  color: white;
}

.live-btn:hover {
  background-color: #ff6b81;
}

.recipes {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
  margin-top: 20px;
}

.live-cards {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1rem;
  margin-top: 1rem;
}

.live-card {
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  padding: 1rem;
  background-color: #f9f9f9;
}

.live-info h4 {
  margin: 0 0 0.5rem 0;
  color: #333;
}

.live-description {
  margin: 0.5rem 0 0 0;
  color: #666;
  font-size: 0.9rem;
}

.loading, .no-lives {
  text-align: center;
  padding: 1rem;
  color: #666;
}

.error {
  color: red;
  text-align: center;
  margin-top: 20px;
}
</style>