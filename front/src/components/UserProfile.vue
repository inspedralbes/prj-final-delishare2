<template>
  <div class="min-h-screen bg-lime-50">
    <!-- Header with back button -->
    <div class="bg-white shadow sticky top-0 z-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center h-16">
          <button @click="goBack" class="text-lime-700 hover:text-lime-500 transition-colors">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </button>
          <h1 class="ml-4 text-2xl font-bold text-lime-700">Perfil d'Usuari</h1>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- User info card -->
      <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
        <div class="flex items-center space-x-4">
          <div v-if="user.img" class="h-20 w-20 rounded-full overflow-hidden">
            <img :src="user.img" :alt="user.name" class="h-full w-full object-cover" />
          </div>
          <div v-else class="h-20 w-20 rounded-full bg-gradient-to-r from-green-500 via-lime-400 to-lime-300 flex items-center justify-center text-lime-900 text-2xl font-bold">
            {{ user.name.charAt(0).toUpperCase() }}
          </div>
          <div>
            <h2 class="text-xl font-semibold text-lime-700">{{ user.name }}</h2>
            <p class="text-gray-600">{{ user.bio }}</p>
          </div>
        </div>
      </div>

      <!-- Navigation tabs -->
      <div class="flex space-x-4 mb-8">
        <button 
          v-for="section in sections" 
          :key="section.id"
          @click="showSection(section.id)"
          :class="[
            'flex-1 py-2 px-4 text-sm rounded-full font-semibold transition-all duration-300',
            activeSection === section.id 
              ? 'bg-gradient-to-r from-green-500 via-lime-400 to-lime-300 text-lime-900 shadow-lg hover:shadow-xl hover:brightness-110' 
              : 'bg-gradient-to-r from-green-100 via-lime-50 to-lime-100 text-lime-700 hover:from-green-200 hover:via-lime-100 hover:to-lime-200 hover:shadow-md'
          ]"
        >
          <component :is="section.icon" class="w-5 h-5 mr-2" />
          {{ section.label }}
        </button>
      </div>

      <!-- Content sections -->
      <div class="space-y-8">
        <!-- Recipes section -->
        <div v-if="activeSection === 'recipes'" class="space-y-6">
          <h3 class="text-xl font-semibold text-lime-700">Receptes publicades</h3>
          <div v-if="user.recipes && user.recipes.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <RecipeCard 
              v-for="recipe in user.recipes" 
              :key="recipe.id" 
              :recipeId="recipe.id" 
              :title="recipe.title"
              :description="recipe.description" 
              :image="recipe.image"
              class="transform transition-all duration-200 hover:scale-105 hover:shadow-lg bg-white rounded-xl shadow-md p-4"
            />
          </div>
          <p v-else class="text-gray-600 text-center py-8 bg-white rounded-xl shadow-md">
            Aquest usuari no té receptes publicades.
          </p>
        </div>

        <!-- Folders section -->
        <div v-else-if="activeSection === 'folders'" class="space-y-6">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-lime-700">Carpetes</h3>
            <span class="text-sm text-gray-500">{{ user.folders?.length || 0 }} carpeta{{ user.folders?.length !== 1 ? 's' : '' }}</span>
          </div>
          <div v-if="user.folders && user.folders.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div v-for="folder in user.folders" :key="folder.id" 
              class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
              <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                  <div class="p-2 bg-lime-100 rounded-lg">
                    <svg class="w-6 h-6 text-lime-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                  </div>
                  <h4 class="text-lg font-medium text-lime-700">
                    {{ folder.name }}
                  </h4>
                </div>
                <span class="px-3 py-1 text-sm bg-lime-100 text-lime-700 rounded-full">
                  {{ folder.recipes.length }} receta{{ folder.recipes.length !== 1 ? 's' : '' }}
                </span>
              </div>
              
              <div v-if="folder.recipes && folder.recipes.length > 0" 
                class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                <div v-for="recipe in folder.recipes.slice(0, 4)" :key="recipe.id" 
                  class="group relative overflow-hidden rounded-lg bg-gray-50 hover:bg-lime-50 transition-colors duration-200">
                  <div class="aspect-w-16 aspect-h-9">
                    <img :src="recipe.image" :alt="recipe.title" 
                      class="w-full h-24 object-cover rounded-lg group-hover:opacity-90 transition-opacity duration-200" />
                  </div>
                  <div class="p-2">
                    <h5 class="text-sm font-medium text-gray-900 truncate">{{ recipe.title }}</h5>
                    <p class="text-xs text-gray-500 truncate">{{ recipe.description }}</p>
                  </div>
                </div>
                <div v-if="folder.recipes.length > 4" 
                  class="flex items-center justify-center bg-lime-50 rounded-lg p-4 text-lime-700 font-medium">
                  +{{ folder.recipes.length - 4 }} més
                </div>
              </div>
              
              <p v-else class="text-gray-500 text-center py-4 bg-gray-50 rounded-lg mt-4">
                Aquesta carpeta no té receptes.
              </p>
            </div>
          </div>
          <p v-else class="text-gray-600 text-center py-8 bg-white rounded-xl shadow-md">
            Aquest usuari no té carpetes.
          </p>
        </div>

        <!-- Lives section -->
        <div v-else-if="activeSection === 'lives'" class="space-y-6">
          <h3 class="text-xl font-semibold text-lime-700">Lives programados</h3>
          <div v-if="loading" class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-lime-500"></div>
          </div>
          <div v-else-if="!isChef" class="text-gray-600 text-center py-8 bg-white rounded-xl shadow-md">
            Este usuario no es chef y por lo tanto no tiene lives programados.
          </div>
          <div v-else-if="scheduledLives.length === 0" class="text-gray-600 text-center py-8 bg-white rounded-xl shadow-md">
            Este chef no tiene lives programados actualmente.
          </div>
          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="live in scheduledLives" :key="live.id" 
              @click="goToLive(live.id)"
              class="bg-white rounded-xl shadow-md p-6 transform transition-all duration-200 hover:scale-105 hover:shadow-lg cursor-pointer">
              <div class="space-y-4">
                <h4 class="text-lg font-medium text-lime-700">{{ live.recipe.title }}</h4>
                <div class="flex items-center space-x-4 text-sm text-gray-600">
                  <span class="flex items-center">
                    <svg class="w-5 h-5 mr-1 text-lime-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ formatDate(live.dia) }}
                  </span>
                  <span class="flex items-center">
                    <svg class="w-5 h-5 mr-1 text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ live.hora }}
                  </span>
                </div>
                <p class="text-gray-600">{{ live.recipe.description }}</p>
                <div class="flex items-center justify-end">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-lime-100 text-lime-700">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    Veure Live
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Error message -->
      <div v-if="error" class="mt-6 bg-white border-l-4 border-lime-500 p-4 rounded-r-xl shadow-md">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-lime-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-lime-700">{{ error }}</p>
          </div>
        </div>
      </div>
    </div>
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
      bio: '',
      recipes: [],
      folders: [],
      recipes_in_folders: {},
      role: '',
      img: '',
    });
    const error = ref(null);
    const activeSection = ref('recipes');
    const scheduledLives = ref([]);
    const loading = ref(false);

    const sections = [
      {
        id: 'recipes',
        label: 'Receptes',
        icon: 'RecipeIcon'
      },
      {
        id: 'folders',
        label: 'Carpetes',
        icon: 'FolderIcon'
      },
      {
        id: 'lives',
        label: 'Lives',
        icon: 'LiveIcon'
      }
    ];

    const isChef = computed(() => {
      return user.value.role?.toLowerCase() === 'chef';
    });

    const fetchUserData = async () => {
      try {
        const response = await communicationManager.getUserInfo(props.userId);
        user.value = {
          name: response.user.name,
          bio: response.user.bio,
          role: response.user.role,
          img: response.user.img,
          recipes: response.recipes || [],
          folders: response.folders || [],
          recipes_in_folders: response.recipes_in_folders || {},
        };
      } catch (err) {
        error.value = 'Hubo un error al cargar los datos del usuario.';
        console.error('Error al cargar datos del usuario:', err);
      }
    };

    const fetchChefLives = async () => {
      if (!isChef.value) {
        scheduledLives.value = [];
        return;
      }
      
      loading.value = true;
      try {
        const response = await communicationManager.getChefLives(props.userId);
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
      fetchUserData();
    });

    return {
      user,
      error,
      activeSection,
      scheduledLives,
      loading,
      sections,
      formatDate,
      isChef,
      showSection
    };
  },

  methods: {
    goBack() {
      this.$router.go(-1);
    },
    goToLive(liveId) {
      this.$router.push(`/chat/${liveId}`);
    }
  },
};
</script>
