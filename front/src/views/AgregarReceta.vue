<template>
    <div>
      <h1>Crear Nueva Receta</h1>
      <div class="form-container">
        <form @submit.prevent="submitRecipe">
          <input type="hidden" v-model="recipe.user_id" />
  
          <div>
            <label for="category">Categoría:</label>
            <select v-model="recipe.category_id" required>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
  
          <div>
            <label for="cuisine">Cocina:</label>
            <select v-model="recipe.cuisine_id" required>
              <option v-for="cuisine in cuisines" :key="cuisine.id" :value="cuisine.id">
                {{ cuisine.country }}
              </option>
            </select>
          </div>
  
          <div>
            <label for="title">Título:</label>
            <input type="text" id="title" v-model="recipe.title" required />
          </div>
  
          <div>
            <label for="description">Descripción:</label>
            <textarea id="description" v-model="recipe.description" required></textarea>
          </div>
  
          <div>
            <label>Ingredientes:</label>
            <div v-for="(ingredient, index) in recipe.ingredients" :key="index">
              <input type="text" v-model="recipe.ingredients[index]" placeholder="Añadir ingrediente" />
            </div>
            <button type="button" @click="addIngredient">+</button>
          </div>
  
          <div>
            <label>Pasos:</label>
            <div v-for="(step, index) in recipe.steps" :key="index">
              <input type="text" v-model="recipe.steps[index]" placeholder="Añadir paso" />
            </div>
            <button type="button" @click="addStep">+</button>
          </div>
  
          <div>
            <label>Información Nutricional:</label>
            <div v-for="(nutrient, index) in recipe.nutrition" :key="index">
              <input type="text" v-model="recipe.nutrition[index].key" placeholder="Nombre (ej. calorías)" />
              <input type="text" v-model="recipe.nutrition[index].value" placeholder="Valor (ej. 250)" />
            </div>
            <button type="button" @click="addNutrition">+</button>
          </div>
  
          <div>
            <label for="prepTime">Tiempo de Preparación (minutos):</label>
            <input type="number" id="prepTime" v-model="recipe.prep_time" required />
          </div>
  
          <div>
            <label for="cookTime">Tiempo de Cocción (minutos):</label>
            <input type="number" id="cookTime" v-model="recipe.cook_time" required />
          </div>
  
          <div>
            <label for="servings">Porciones:</label>
            <input type="number" id="servings" v-model="recipe.servings" required />
          </div>
  
          <div>
            <label for="image">Subir Imagen:</label>
            <input type="file" @change="onFileChange" />
            <button type="button" @click="uploadImage">Subir Imagen</button>
            <img v-if="recipe.image" :src="recipe.image" alt="Imagen subida" />
          </div>
  
          <button type="submit">Crear Receta</button>
        </form>
      </div>
  
      <div v-if="message" :class="messageClass" class="message-container">
        {{ message }}
      </div>
    </div>
  </template>
  
  <script>
  import { ref, onMounted } from 'vue';
  import { useRouter } from 'vue-router'; // Importar el router
  import axios from 'axios';
  import communicationManager from '@/services/communicationManager';
  
  const cloudName = 'dt5vjbgab'; 
  const uploadPreset = 'ejemplo1';
  
  export default {
    setup() {
      const router = useRouter(); // Obtener acceso al router
      const user = ref(null);
      const categories = ref([]);
      const cuisines = ref([]);
      const recipe = ref({
        title: '',
        description: '',
        ingredients: [],
        steps: [],
        nutrition: [],
        prep_time: 0,
        cook_time: 0,
        servings: 0,
        category_id: null,
        cuisine_id: null,
        image: null
      });
  
      const selectedFile = ref(null);
      const message = ref('');
      const messageClass = ref('');
  
      // Cargar categorías, cocinas y usuario al montar el componente
      onMounted(async () => {
        try {
          user.value = await communicationManager.getUser();
          categories.value = await communicationManager.fetchCategories();
          cuisines.value = await communicationManager.fetchCuisines();
        } catch (error) {
          console.error("Error obteniendo datos:", error);
        }
      });
  
      // Métodos para agregar ingredientes, pasos e información nutricional
      const addIngredient = () => recipe.value.ingredients.push('');
      const addStep = () => recipe.value.steps.push('');
      const addNutrition = () => recipe.value.nutrition.push({ key: '', value: '' });
  
      // Función para manejar el cambio de archivo (imagen)
      const onFileChange = (e) => {
        selectedFile.value = e.target.files[0];
      };
  
      // Subir la imagen a Cloudinary
      const uploadImage = async () => {
        if (!selectedFile.value) return;
  
        const formData = new FormData();
        formData.append('file', selectedFile.value);
        formData.append('upload_preset', uploadPreset);
  
        try {
          const response = await axios.post(
            `https://api.cloudinary.com/v1_1/${cloudName}/image/upload`,
            formData
          );
          recipe.value.image = response.data.secure_url;
          console.log("Imagen subida correctamente", response.data.secure_url);
        } catch (error) {
          console.error('Error al subir la imagen:', error);
        }
      };
  
      // Función para crear la receta y redirigir a la landingPage
      const submitRecipe = async () => {
        if (!user.value) {
          console.error("Usuario no autenticado");
          return;
        }
  
        try {
          // Crear receta en la base de datos
          await communicationManager.createRecipe({
            ...recipe.value,
            user_id: user.value.id
          });
          
          // Mostrar mensaje de éxito
          message.value = "Receta creada con éxito!";
          messageClass.value = 'success';
          console.log("Receta creada con éxito");
  
          // Redirigir a la landingPage después de crear la receta
          router.push({ name: 'LandingPage' }); // Redirige a la página principal
  
        } catch (error) {
          console.error("Error creando receta:", error);
          // Mostrar mensaje de error
          message.value = "Error creando receta!";
          messageClass.value = 'error';
        }
      };
  
      return {
        recipe,
        categories,
        cuisines,
        addIngredient,
        addStep,
        addNutrition,
        onFileChange,
        uploadImage,
        submitRecipe,
        message,
        messageClass
      };
    }
  };
  </script>
  
  <style scoped>
  .success {
    color: green;
  }
  .error {
    color: red;
  }
  .form-container {
    max-height: 80vh;
    overflow-y: auto;
  }
  .message-container {
    margin-top: 20px;
    font-weight: bold;
  }
  </style>
  