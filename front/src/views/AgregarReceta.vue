<template>
  <div class="page-container">
    <div v-if="message" class="popup-error" :class="messageClass">
      <p>{{ message }}</p>
      <button @click="closePopup">Cerrar</button>
    </div>

    <!-- Mostrar contenido solo si está autenticado -->
    <div v-if="authStore.isAuthenticated">
      <div class="form-card">
        <h1>Crear Nova Recepta</h1>
        <div class="form-container">
          <form @submit.prevent="submitRecipe">
            <input type="hidden" v-model="recipe.user_id" />

            <div class="form-group form-row">
              <div class="half-width">
                <label for="category">Categoria:</label>
                <select v-model="recipe.category_id" required class="full-width-input">
                  <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                  </option>
                </select>
              </div>
              <div class="half-width">
                <label for="cuisine">Cuina:</label>
                <select v-model="recipe.cuisine_id" required class="full-width-input">
                  <option v-for="cuisine in cuisines" :key="cuisine.id" :value="cuisine.id">
                    {{ cuisine.country }}
                  </option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label for="title">Títol:</label>
              <input type="text" id="title" v-model="recipe.title" required class="full-width-input" />
            </div>

            <div class="form-group">
              <label for="servings">Racions:</label>
              <input type="number" id="servings" v-model="recipe.servings" required class="full-width-input" min="1" />
            </div>

            <button type="button" @click="autofillRecipe" class="auto-fill-button">
              Omplir automàticament
            </button>

            <div class="form-group">
              <label for="description">Descripció:</label>
              <textarea id="description" v-model="recipe.description" readonly class="full-width-input"></textarea>
            </div>

            <div class="form-group">
              <label>Ingredients:</label>
              <div v-for="(ingredient, index) in recipe.ingredients" :key="index" class="ingredient-row">
                <div class="quantity-input">
                  <input type="text" v-model="ingredient.quantity" placeholder="Quantitat" />
                  <select v-model="ingredient.unit">
                    <option value="">Sense unitat</option>
                    <option value="g">g</option>
                    <option value="kg">kg</option>
                    <option value="ml">ml</option>
                    <option value="l">l</option>
                    <option value="cullerada">cullerada</option>
                    <option value="culleradeta">culleradeta</option>
                    <option value="tassa">tassa</option>
                    <option value="pessics">pessics</option>
                    <option value="unitats">unitats</option>
                  </select>
                </div>
                <input type="text" v-model="ingredient.name" placeholder="Nom de l'ingredient"
                  class="ingredient-name" />
                <button type="button" @click="removeIngredient(index)" class="remove-button">×</button>
              </div>
              <button type="button" @click="addIngredient" class="add-button">+ Afegir ingredient</button>
            </div>

            <div class="form-group">
              <label>Passos:</label>
              <div v-for="(step, index) in recipe.steps" :key="index" class="step-row">
                <textarea v-model="recipe.steps[index]" placeholder="Descriu el pas" class="step-textarea"></textarea>
                <button type="button" @click="removeStep(index)" class="remove-button">×</button>
              </div>
              <button type="button" @click="addStep" class="add-button">+ Afegir pas</button>
            </div>

            <div class="form-group">
    <label>Informació Nutricional (per ració):</label>
    <div v-if="isUpdatingNutrition" class="nutrition-loading">
      <p>Calculant valors nutricionals...</p>
    </div>
    <div class="nutrition-grid">
      <div class="nutrition-item">
        <label for="calories">Calories:</label>
        <input type="number" id="calories" v-model="recipe.calories" min="0" />
      </div>
      <div class="nutrition-item">
        <label for="protein">Proteïnes (g):</label>
        <input type="number" id="protein" v-model="recipe.protein" min="0" />
      </div>
      <div class="nutrition-item">
        <label for="fats">Greixos (g):</label>
        <input type="number" id="fats" v-model="recipe.fats" min="0" />
      </div>
      <div class="nutrition-item">
        <label for="carbs">Carbohidrats (g):</label>
        <input type="number" id="carbs" v-model="recipe.carbs" min="0" />
      </div>
    </div>
  </div>

            <div class="form-group form-row">
              <div class="third-width">
                <label for="prepTime">Temps de Preparació (minuts):</label>
                <input type="number" id="prepTime" v-model="recipe.prep_time" required min="0"
                  class="full-width-input" />
              </div>
              <div class="third-width">
                <label for="cookTime">Temps de Cocció (minuts):</label>
                <input type="number" id="cookTime" v-model="recipe.cook_time" required min="0"
                  class="full-width-input" />
              </div>
            </div>

            <div class="form-group">
              <div class="upload-container">
                <div class="upload-image-container">
                  <label for="image" class="upload-label">Pujar Imatge:</label>
                  <div class="upload-area" :class="{ 'error-border': messageClass === 'error' && !recipe.image }">
                    <input type="file" id="image" @change="onImageChange" accept="image/*" />
                    <p class="upload-instructions">Arrossega i deixa anar una imatge o fes clic per seleccionar-la.</p>
                  </div>
                  <img v-if="recipe.image" :src="recipe.image" alt="Imatge pujada" class="uploaded-preview" />
                </div>

                <div class="upload-video-container">
                  <label for="video" class="upload-label">Pujar Vídeo:</label>
                  <div class="upload-area" :class="{ 'error-border': messageClass === 'error' && !recipe.video }">
                    <input type="file" id="video" @change="onVideoChange" accept="video/*" />
                    <p class="upload-instructions">Arrossega i deixa anar un vídeo o fes clic per seleccionar-lo.</p>
                  </div>
                  <video v-if="recipe.video" controls class="uploaded-preview">
                    <source :src="recipe.video" type="video/mp4">
                    El teu navegador no suporta l'element de vídeo.
                  </video>
                </div>
              </div>
            </div>

            <div v-if="message" :class="messageClass" class="message-container">
              {{ message }}
            </div>
            <button type="submit" class="submit-button">Crear Recepta</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Mostrar mensaje de login requerido si no está autenticado -->
    <div v-else class="auth-required-container">
      <div class="auth-required-message">
        <p>Per crear una recepta, has d'iniciar sessió</p>
        <button @click="goToLogin" class="login-button">Iniciar Sessió</button>
      </div>
    </div>
  </div>
