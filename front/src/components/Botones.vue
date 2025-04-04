<template>
  <div class="filter-buttons">
    <!-- Botones principales en fila -->
    <div class="button-group">
      <button @click="toggleSubButtons('categoria')" class="button-main">Categoria</button>
      <button @click="toggleSubButtons('cuisine')" class="button-main">Cuina</button>
      <button @click="toggleSubButtons('tiempo')" class="button-main">Temps</button>
      <button @click="toggleSubButtons('ingredientes')" class="button-main">Ingredientes</button>
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
      <button 
        v-for="ingredient in ingredients" 
        :key="ingredient"
        class="button-secondary"
        @click="filtrarPorIngrediente(ingredient)"
      >
        {{ ingredient }}
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
  setup(_, { emit }) {
    // Variables reactivas para almacenar los datos
    const categorias = ref([]);      // Categorías
    const cuisines = ref([]);        // Cocinas (países)
    const times = ref([]);           // Tiempos
    const ingredients = ref([]);     // Ingredientes
    const recetas = ref([]);         // Recetas filtradas
    const activeButton = ref('');    // Botón principal activo

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
        // Asumimos que la respuesta tiene la estructura { ingredients: [...] }
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

    // Función para filtrar por ingrediente (envía un array con un solo ingrediente)
    const filtrarPorIngrediente = async (ingredient) => {
      try {
        const response = await communicationManager.fetchRecipesByIngredients([ingredient]);
        recetas.value = response.recipes;
        emit('filtradoPorIngrediente', true);
      } catch (error) {
        console.error('Error al filtrar recetas por ingrediente:', error);
      }
    };

    // Alterna la visualización de los subbotones según el botón principal
    const toggleSubButtons = (buttonName) => {
      activeButton.value = activeButton.value === buttonName ? '' : buttonName;
    };

    return {
      categorias,
      cuisines,
      times,
      ingredients,
      recetas,
      activeButton,
      obtenerCategorias,
      obtenerCuisines,
      obtenerTimes,
      obtenerIngredients,
      filtrarPorCategoria,
      filtrarPorCuisine,
      filtrarPorTiempo,
      filtrarPorIngrediente,
      toggleSubButtons,
    };
  },
};
</script>


<style scoped>
/* Contenedor para los botones principales alineados en fila */
.button-group {
  display: flex;
  gap: 10px; /* Espacio entre botones */
  justify-content: center;
  margin-bottom: 20px; /* Separación debajo de los botones */
}

/* Botones principales (azul más oscuro, más pequeños, en fila) */
.button-main {
  font-size: 0.85rem;  /* Más pequeños */
  padding: 8px 12px;   /* Menos espacio */
  background-color: #0c0636; /* Azul más oscuro */
  border: none;
  color: white;
  cursor: pointer;
  border-radius: 5px;  /* Menos redondeado */
  display: inline-block; /* Alineados en fila */
}

.button-main:hover {
  background-color: #322b5f; /* Azul aún más oscuro */
}

/* Botones secundarios (verde suave) */
.button-secondary {
  font-size: 0.85rem;
  padding: 8px 12px;
  background-color: #02067d; /* Verde suave */
  border: none;
  color: white;
  cursor: pointer;
  border-radius: 5px;
  margin: 5px 5px;
}

.button-secondary:hover {
  background-color: #4545a0; /* Verde más oscuro */
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
</style>
