<template>
  <div class="filter-buttons">
    <!-- Botones principales en fila -->
    <div class="button-group">
      <button @click="toggleSubButtons('categoria')" class="button-main">Categoria</button>
      <button @click="toggleSubButtons('cuisine')" class="button-main">Cuina</button>
      <button @click="toggleSubButtons('tiempo')" class="button-main">Temps</button>
      <button @click="toggleSubButtons('ingredientes')" class="button-main">Ingredientes</button>
      <button @click="toggleSubButtons('dificultad')" class="button-main">Dificultad</button>
    </div>

    <!-- Subbotones para Categorías -->
    <div v-if="activeButton === 'categoria'" class="subbutton-group">
      <button 
        v-for="dato in categorias" 
        :key="dato.id" 
        class="button-secondary"
        @click="filtrarPorCategoria(dato.id)"
      >
        {{ dato.name }}
      </button>
    </div>

    <!-- Subbotones para Cocinas -->
    <div v-if="activeButton === 'cuisine'" class="subbutton-group">
      <button 
        v-for="cuisine in cuisines" 
        :key="cuisine.id" 
        class="button-secondary"
        @click="filtrarPorCuisine(cuisine.id)"
      >
        {{ cuisine.country }}
      </button>
    </div>

    <!-- Subbotones para Tiempo -->
    <div v-if="activeButton === 'tiempo'" class="subbutton-group">
      <button 
        v-for="time in times" 
        :key="time"
        class="button-secondary"
        @click="filtrarPorTiempo(time)"
      >
        {{ time }} minutos
      </button>
    </div>

    <!-- Subbotones para Ingredientes -->
    <div v-if="activeButton === 'ingredientes'" class="subbutton-group">
      <div class="ingredient-selection">
        <!-- Mostrar ingredientes seleccionados -->
        <div v-if="selectedIngredients.length" class="selected-ingredients">
          <span class="selected-label">Seleccionados:</span>
          <span v-for="ingredient in selectedIngredients" :key="ingredient" class="ingredient-tag">
            {{ ingredient }}
            <button @click="removeIngredient(ingredient)" class="remove-btn">×</button>
          </span>
          <button @click="applyIngredientFilter" class="apply-btn">Aplicar Filtro</button>
          <button @click="clearIngredients" class="clear-btn">Limpiar</button>
        </div>
        
        <!-- Lista de ingredientes disponibles -->
        <div class="ingredient-list">
          <button 
            v-for="ingredient in ingredients" 
            :key="ingredient"
            class="button-secondary"
            :class="{ 'selected': isIngredientSelected(ingredient) }"
            @click="toggleIngredient(ingredient)"
          >
            {{ ingredient }}
          </button>
        </div>
      </div>
    </div>

    <!-- Subbotones para Dificultad -->
    <div v-if="activeButton === 'dificultad'" class="subbutton-group">
      <button 
        class="button-secondary"
        @click="filtrarPorDificultad('facil')"
      >
        Fácil (1-5 pasos)
      </button>
      <button 
        class="button-secondary"
        @click="filtrarPorDificultad('regular')"
      >
        Regular (6-10 pasos)
      </button>
      <button 
        class="button-secondary"
        @click="filtrarPorDificultad('dificil')"
      >
        Difícil (10+ pasos)
      </button>
    </div>

    <!-- Recetas filtradas -->
    <div v-if="recetas.length" class="recipe-list">
      <RecipeCard
        v-for="receta in recetas"
        :key="receta.id"
        :recipeId="receta.id"
        :title="receta.title"
        :description="receta.description || 'Descripción no disponible'"
        :image="receta.image"
      />
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
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
    // Variables reactivas para almacenar los datos
    const categorias = ref([]);
    const cuisines = ref([]);
    const times = ref([]);
    const ingredients = ref([]);
    const recetas = ref([]);
    const activeButton = ref('');
    const selectedIngredients = ref([]);
    const isLoading = ref(false);

    // Cargar datos al montar el componente
    onMounted(() => {
      obtenerCategorias();
      obtenerCuisines();
      obtenerTimes();
      obtenerIngredients();
    });

    // Función para obtener las categorías
    const obtenerCategorias = async () => {
      try {
        categorias.value = await communicationManager.fetchCategories();
      } catch (error) {
        console.error('Error al obtener categorías:', error);
      }
    };

    // Función para obtener las cocinas (países)
    const obtenerCuisines = async () => {
      try {
        cuisines.value = await communicationManager.fetchCuisines();
      } catch (error) {
        console.error('Error al obtener cocinas:', error);
      }
    };

    // Función para obtener los tiempos disponibles
    const obtenerTimes = async () => {
      try {
        const response = await communicationManager.getAllTimes();
        times.value = response.times;
      } catch (error) {
        console.error('Error al obtener tiempos:', error);
      }
    };

    // Función para obtener los ingredientes
    const obtenerIngredients = async () => {
      try {
        const response = await communicationManager.fetchIngredients();
        ingredients.value = response.ingredients;
      } catch (error) {
        console.error('Error al obtener ingredientes:', error);
      }
    };

    // Función para filtrar por categoría
    const filtrarPorCategoria = async (categoryId) => {
      try {
        const response = await communicationManager.fetchRecipesByCategory(categoryId);
        recetas.value = response.recipes;
        emit('filtradoPorCategoria', true);
      } catch (error) {
        console.error('Error al filtrar recetas por categoría:', error);
      }
    };

    // Función para filtrar por cocina (país)
    const filtrarPorCuisine = async (cuisineId) => {
      try {
        const response = await communicationManager.fetchRecipesByCuisine(cuisineId);
        recetas.value = response.recipes;
        emit('filtradoPorCuisine', true);
      } catch (error) {
        console.error('Error al filtrar recetas por cocina:', error);
      }
    };

    // Función para filtrar por tiempo
    const filtrarPorTiempo = async (time) => {
      try {
        const response = await communicationManager.fetchRecipesByTime(time);
        recetas.value = response.recipes;
        emit('filtradoPorTiempo', true);
      } catch (error) {
        console.error('Error al filtrar recetas por tiempo:', error);
      }
    };

    // Función para filtrar por dificultad (basado en número de pasos)
    const filtrarPorDificultad = async (nivel) => {
      isLoading.value = true;
      try {
        // Usamos las recetas proporcionadas como prop o fetchRecipes si no hay
        const todasLasRecetas = props.allRecipes.length > 0 
          ? props.allRecipes 
          : await communicationManager.fetchRecipes();
        
        // Array para almacenar las recetas filtradas
        const recetasFiltradas = [];
        
        // Para cada receta, obtenemos sus pasos y evaluamos su dificultad
        for (const receta of todasLasRecetas) {
          try {
            const stepsResponse = await communicationManager.getRecipeSteps(receta.id);
            const numSteps = stepsResponse.steps.length;
            
            // Clasificamos según el número de pasos
            let dificultad;
            if (numSteps <= 5) {
              dificultad = 'facil';
            } else if (numSteps <= 10) {
              dificultad = 'regular';
            } else {
              dificultad = 'dificil';
            }
            
            // Si coincide con el nivel solicitado, la agregamos
            if (dificultad === nivel) {
              recetasFiltradas.push(receta);
            }
          } catch (error) {
            console.error(`Error al obtener pasos para la receta ${receta.id}:`, error);
          }
        }
        
        // Actualizamos el listado de recetas filtradas
        recetas.value = recetasFiltradas;
        emit('filtradoPorDificultad', recetasFiltradas);
      } catch (error) {
        console.error('Error al filtrar recetas por dificultad:', error);
      } finally {
        isLoading.value = false;
      }
    };

    // Alterna la visualización de los subbotones según el botón principal
    const toggleSubButtons = (buttonName) => {
      activeButton.value = activeButton.value === buttonName ? '' : buttonName;
    };

    // Añade o quita un ingrediente de la selección
    const toggleIngredient = (ingredient) => {
      const index = selectedIngredients.value.indexOf(ingredient);
      if (index === -1) {
        selectedIngredients.value.push(ingredient);
      } else {
        selectedIngredients.value.splice(index, 1);
      }
    };

    // Elimina un ingrediente de la selección
    const removeIngredient = (ingredient) => {
      selectedIngredients.value = selectedIngredients.value.filter(i => i !== ingredient);
    };
    
    // Verifica si un ingrediente está seleccionado
    const isIngredientSelected = (ingredient) => {
      return selectedIngredients.value.includes(ingredient);
    };

    // Limpia todos los ingredientes seleccionados
    const clearIngredients = () => {
      selectedIngredients.value = [];
      recetas.value = [];
    };
    
    // Aplica el filtro con los ingredientes seleccionados
    const applyIngredientFilter = async () => {
      if (selectedIngredients.value.length === 0) {
        recetas.value = [];
        return;
      }

      try {
        const response = await communicationManager.fetchRecipesByIngredients(selectedIngredients.value);
        recetas.value = response.recipes;
        emit('filtradoPorIngrediente', true);
      } catch (error) {
        console.error('Error al filtrar recetas por ingredientes:', error);
      }
    };

    return {
      categorias,
      cuisines,
      times,
      ingredients,
      recetas,
      activeButton,
      selectedIngredients,
      isLoading,
      obtenerCategorias,
      obtenerCuisines,
      obtenerTimes,
      obtenerIngredients,
      filtrarPorCategoria,
      filtrarPorCuisine,
      filtrarPorTiempo,
      filtrarPorDificultad,
      toggleSubButtons,
      toggleIngredient,
      removeIngredient,
      isIngredientSelected,
      clearIngredients,
      applyIngredientFilter,
    };
  },
};
</script>