</template>
<script>
import { ref, onMounted, watch } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import communicationManager from "@/services/communicationManager";
import Groq from "groq-sdk";
import { useAuthStore } from '@/stores/authStore';
import { validateFoodImage, validateFoodVideo } from '@/services/geminiService';
import { debounce } from 'lodash-es';

const groq = new Groq({
  apiKey: import.meta.env.VITE_GROQ_API_KEY,
  dangerouslyAllowBrowser: true,
});

const cloudName = "dt5vjbgab";
const uploadPreset = "ejemplo1";

export default {
  setup() {
    const router = useRouter();
    const authStore = useAuthStore();
    const user = ref(null);
    const categories = ref([]);
    const cuisines = ref([]);
    const recipe = ref({
      title: "",
      description: "",
      ingredients: [],
      steps: [],
      calories: 0,
      protein: 0,
      fats: 0,
      carbs: 0,
      prep_time: 0,
      cook_time: 0,
      servings: 1,
      category_id: null,
      cuisine_id: null,
      image: null,
      video: null,
    });
    const selectedFile = ref(null);
    const message = ref("");
    const messageClass = ref("");
    const bannedWords = ['polla', 'cul', 'penis', 'fuck', 'mierda', 'shit', 'puta', 'caca', 'porno', 'sex', 'nazi'];
    const isUpdatingNutrition = ref(false);
    const lastNutritionUpdate = ref(null);

    const isInputValid = (text) => {
      const lowerText = text.toLowerCase();
      return !bannedWords.some(word => lowerText.includes(word));
    };

    const showErrorPopup = (msg) => {
      message.value = msg;
      messageClass.value = "error";
    };

    const showSuccessPopup = (msg) => {
      message.value = msg;
      messageClass.value = "success";
    };

    const closePopup = () => {
      message.value = "";
      messageClass.value = "";
    };

    const goToLogin = () => {
      router.push({
        name: 'login',
        query: { redirect: router.currentRoute.value.fullPath }
      });
    };

    // Función para actualizar la nutrición con IA
    const updateNutritionWithAI = debounce(async () => {
      if (recipe.value.ingredients.length === 0) {
        resetNutrition();
        return;
      }

      // Evitar llamadas duplicadas con los mismos ingredientes
      const currentIngredients = JSON.stringify(recipe.value.ingredients);
      if (lastNutritionUpdate.value === currentIngredients) return;
      
      lastNutritionUpdate.value = currentIngredients;
      isUpdatingNutrition.value = true;

      try {
        const servings = recipe.value.servings || 1;
        const ingredientsText = recipe.value.ingredients
          .map(ing => `${ing.quantity || '1'} ${ing.unit || ''} ${ing.name}`.trim())
          .join('\n');

        const response = await groq.chat.completions.create({
          model: "llama-3.3-70b-versatile",
          messages: [{
            role: "user",
            content: `Calcula los valores nutricionales aproximados para esta receta por ración (${servings} raciones en total). 
            Solo devuelve un JSON válido con los campos: calories, protein, fats, carbs. 
            No incluyas ningún otro texto o explicación.
            
            Ingredientes:
            ${ingredientsText}
            
            Formato requerido:
            {
              "calories": 0,
              "protein": 0,
              "fats": 0,
              "carbs": 0
            }`
          }],
          response_format: { type: "json_object" },
          temperature: 0.2,
        });

        const nutritionData = JSON.parse(response.choices[0].message.content);
        
        if (nutritionData.calories !== undefined) {
          recipe.value.calories = Math.round(nutritionData.calories);
          recipe.value.protein = Math.round(nutritionData.protein * 10) / 10;
          recipe.value.fats = Math.round(nutritionData.fats * 10) / 10;
          recipe.value.carbs = Math.round(nutritionData.carbs * 10) / 10;
        }
      } catch (error) {
        console.error("Error calculando nutrición:", error);
        showErrorPopup("Error al calcular los valores nutricionales. Por favor verifica los ingredientes.");
      } finally {
        isUpdatingNutrition.value = false;
      }
    }, 1500); // Debounce de 1.5 segundos

    const resetNutrition = () => {
      recipe.value.calories = 0;
      recipe.value.protein = 0;
      recipe.value.fats = 0;
      recipe.value.carbs = 0;
    };

    // Watcher para ingredientes y raciones
    watch(() => [...recipe.value.ingredients, recipe.value.servings], () => {
      updateNutritionWithAI();
    }, { deep: true });

    onMounted(async () => {
      try {
        if (authStore.isAuthenticated) {
          user.value = await communicationManager.getUser();
          categories.value = await communicationManager.fetchCategories();
          cuisines.value = await communicationManager.fetchCuisines();
        }
      } catch (error) {
        console.error("Error fetching data:", error);
      }
    });

    const autofillRecipe = async () => {
      if (!isInputValid(recipe.value.title)) {
        showErrorPopup("El títol conté paraules inapropiades. Si us plau, revisa-ho.");
        return;
      }

      if (!recipe.value.category_id || !recipe.value.cuisine_id) {
        showErrorPopup("Selecciona una categoria i una cuina primer.");
        messageClass.value = "error";
        return;
      }

      try {
        const servings = recipe.value.servings || 4;
        const categoryName = categories.value.find((c) => c.id === recipe.value.category_id).name;
        const cuisineName = cuisines.value.find((c) => c.id === recipe.value.cuisine_id).country;

        const userMessage = ` 
ESTRICTE: Només pots generar receptes de cuina. Rebutja absolutament qualsevol altre tipus de sol·licitud.

Genera UNA RECEPTA DE CUINA en format JSON basada en aquestes dades:
- Categoria: ${categoryName}
- Cuina: ${cuisineName}
- Racions: ${servings}
- Títol: ${recipe.value.title || "[crea un títol atractiu]"}

La recepta ha de ser EXCLUSIVAMENT sobre cuina i ha d'incloure:
1. Descripció breu (màxim 2 frases)
2. Llista d'ingredients amb quantitats exactes
3. Passos de preparació numerats
4. Informació nutricional per ració
5. Temps de preparació i cocció

FORMAT REQUERIT (en català, només JSON):

{
"title": "Títol de la recepta",
"description": "Descripció breu",
"ingredients": [
    {"quantity": "quantitat exacta", "unit": "unitat", "name": "Nom ingredient"},
    ...
],
"steps": [
    "Pas 1: Descripció detallada",
    "Pas 2: Descripció detallada",
    ...
],
"nutrition": {
    "calories": 0,
    "protein": 0,
    "fats": 0,
    "carbs": 0
},
"times": {
    "prep_time": 0,
    "cook_time": 0
}
}

NORMES ESTRICTES:
- ABSOLUTAMENT RES que no sigui sobre cuina/receptes
- No inclour cap tipus de contingut inadequat
- Quantitats precises i reals
- Passos clars i executables
- Informació nutricional realista
- Resposta EXCLUSIVAMENT en format JSON vàlid`;

        const response = await groq.chat.completions.create({
          model: "llama-3.3-70b-versatile",
          messages: [{
            role: "user",
            content: userMessage
          }],
          response_format: { type: "json_object" },
          temperature: 0.5,
        });

        const aiData = response.choices[0].message.content;
        const parsedData = JSON.parse(aiData);

        // Validar que la respuesta sea sobre comida
        if (!parsedData.ingredients || !parsedData.steps) {
          throw new Error("La resposta no és una recepta vàlida");
        }

        // Asignar los valores a la receta
        recipe.value.title = parsedData.title || "Recepta sense títol";
        recipe.value.description = parsedData.description || "Sense descripció.";
        recipe.value.ingredients = parsedData.ingredients || [];
        recipe.value.steps = parsedData.steps || [];
        recipe.value.prep_time = parsedData.times?.prep_time || 0;
        recipe.value.cook_time = parsedData.times?.cook_time || 0;
        recipe.value.calories = parsedData.nutrition?.calories || 0;
        recipe.value.carbs = parsedData.nutrition?.carbs || 0;
        recipe.value.fats = parsedData.nutrition?.fats || 0;
        recipe.value.protein = parsedData.nutrition?.protein || 0;

        message.value = "Recepta generada automàticament.";
        messageClass.value = "success";
      } catch (error) {
        console.error("Error obtenint dades de la IA:", error);
        showErrorPopup("Error: Només es poden generar receptes de cuina. Si us plau, introdueix dades vàlides.");
      }
    };

    const addIngredient = () => {
      recipe.value.ingredients.push({
        quantity: "",
        unit: "",
        name: ""
      });
    };

    const removeIngredient = (index) => {
      recipe.value.ingredients.splice(index, 1);
    };

    const addStep = () => recipe.value.steps.push("");

    const removeStep = (index) => {
      recipe.value.steps.splice(index, 1);
    };

    const onImageChange = async (e) => {
      const file = e.target.files[0];
      if (!file) return;

      // Validar que sea una imagen de comida
      message.value = "Validant la imatge...";
      messageClass.value = "info";

      try {
        const validation = await validateFoodImage(file);

        if (!validation.isFood) {
          message.value = `La imatge no és vàlida: ${validation.reason}`;
          messageClass.value = "error";
          e.target.value = "";
          return;
        }

        // Si es válida, proceder con la subida
        const formData = new FormData();
        formData.append("file", file);
        formData.append("upload_preset", uploadPreset);

        const response = await axios.post(
          `https://api.cloudinary.com/v1_1/${cloudName}/image/upload`,
          formData
        );
        recipe.value.image = response.data.secure_url;
        message.value = "Imatge pujada correctament!";
        messageClass.value = "success";
      } catch (error) {
        console.error("Error al validar o subir la imagen:", error);
        message.value = "Error al processar la imatge. Si us plau, prova amb una altra.";
        messageClass.value = "error";
      }
    };

    const onVideoChange = async (e) => {
      const file = e.target.files[0];
      if (!file) return;

      message.value = "Validant el vídeo...";
      messageClass.value = "info";

      try {
        const validation = await validateFoodVideo(file);

        if (!validation.isFood) {
          message.value = `El vídeo no és vàlid: ${validation.reason}`;
          messageClass.value = "error";
          e.target.value = "";
          return;
        }

        const formData = new FormData();
        formData.append("file", file);
        formData.append("upload_preset", uploadPreset);

        const response = await axios.post(
          `https://api.cloudinary.com/v1_1/${cloudName}/video/upload`,
          formData
        );
        recipe.value.video = response.data.secure_url;
        message.value = "Vídeo pujat correctament!";
        messageClass.value = "success";
      } catch (error) {
        console.error("Error al validar o pujar el vídeo:", error);
        message.value = "Error al processar el vídeo. Si us plau, prova amb un altre.";
        messageClass.value = "error";
      }
    };
    const submitRecipe = async () => {
  if (!authStore.isAuthenticated || !user.value) {
    console.error("Usuario no autenticado");
    return;
  }

  // Validar que se haya subido una imagen o un vídeo
  if (!recipe.value.image && !recipe.value.video) {
    message.value = "Has de pujar una imatge o un vídeo per a la recepta!";
    messageClass.value = "error";
    return;
  }

  try {
    // Preparar los datos para enviar en el formato correcto
    const recipeData = {
      ...recipe.value,
      user_id: user.value.id,
      ingredients: recipe.value.ingredients.map(ing => ({
        ...ing,
        quantity: ing.quantity || "",
        unit: ing.unit || ""
      })),
      nutrition: {  // Crear objeto nutrition con los valores
        calories: recipe.value.calories || 0,
        protein: recipe.value.protein || 0,
        fats: recipe.value.fats || 0,
        carbs: recipe.value.carbs || 0
      }
    };

    // Eliminar los campos individuales para evitar confusión
    delete recipeData.calories;
    delete recipeData.protein;
    delete recipeData.fats;
    delete recipeData.carbs;

    await communicationManager.createRecipe(recipeData);

    message.value = "¡Receta creada con éxito!";
    messageClass.value = "success";
    console.log("Receta creada correctamente");

    router.push({ name: "LandingPage" });
  } catch (error) {
    console.error("Error al crear la receta:", error);
    message.value = "¡Error al crear la receta!";
    messageClass.value = "error";
  }
};

    return {
      authStore,
      recipe,
      categories,
      cuisines,
      isUpdatingNutrition,
      autofillRecipe,
      addIngredient,
      removeIngredient,
      addStep,
      removeStep,
      onImageChange,
      onVideoChange,
      submitRecipe,
      message,
      messageClass,
      goToLogin,
      showSuccessPopup,
      closePopup
    };
  },
};
</script>
<style scoped>
/* Estilos para el mensaje de login requerido */
.auth-required-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 70vh;
}

