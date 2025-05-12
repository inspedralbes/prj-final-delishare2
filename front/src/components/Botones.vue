<template>
  <div class="flex flex-col gap-6 p-6">
    <!-- Filtro por categoría -->
    <div class="flex flex-col gap-3">
      <button 
        @click="toggleFilterSection('category')"
        class="text-xl font-bold text-[#166534] hover:text-[#22c55e] transition-colors text-left flex items-center justify-between bg-gradient-to-r from-[#f0fdf4] to-[#dcfce7] p-4 rounded-xl shadow-sm"
      >
        Filtrar per categoria
        <svg 
          class="w-6 h-6 transform transition-transform"
          :class="{ 'rotate-180': activeSection === 'category' }"
          fill="none" 
          stroke="currentColor" 
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </button>
      <div v-if="activeSection === 'category'" class="pl-4 border-l-2 border-[#22c55e] bg-white/50 p-4 rounded-lg">
        <div class="flex flex-wrap gap-2">
      <button 
        v-for="dato in categorias" 
        :key="dato.id" 
        class="px-4 py-2 text-sm font-medium bg-[#4ade80] text-white rounded-full hover:bg-[#22c55e] transition-all duration-200 shadow-sm"
        :class="{ 'ring-2 ring-[#166534] ring-offset-2': selectedCategories.includes(dato.id) }"
        @click="toggleCategory(dato.id)"
      >
        {{ dato.name }}
      </button>
        </div>
      </div>
    </div>

    <!-- Filtro por cocina -->
    <div class="flex flex-col gap-3">
      <button 
        @click="toggleFilterSection('cuisine')"
        class="text-xl font-bold text-[#166534] hover:text-[#22c55e] transition-colors text-left flex items-center justify-between bg-gradient-to-r from-[#f0fdf4] to-[#dcfce7] p-4 rounded-xl shadow-sm"
      >
        Filtrar per cuina
        <svg 
          class="w-6 h-6 transform transition-transform"
          :class="{ 'rotate-180': activeSection === 'cuisine' }"
          fill="none" 
          stroke="currentColor" 
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </button>
      <div v-if="activeSection === 'cuisine'" class="pl-4 border-l-2 border-[#22c55e] bg-white/50 p-4 rounded-lg">
        <div class="flex flex-wrap gap-2">
      <button 
        v-for="cuisine in cuisines" 
        :key="cuisine.id" 
        class="px-4 py-2 text-sm font-medium bg-[#4ade80] text-white rounded-full hover:bg-[#22c55e] transition-all duration-200 shadow-sm"
        :class="{ 'ring-2 ring-[#166534] ring-offset-2': selectedCuisines.includes(cuisine.id) }"
        @click="toggleCuisine(cuisine.id)"
      >
        {{ cuisine.country }}
      </button>
        </div>
      </div>
    </div>

    <!-- Filtro por tiempo -->
    <div class="flex flex-col gap-3">
      <button 
        @click="toggleFilterSection('time')"
        class="text-xl font-bold text-[#166534] hover:text-[#22c55e] transition-colors text-left flex items-center justify-between bg-gradient-to-r from-[#f0fdf4] to-[#dcfce7] p-4 rounded-xl shadow-sm"
      >
        Filtrar per temps
        <svg 
          class="w-6 h-6 transform transition-transform"
          :class="{ 'rotate-180': activeSection === 'time' }"
          fill="none" 
          stroke="currentColor" 
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </button>
      <div v-if="activeSection === 'time'" class="pl-4 border-l-2 border-[#22c55e] bg-white/50 p-4 rounded-lg">
        <div class="flex flex-wrap gap-2">
          <button 
            v-for="time in times" 
            :key="time"
            class="px-4 py-2 text-sm font-medium bg-[#4ade80] text-white rounded-full hover:bg-[#22c55e] transition-all duration-200 shadow-sm"
            :class="{ 'ring-2 ring-[#166534] ring-offset-2': selectedTimes.includes(time) }"
            @click="toggleTime(time)"
          >
            {{ time }} minuts
          </button>
        </div>
      </div>
    </div>

    <!-- Filtro por ingredientes -->
    <div class="flex flex-col gap-3">
      <button 
        @click="toggleFilterSection('ingredients')"
        class="text-xl font-bold text-[#166534] hover:text-[#22c55e] transition-colors text-left flex items-center justify-between bg-gradient-to-r from-[#f0fdf4] to-[#dcfce7] p-4 rounded-xl shadow-sm"
      >
        Filtrar per ingredients
        <svg 
          class="w-6 h-6 transform transition-transform"
          :class="{ 'rotate-180': activeSection === 'ingredients' }"
          fill="none" 
          stroke="currentColor" 
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </button>
      <div v-if="activeSection === 'ingredients'" class="pl-4 border-l-2 border-[#22c55e] bg-white/50 p-4 rounded-lg">
        <!-- Barra de búsqueda de ingredientes -->
        <div class="mb-4">
          <input 
            type="text" 
            v-model="ingredientSearch" 
            placeholder="Cerca ingredients..." 
            class="w-full px-4 py-2 border border-[#22c55e] rounded-full focus:ring-2 focus:ring-[#22c55e] focus:border-transparent outline-none transition-all duration-200"
          />
        </div>
        <div class="flex flex-wrap gap-2">
          <button 
            v-for="ingredient in displayedIngredients" 
            :key="ingredient"
            class="px-4 py-2 text-sm font-medium bg-[#4ade80] text-white rounded-full hover:bg-[#22c55e] transition-all duration-200 shadow-sm"
            :class="{ 'ring-2 ring-[#166534] ring-offset-2': isIngredientSelected(ingredient) }"
            @click="toggleIngredient(ingredient)"
          >
            {{ ingredient }}
          </button>
        </div>
        <!-- Botón de mostrar más -->
        <div v-if="hasMoreIngredients" class="mt-4 text-center">
          <button 
            @click="loadMoreIngredients" 
            class="text-[#166534] hover:text-[#22c55e] font-medium text-sm flex items-center gap-1 mx-auto"
          >
            Mostrar més ingredients
            <svg 
              class="w-4 h-4" 
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>
        </div>
        <div v-if="selectedIngredients.length" class="flex flex-wrap items-center gap-2 mt-4 p-3 bg-[#f0fdf4] rounded-lg">
          <span class="font-bold text-[#166534]">Seleccionats:</span>
          <span v-for="ingredient in selectedIngredients" :key="ingredient" class="inline-flex items-center gap-1 px-3 py-1 bg-gradient-to-r from-[#22c55e] to-[#a3e635] text-white rounded-full text-sm">
            {{ ingredient }}
            <button @click="removeIngredient(ingredient)" class="text-white hover:text-[#f0fdf4]">×</button>
          </span>
        </div>
      </div>
    </div>

    <!-- Filtro por dificultad -->
    <div class="flex flex-col gap-3">
      <button 
        @click="toggleFilterSection('difficulty')"
        class="text-xl font-bold text-[#166534] hover:text-[#22c55e] transition-colors text-left flex items-center justify-between bg-gradient-to-r from-[#f0fdf4] to-[#dcfce7] p-4 rounded-xl shadow-sm"
      >
        Filtrar per dificultat
        <svg 
          class="w-6 h-6 transform transition-transform"
          :class="{ 'rotate-180': activeSection === 'difficulty' }"
          fill="none" 
          stroke="currentColor" 
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </button>
      <div v-if="activeSection === 'difficulty'" class="pl-4 border-l-2 border-[#22c55e] bg-white/50 p-4 rounded-lg">
        <div class="flex flex-wrap gap-2">
          <button 
            class="px-4 py-2 text-sm font-medium bg-[#4ade80] text-white rounded-full hover:bg-[#22c55e] transition-all duration-200 shadow-sm"
            :class="{ 'ring-2 ring-[#166534] ring-offset-2': selectedDifficulties.includes('facil') }"
            @click="toggleDifficulty('facil')"
          >
            Fàcil (1-5 passos)
          </button>
          <button 
            class="px-4 py-2 text-sm font-medium bg-[#4ade80] text-white rounded-full hover:bg-[#22c55e] transition-all duration-200 shadow-sm"
            :class="{ 'ring-2 ring-[#166534] ring-offset-2': selectedDifficulties.includes('regular') }"
            @click="toggleDifficulty('regular')"
          >
            Regular (6-10 passos)
          </button>
          <button 
            class="px-4 py-2 text-sm font-medium bg-[#4ade80] text-white rounded-full hover:bg-[#22c55e] transition-all duration-200 shadow-sm"
            :class="{ 'ring-2 ring-[#166534] ring-offset-2': selectedDifficulties.includes('dificil') }"
            @click="toggleDifficulty('dificil')"
          >
            Difícil (10+ passos)
          </button>
        </div>
      </div>
    </div>

    <!-- Resumen de filtros seleccionados -->
    <div v-if="hasSelectedFilters" class="mt-4 p-4 bg-gradient-to-r from-[#f0fdf4] to-[#dcfce7] rounded-xl">
      <h3 class="font-bold text-[#166534] mb-3">Filtres seleccionats:</h3>
      <div class="flex flex-wrap gap-2">
        <span v-for="category in selectedCategories" :key="'cat-'+category" class="px-3 py-1 bg-gradient-to-r from-[#22c55e] to-[#a3e635] text-white rounded-full text-sm">
          {{ getCategoryName(category) }}
        </span>
        <span v-for="cuisine in selectedCuisines" :key="'cuis-'+cuisine" class="px-3 py-1 bg-gradient-to-r from-[#22c55e] to-[#a3e635] text-white rounded-full text-sm">
          {{ getCuisineName(cuisine) }}
        </span>
        <span v-for="time in selectedTimes" :key="'time-'+time" class="px-3 py-1 bg-gradient-to-r from-[#22c55e] to-[#a3e635] text-white rounded-full text-sm">
          {{ time }} min
        </span>
        <span v-for="difficulty in selectedDifficulties" :key="'diff-'+difficulty" class="px-3 py-1 bg-gradient-to-r from-[#22c55e] to-[#a3e635] text-white rounded-full text-sm">
          {{ getDifficultyName(difficulty) }}
        </span>
        <span v-for="ingredient in selectedIngredients" :key="'ing-'+ingredient" class="px-3 py-1 bg-gradient-to-r from-[#22c55e] to-[#a3e635] text-white rounded-full text-sm">
          {{ ingredient }}
        </span>
      </div>
    </div>

    <!-- Botones de control -->
    <div class="flex justify-end gap-3 mt-6">
      <button 
        @click="applyAllFilters" 
        class="px-4 py-2 text-sm bg-gradient-to-r from-[#22c55e] to-[#a3e635] text-white rounded-full hover:from-[#16a34a] hover:to-[#bef264] transition-all duration-200 shadow-sm font-medium"
        :disabled="!hasSelectedFilters"
        :class="{ 'opacity-50 cursor-not-allowed': !hasSelectedFilters }"
      >
        Aplicar filtres
      </button>
      <button 
        @click="clearAllFilters" 
        class="px-4 py-2 text-sm bg-gradient-to-r from-red-500 to-red-600 text-white rounded-full hover:from-red-600 hover:to-red-700 transition-all duration-200 shadow-sm font-medium"
        :disabled="!hasSelectedFilters"
        :class="{ 'opacity-50 cursor-not-allowed': !hasSelectedFilters }"
      >
        Netejar filtres
      </button>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed, watch } from 'vue';