<style scoped>
/* Contenedor para los botones principales alineados en fila */
.button-group {
  display: flex;
  gap: 10px;
  justify-content: center;
  margin-bottom: 20px;
}

/* Botones principales (azul más oscuro, más pequeños, en fila) */
.button-main {
  font-size: 0.85rem;
  padding: 8px 12px;
  background-color: #0c0636;
  border: none;
  color: white;
  cursor: pointer;
  border-radius: 5px;
  display: inline-block;
}

.button-main:hover {
  background-color: #322b5f;
}

/* Botones secundarios (verde suave) */
.button-secondary {
  font-size: 0.85rem;
  padding: 8px 12px;
  background-color: #02067d;
  border: none;
  color: white;
  cursor: pointer;
  border-radius: 5px;
  margin: 5px 5px;
  transition: all 0.2s;
}

.button-secondary:hover {
  background-color: #4545a0;
}

.button-secondary.selected {
  background-color: #4CAF50;
  font-weight: bold;
}

/* Estilo de los subbotones, centrados */
.subbutton-group {
  margin-top: 10px;
}

/* Estilo de la lista de recetas */
.recipe-list {
  display: grid;
  grid-template-columns: repeat(2, 1fr); 
  gap: 20px;
  justify-items: center;
  margin-top: 20px;
}