.auth-required-message {
  text-align: center;
  background-color: #f8f9fa;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  max-width: 500px;
  width: 100%;
}

.auth-required-message p {
  font-size: 1.2rem;
  margin-bottom: 1.5rem;
  color: #333;
}

.ingredient-row,
.step-row {
  display: flex;
  gap: 10px;
  margin-bottom: 10px;
  align-items: center;
}

.quantity-input {
  display: flex;
  gap: 5px;
  width: 180px;
}

/* Estilo mejorado para mensajes de error */
.message-container.error {
  background-color: #ffebee;
  border-left: 4px solid #f44336;
  padding: 1rem;
  margin: 1rem 0;
  border-radius: 4px;
  animation: shake 0.5s;
}

@keyframes shake {

  0%,
  100% {
    transform: translateX(0);
  }

  20%,
  60% {
    transform: translateX(-5px);
  }

  40%,
  80% {
    transform: translateX(5px);
  }
}

/* Destacar el área de subida cuando hay error */
.upload-area.error-border {
  border-color: #f44336;
  animation: pulse 1.5s infinite;
}

@keyframes pulse {
  0% {
    box-shadow: 0 0 0 0 rgba(244, 67, 54, 0.4);
  }

  70% {
    box-shadow: 0 0 0 10px rgba(244, 67, 54, 0);
  }

  100% {
    box-shadow: 0 0 0 0 rgba(244, 67, 54, 0);
  }
}