import communicationManager from '../services/communicationManager';
import RecipeCard from './RecipeCard.vue';

export default {
  name: 'Botones',
  components: { RecipeCard },
  props: {
    allRecipes: {
      type: Array,
      default: () => []
    }
  },
  setup(props, { emit }) {
    const categorias = ref([]);
    const cuisines = ref([]);
    const times = ref([]);
    const ingredients = ref([]);
    const selectedIngredients = ref([]);
    const selectedCategories = ref([]);
    const selectedCuisines = ref([]);
    const selectedTimes = ref([]);
    const selectedDifficulties = ref([]);
    const isLoading = ref(false);
    const activeSection = ref(null);
    const ingredientSearch = ref('');
    const displayedIngredientsCount = ref(10);

    // Computed properties
    const hasSelectedFilters = computed(() => {
      return selectedCategories.value.length > 0 ||
             selectedCuisines.value.length > 0 ||
             selectedTimes.value.length > 0 ||
             selectedDifficulties.value.length > 0 ||
             selectedIngredients.value.length > 0;
    });

    // Computed property para filtrar ingredientes según la búsqueda
    const filteredIngredients = computed(() => {
      if (!ingredientSearch.value) return ingredients.value;
      const searchTerm = ingredientSearch.value.toLowerCase();
      return ingredients.value.filter(ingredient => 
        ingredient.toLowerCase().includes(searchTerm)
      );
    });

    // Computed property para los ingredientes mostrados
    const displayedIngredients = computed(() => {
      return filteredIngredients.value.slice(0, displayedIngredientsCount.value);
    });

    // Computed property para verificar si hay más ingredientes por mostrar
    const hasMoreIngredients = computed(() => {
      return displayedIngredientsCount.value < filteredIngredients.value.length;
    });

    // Función para cargar más ingredientes
    const loadMoreIngredients = () => {
      displayedIngredientsCount.value += 5;
    };

    // Resetear el contador cuando se cambia la búsqueda
    const resetDisplayedCount = () => {
      displayedIngredientsCount.value = 10;
    };

    // Watch para resetear el contador cuando cambia la búsqueda
    watch(ingredientSearch, () => {
      resetDisplayedCount();
    });

    onMounted(() => {
      obtenerCategorias();
      obtenerCuisines();
      obtenerTimes();
      obtenerIngredients();
    });

    const toggleFilterSection = (section) => {
      activeSection.value = activeSection.value === section ? null : section;
    };

    const obtenerCategorias = async () => {
      try {
        categorias.value = await communicationManager.fetchCategories();
      } catch (error) {
        console.error('Error al obtener categorías:', error);
      }
    };

    const obtenerCuisines = async () => {
      try {
        cuisines.value = await communicationManager.fetchCuisines();
      } catch (error) {
        console.error('Error al obtener cocinas:', error);
      }
    };

    const obtenerTimes = async () => {
      try {
        const response = await communicationManager.getAllTimes();
        times.value = response.times;
      } catch (error) {
        console.error('Error al obtener tiempos:', error);
      }
    };

    const obtenerIngredients = async () => {
      try {
        const response = await communicationManager.fetchIngredients();
        ingredients.value = response.ingredients;
      } catch (error) {
        console.error('Error al obtener ingredientes:', error);
      }
    };

    const toggleCategory = (categoryId) => {
      const index = selectedCategories.value.indexOf(categoryId);
      if (index === -1) {
        selectedCategories.value.push(categoryId);
      } else {
        selectedCategories.value.splice(index, 1);
      }
    };

    const toggleCuisine = (cuisineId) => {
      const index = selectedCuisines.value.indexOf(cuisineId);
      if (index === -1) {
        selectedCuisines.value.push(cuisineId);
      } else {
        selectedCuisines.value.splice(index, 1);
      }
    };

    const toggleTime = (time) => {
      const index = selectedTimes.value.indexOf(time);
      if (index === -1) {
        selectedTimes.value.push(time);
      } else {
        selectedTimes.value.splice(index, 1);
      }
    };

    const toggleDifficulty = (difficulty) => {
      const index = selectedDifficulties.value.indexOf(difficulty);
      if (index === -1) {
        selectedDifficulties.value.push(difficulty);
      } else {
        selectedDifficulties.value.splice(index, 1);
      }
    };

    // Funciones helper para obtener nombres
    const getCategoryName = (categoryId) => {
      const category = categorias.value.find(c => c.id === categoryId);
      return category ? category.name : '';
    };

    const getCuisineName = (cuisineId) => {
      const cuisine = cuisines.value.find(c => c.id === cuisineId);
      return cuisine ? cuisine.country : '';
    };

    const getDifficultyName = (difficulty) => {
      const names = {
        'facil': 'Fàcil',
        'regular': 'Regular',
        'dificil': 'Difícil'
      };
      return names[difficulty] || difficulty;
    };

    // Función para aplicar todos los filtros
    const applyAllFilters = async () => {
      if (!hasSelectedFilters.value) return;

      isLoading.value = true;
      try {
        // Obtener todas las recetas si no están disponibles
        let allRecipes = props.allRecipes;
        if (!allRecipes || allRecipes.length === 0) {
          const response = await communicationManager.fetchRecipes();
          allRecipes = Array.isArray(response) ? response : [];
        }

        let filteredRecipes = [...allRecipes];

        // Aplicar filtros de categoría
        if (selectedCategories.value.length > 0) {
          const categoryRecipes = await Promise.all(
            selectedCategories.value.map(categoryId => 
              communicationManager.fetchRecipesByCategory(categoryId)
            )
          );
          const categoryRecipeIds = new Set(
            categoryRecipes.flatMap(response => {
              if (Array.isArray(response)) return response.map(r => r.id);
              if (response && response.recipes) return response.recipes.map(r => r.id);
              return [];
            })
          );
          filteredRecipes = filteredRecipes.filter(recipe => 
            categoryRecipeIds.has(recipe.id)
          );
        }

        // Aplicar filtros de cocina
        if (selectedCuisines.value.length > 0) {
          const cuisineRecipes = await Promise.all(
            selectedCuisines.value.map(cuisineId => 
              communicationManager.fetchRecipesByCuisine(cuisineId)
            )
          );
          const cuisineRecipeIds = new Set(
            cuisineRecipes.flatMap(response => {
              if (Array.isArray(response)) return response.map(r => r.id);
              if (response && response.recipes) return response.recipes.map(r => r.id);
              return [];
            })
          );
          filteredRecipes = filteredRecipes.filter(recipe => 
            cuisineRecipeIds.has(recipe.id)
          );
        }

        // Aplicar filtros de tiempo
        if (selectedTimes.value.length > 0) {
          const timeRecipes = await Promise.all(
            selectedTimes.value.map(time => 
              communicationManager.fetchRecipesByTime(time)
            )
          );
          const timeRecipeIds = new Set(
            timeRecipes.flatMap(response => {
              if (Array.isArray(response)) return response.map(r => r.id);
              if (response && response.recipes) return response.recipes.map(r => r.id);
              return [];
            })
          );
          filteredRecipes = filteredRecipes.filter(recipe => 
            timeRecipeIds.has(recipe.id)
          );
        }

        // Aplicar filtros de ingredientes
        if (selectedIngredients.value.length > 0) {
          const ingredientRecipes = await communicationManager.fetchRecipesByIngredients(
            selectedIngredients.value
          );
          const ingredientRecipeIds = new Set(
            Array.isArray(ingredientRecipes) ? ingredientRecipes.map(r => r.id) :
            ingredientRecipes && ingredientRecipes.recipes ? ingredientRecipes.recipes.map(r => r.id) : []
          );
          filteredRecipes = filteredRecipes.filter(recipe => 
            ingredientRecipeIds.has(recipe.id)
          );
        }

        // Aplicar filtros de dificultad
        if (selectedDifficulties.value.length > 0) {
          const difficultyRecipes = await Promise.all(
            filteredRecipes.map(async (recipe) => {
              try {
                const stepsResponse = await communicationManager.getRecipeSteps(recipe.id);
                const steps = Array.isArray(stepsResponse) ? stepsResponse :
                            stepsResponse && stepsResponse.steps ? stepsResponse.steps : [];
                const numSteps = steps.length;
                let difficulty;
                if (numSteps <= 5) difficulty = 'facil';
                else if (numSteps <= 10) difficulty = 'regular';
                else difficulty = 'dificil';
                return selectedDifficulties.value.includes(difficulty) ? recipe : null;
          } catch (error) {
                console.error(`Error al obtener pasos para la receta ${recipe.id}:`, error);
                return null;
              }
            })
          );
          filteredRecipes = difficultyRecipes.filter(recipe => recipe !== null);
        }

        console.log('Recetas filtradas:', filteredRecipes); // Debug log

        // Emitir las recetas filtradas
        if (filteredRecipes.length > 0) {
          emit('filtradoPorCategoria', filteredRecipes);
          emit('closeDrawer');
        } else {
          // Si no hay recetas filtradas, emitir un array vacío
          emit('filtradoPorCategoria', []);
        }
      } catch (error) {
        console.error('Error al aplicar filtros:', error);
        emit('filtradoPorCategoria', []);
      } finally {
        isLoading.value = false;
      }
    };

    // Función para limpiar todos los filtros
    const clearAllFilters = () => {
      selectedCategories.value = [];
      selectedCuisines.value = [];
      selectedTimes.value = [];
      selectedDifficulties.value = [];
      selectedIngredients.value = [];
    };

    const toggleIngredient = (ingredient) => {
      const index = selectedIngredients.value.indexOf(ingredient);
      if (index === -1) {
        selectedIngredients.value.push(ingredient);
      } else {
        selectedIngredients.value.splice(index, 1);
      }
    };

    const removeIngredient = (ingredient) => {
      selectedIngredients.value = selectedIngredients.value.filter(i => i !== ingredient);
    };
    
    const isIngredientSelected = (ingredient) => {
      return selectedIngredients.value.includes(ingredient);
    };

    return {
      categorias,
      cuisines,
      times,
      ingredients,
      selectedIngredients,
      selectedCategories,
      selectedCuisines,
      selectedTimes,
      selectedDifficulties,
      isLoading,
      activeSection,
      ingredientSearch,
      displayedIngredients,
      hasMoreIngredients,
      hasSelectedFilters,
      loadMoreIngredients,
      toggleFilterSection,
      toggleCategory,
      toggleCuisine,
      toggleTime,
      toggleDifficulty,
      getCategoryName,
      getCuisineName,
      getDifficultyName,
      applyAllFilters,
      clearAllFilters,
      toggleIngredient,
      removeIngredient,
      isIngredientSelected,
    };
  },
};
</script>