/* Estilos para la selección de ingredientes */
.ingredient-selection {
  max-width: 800px;
  margin: 0 auto;
}

.selected-ingredients {
  margin-bottom: 15px;
  padding: 10px;
  background-color: #f5f5f5;
  border-radius: 5px;
}

.selected-label {
  font-weight: bold;
  margin-right: 10px;
}

.ingredient-tag {
  display: inline-block;
  background-color: #4CAF50;
  color: white;
  padding: 3px 8px;
  border-radius: 15px;
  margin-right: 8px;
  margin-bottom: 5px;
  font-size: 0.8rem;
}

.remove-btn {
  background: none;
  border: none;
  color: white;
  cursor: pointer;
  margin-left: 5px;
  padding: 0;
  font-weight: bold;
}

.apply-btn {
  background-color: #4CAF50;
  color: white;
  border: none;
  padding: 5px 10px;
  border-radius: 5px;
  margin-left: 10px;
  cursor: pointer;
  font-size: 0.8rem;
}

.clear-btn {
  background-color: #f44336;
  color: white;
  border: none;
  padding: 5px 10px;
  border-radius: 5px;
  margin-left: 5px;
  cursor: pointer;
  font-size: 0.8rem;
}

.ingredient-list {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  max-height: 300px;
  overflow-y: auto;
}
</style>