.quantity-input input {
  width: 70px;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.quantity-input select {
  width: 90px;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.ingredient-name {
  flex: 1;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.step-textarea {
  flex: 1;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
  min-height: 60px;
  resize: vertical;
}

.remove-button {
  background: #ff4444;
  color: white;
  border: none;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  cursor: pointer;
  font-size: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.3s;
}

.remove-button:hover {
  background: #cc0000;
}

.popup-error {
  position: fixed;
  top: 20%;
  left: 50%;
  transform: translateX(-50%);
  background-color: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 20px;
  border-radius: 10px;
  z-index: 1000;
}

.popup-error.error {
  background-color: red;
}

.popup-error.success {
  background-color: green;
}

.nutrition-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 15px;
  margin-top: 10px;
}

.nutrition-item {
  display: flex;
  flex-direction: column;
}

.nutrition-item input {
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.add-button,
.upload-button,
.auto-fill-button {
  background: transparent;
  color: #0c0636;
  border: 2px solid #0c0636;
  padding: 8px 16px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.add-button:hover,
.upload-button:hover,
.auto-fill-button:hover {
  background: #004080;
  color: #fff;
}

.submit-button {
  background-color: #0c0636;
  color: white;
  padding: 10px;
  border-radius: 10px;
  cursor: pointer;
  font-size: 16px;
  border: none;
  margin-top: 10px;
  transition: background-color 0.3s ease;
  width: 300px;
  margin-bottom: 50px;
}

.submit-button:hover {
  background: #322b5f;
}

.message-container {
  margin-top: 1.5rem;
  text-align: center;
  font-weight: bold;
}

.success {
  color: #10b981;
}

.error {
  color: #322b5f;
}

.upload-container {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
}

.upload-image-container,
.upload-video-container {
  flex: 1;
  min-width: 300px;
  margin-bottom: 1.5rem;
  text-align: center;
}

.upload-label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #333;
}

.upload-area {
  border: 2px dashed #0c0636;
  border-radius: 15px;
  padding: 1.5rem;
  background-color: #f9f9f9;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.upload-area:hover {
  background-color: #e6e6e6;
  border-color: #322b5f;
}

.upload-area input[type="file"] {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  opacity: 0;
  cursor: pointer;
}

.upload-instructions {
  font-size: 0.9rem;
  color: #666;
  margin-top: 0.5rem;
}

.uploaded-preview {
  margin-top: 1rem;
  max-width: 100%;
  border-radius: 10px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  max-height: 200px;
}

* {
  font-family: 'Times New Roman', Times, serif;
}

.page-container {
  text-align: center;
  background-color: #fdfdff;
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
}

.form-card {
  background: #ffffff;
  border-radius: 15px;
  padding: 2rem;
  max-width: 900px;
  width: 100%;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

h1 {
  margin-bottom: 1.5rem;
  font-size: 2rem;
  color: #333;
}

.form-container {
  width: 100%;
}

.form-group {
  margin-bottom: 1.5rem;
  text-align: left;
}

label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #333;
}

input,
select,
textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ccc;
  border-radius: 8px;
  background: #fff;
  font-size: 1rem;
  color: #333;
  margin-bottom: 0.5rem;
}

textarea {
  resize: vertical;
  min-height: 100px;
}

.input-group {
  margin-bottom: 0.5rem;
}

.form-row {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
  margin-bottom: 1.5rem;
}

.half-width {
  flex: 1;
  min-width: 48%;
}

.third-width {
  flex: 1;
  min-width: 30%;
}

.full-width-input {
  width: 100%;
}

.login-button {
  background-color: #4CAF50;
  color: white;
  border: none;
  padding: 0.8rem 1.5rem;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
  transition: background-color 0.3s;
}

.login-button:hover {
  background-color: #45a049;
}

@media (max-width: 768px) {
  .form-row {
    flex-direction: column;
  }

  .half-width,
  .third-width {
    min-width: 100%;
  }

  .submit-button {
    width: 100%;
  }

  .upload-container {
    flex-direction: column;
  }

  .upload-image-container,
  .upload-video-container {
    min-width: 100%;
  }
}
</style>