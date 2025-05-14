<template>
  <div class="min-h-screen bg-lime-50 flex flex-col">
    <!-- Hero Section with animated background -->
    <section class="relative overflow-hidden">
      <div class="bg-gradient-to-br from-lime-100 via-lime-200 to-green-200 py-8 sm:py-16 relative">
        <div class="absolute inset-0 bg-white/30 backdrop-blur-sm"></div>
        <!-- Animated circles decoration -->
        <div class="absolute inset-0 overflow-hidden">
          <div class="absolute -left-10 -top-10 w-20 h-20 sm:w-40 sm:h-40 bg-yellow-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
          <div class="absolute -right-10 -top-10 w-20 h-20 sm:w-40 sm:h-40 bg-lime-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
          <div class="absolute -bottom-10 left-20 w-20 h-20 sm:w-40 sm:h-40 bg-green-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 relative z-10">
          <div class="text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl tracking-tight font-extrabold text-lime-900">
              <span class="block bg-gradient-to-r from-lime-900 via-lime-700 to-green-800 bg-clip-text text-transparent">
                Crear Nova Recepta
              </span>
              <span class="block text-xl sm:text-2xl mt-2 sm:mt-3 text-lime-700 font-medium">
                Comparteix la teva passió per la cuina
              </span>
            </h1>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Form Section -->
    <div v-if="authStore.isAuthenticated" class="max-w-4xl mx-auto px-4 sm:px-6 -mt-4 sm:-mt-8 relative z-20 pb-32 sm:pb-40">
      <div class="bg-white rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-8 transform hover:scale-[1.01] transition-transform duration-300">
        <form @submit.prevent="submitRecipe" class="space-y-4 sm:space-y-6">
          <input type="hidden" v-model="recipe.user_id" />

          <!-- Category and Cuisine -->
          <div class="grid grid-cols-1 gap-4 sm:gap-6">
            <div>
              <label for="category" class="block text-sm font-medium text-lime-900 mb-1">Categoria:</label>
              <div class="relative">
                <select v-model="recipe.category_id" required 
                  class="w-full rounded-lg sm:rounded-xl border-lime-300 shadow-sm focus:ring-2 focus:ring-lime-400 focus:border-lime-400 bg-white/80 backdrop-blur-sm outline-none appearance-none pl-3 pr-8 py-2 sm:py-2.5 cursor-pointer hover:bg-lime-50 transition-colors duration-200 text-sm sm:text-base">
                  <option value="" disabled selected class="text-lime-500">Selecciona una categoria</option>
                  <option v-for="category in categories" :key="category.id" :value="category.id"
                    class="py-2 px-4 hover:bg-lime-100 cursor-pointer">
                    {{ category.name }}
                  </option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                  <svg class="w-4 h-4 sm:w-5 sm:h-5 text-lime-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </div>
              </div>
            </div>
            <div>
              <label for="cuisine" class="block text-sm font-medium text-lime-900 mb-1">Cuina:</label>
              <div class="relative">
                <select v-model="recipe.cuisine_id" required 
                  class="w-full rounded-lg sm:rounded-xl border-lime-300 shadow-sm focus:ring-2 focus:ring-lime-400 focus:border-lime-400 bg-white/80 backdrop-blur-sm outline-none appearance-none pl-3 pr-8 py-2 sm:py-2.5 cursor-pointer hover:bg-lime-50 transition-colors duration-200 text-sm sm:text-base">
                  <option value="" disabled selected class="text-lime-500">Selecciona una cuina</option>
                  <option v-for="cuisine in cuisines" :key="cuisine.id" :value="cuisine.id"
                    class="py-2 px-4 hover:bg-lime-100 cursor-pointer">
                    {{ cuisine.country }}
                  </option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                  <svg class="w-4 h-4 sm:w-5 sm:h-5 text-lime-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </div>
              </div>
            </div>
          </div>

          <!-- Title -->
          <div>
            <label for="title" class="block text-sm font-medium text-lime-900 mb-1">Títol:</label>
            <input type="text" id="title" v-model="recipe.title" required 
              class="w-full rounded-lg sm:rounded-xl border-lime-300 shadow-sm focus:ring-2 focus:ring-lime-400 focus:border-lime-400 bg-white/80 backdrop-blur-sm outline-none text-sm sm:text-base py-2 sm:py-2.5 px-3" />
          </div>

          <!-- Servings -->
          <div>
            <label for="servings" class="block text-sm font-medium text-lime-900 mb-1">Racions:</label>
            <input type="number" id="servings" v-model="recipe.servings" required min="1"
              class="w-full rounded-lg sm:rounded-xl border-lime-300 shadow-sm focus:ring-2 focus:ring-lime-400 focus:border-lime-400 bg-white/80 backdrop-blur-sm outline-none text-sm sm:text-base py-2 sm:py-2.5 px-3" />
          </div>

          <!-- Explanation -->
          <div>
            <label for="promptExplanation" class="block text-sm font-medium text-lime-900 mb-1">
              Explicació de la recepta (què vols cuinar?):
            </label>
            <textarea id="promptExplanation" v-model="recipe.explanation" required
              placeholder="Ex: Vull fer un plat vegà ràpid per sopar..."
              class="w-full rounded-lg sm:rounded-xl border-lime-300 shadow-sm focus:ring-2 focus:ring-lime-400 focus:border-lime-400 bg-white/80 backdrop-blur-sm h-20 sm:h-24 outline-none text-sm sm:text-base py-2 sm:py-2.5 px-3"></textarea>
          </div>

          <!-- Auto-fill Button -->
          <button type="button" @click="autofillRecipe" 
            class="w-full py-2.5 sm:py-3 px-4 bg-gradient-to-r from-lime-400 to-lime-300 text-lime-900 rounded-lg sm:rounded-xl hover:shadow-lg hover:bg-lime-300 hover:scale-105 transition-all duration-300 font-medium text-sm sm:text-base">
            Omplir automàticament
          </button>

          <!-- Description -->
          <div>
            <label for="description" class="block text-sm font-medium text-lime-900 mb-1">Descripció:</label>
            <textarea id="description" v-model="recipe.description"
              class="w-full rounded-lg sm:rounded-xl border-lime-300 shadow-sm focus:ring-2 focus:ring-lime-400 focus:border-lime-400 bg-white/80 backdrop-blur-sm h-24 sm:h-32 outline-none text-sm sm:text-base py-2 sm:py-2.5 px-3"></textarea>
          </div>

          <!-- Steps -->
          <div>
            <label class="block text-sm font-medium text-lime-900 mb-2">Passos:</label>
            <draggable v-model="recipe.steps" handle=".drag-handle" group="steps" item-key="index"
              class="space-y-2 sm:space-y-3">
              <template #item="{ element, index }">
                <div class="flex items-start gap-2 sm:gap-3 bg-lime-50 p-3 sm:p-4 rounded-lg sm:rounded-xl group hover:bg-lime-100 transition-colors duration-300">
                  <span class="drag-handle cursor-move text-lime-400 group-hover:text-lime-600 text-lg">☰</span>
                  <div class="flex-1">
                    <label class="text-sm text-lime-700">Pas {{ index + 1 }}:</label>
                    <textarea v-model="recipe.steps[index]" placeholder="Descriu el pas"
                      class="w-full mt-1 rounded-lg sm:rounded-xl border-lime-300 shadow-sm focus:ring-4 focus:ring-lime-300/50 focus:border-lime-400 bg-white/80 backdrop-blur-sm text-sm sm:text-base py-2 sm:py-2.5 px-3"></textarea>
                  </div>
                  <button type="button" @click="removeStep(index)"
                    class="text-red-500 hover:text-red-700 text-lg">×</button>
                </div>
              </template>
            </draggable>
            <button type="button" @click="addStep"
              class="mt-2 sm:mt-3 w-full py-2 px-4 border-2 border-lime-300 text-lime-700 rounded-lg sm:rounded-xl hover:bg-lime-50 transition-colors duration-300 text-sm sm:text-base">
              + Afegir pas
            </button>
          </div>

          <!-- Ingredients -->
          <div class="mb-8 sm:mb-12">
            <label class="block text-sm font-medium text-lime-900 mb-3 sm:mb-4">Ingredients:</label>
            <div v-for="(ingredient, index) in recipe.ingredients" :key="index"
              class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3 mb-3 sm:mb-4 bg-lime-50 p-3 sm:p-4 rounded-lg sm:rounded-xl group hover:bg-lime-100 transition-colors duration-300">
              <div class="flex flex-col sm:flex-row gap-2 w-full">
                <div class="flex gap-2 w-full sm:w-auto">
                  <input type="text" v-model="ingredient.quantity" placeholder="Quantitat"
                    class="w-20 sm:w-24 rounded-lg sm:rounded-xl border-lime-300 shadow-sm focus:ring-4 focus:ring-lime-300/50 focus:border-lime-400 bg-white/80 backdrop-blur-sm text-sm sm:text-base py-2 sm:py-2.5 px-3" />
                  <select v-model="ingredient.unit"
                    class="w-28 sm:w-32 rounded-lg sm:rounded-xl border-lime-300 shadow-sm focus:ring-4 focus:ring-lime-300/50 focus:border-lime-400 bg-white/80 backdrop-blur-sm text-sm sm:text-base py-2 sm:py-2.5 px-3">
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
                  class="flex-1 rounded-lg sm:rounded-xl border-lime-300 shadow-sm focus:ring-4 focus:ring-lime-300/50 focus:border-lime-400 bg-white/80 backdrop-blur-sm text-sm sm:text-base py-2 sm:py-2.5 px-3" />
              </div>
              <button type="button" @click="removeIngredient(index)"
                class="text-red-500 hover:text-red-700 text-lg self-end sm:self-center">×</button>
            </div>
            <button type="button" @click="addIngredient"
              class="w-full py-2 px-4 border-2 border-lime-300 text-lime-700 rounded-lg sm:rounded-xl hover:bg-lime-50 transition-colors duration-300 text-sm sm:text-base">
              + Afegir ingredient
            </button>
          </div>

          <!-- Nutritional Information -->
          <div>
            <label class="block text-sm font-medium text-lime-900 mb-2">
              Informació Nutricional (per ració):
            </label>
            <div v-if="isUpdatingNutrition" class="text-center py-4 bg-lime-50 rounded-lg sm:rounded-xl">
              <p class="text-lime-700">🔄 Calculant valors nutricionals...</p>
            </div>
            <div v-else class="grid grid-cols-2 gap-3 sm:gap-4">
              <div>
                <label for="calories" class="block text-sm text-lime-700">Calories:</label>
                <input type="number" id="calories" v-model="recipe.calories" min="0"
                  class="w-full rounded-lg sm:rounded-xl border-lime-300 shadow-sm focus:ring-2 focus:ring-lime-400 focus:border-lime-400 bg-white/80 backdrop-blur-sm outline-none text-sm sm:text-base py-2 sm:py-2.5 px-3" />
              </div>
              <div>
                <label for="protein" class="block text-sm text-lime-700">Proteïnes (g):</label>
                <input type="number" id="protein" v-model="recipe.protein" min="0"
                  class="w-full rounded-lg sm:rounded-xl border-lime-300 shadow-sm focus:ring-2 focus:ring-lime-400 focus:border-lime-400 bg-white/80 backdrop-blur-sm outline-none text-sm sm:text-base py-2 sm:py-2.5 px-3" />
              </div>
              <div>
                <label for="fats" class="block text-sm text-lime-700">Greixos (g):</label>
                <input type="number" id="fats" v-model="recipe.fats" min="0"
                  class="w-full rounded-lg sm:rounded-xl border-lime-300 shadow-sm focus:ring-2 focus:ring-lime-400 focus:border-lime-400 bg-white/80 backdrop-blur-sm outline-none text-sm sm:text-base py-2 sm:py-2.5 px-3" />
              </div>
              <div>
                <label for="carbs" class="block text-sm text-lime-700">Carbohidrats (g):</label>
                <input type="number" id="carbs" v-model="recipe.carbs" min="0"
                  class="w-full rounded-lg sm:rounded-xl border-lime-300 shadow-sm focus:ring-2 focus:ring-lime-400 focus:border-lime-400 bg-white/80 backdrop-blur-sm outline-none text-sm sm:text-base py-2 sm:py-2.5 px-3" />
              </div>
            </div>
          </div>

          <!-- Time Information -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
            <div>
              <label for="prepTime" class="block text-sm font-medium text-lime-900 mb-1">
                Temps de Preparació (minuts):
              </label>
              <input type="number" id="prepTime" v-model="recipe.prep_time" required min="0"
                class="w-full rounded-lg sm:rounded-xl border-lime-300 shadow-sm focus:ring-2 focus:ring-lime-400 focus:border-lime-400 bg-white/80 backdrop-blur-sm outline-none text-sm sm:text-base py-2 sm:py-2.5 px-3" />
            </div>
            <div>
              <label for="cookTime" class="block text-sm font-medium text-lime-900 mb-1">
                Temps de Cocció (minuts):
              </label>
              <input type="number" id="cookTime" v-model="recipe.cook_time" required min="0"
                class="w-full rounded-lg sm:rounded-xl border-lime-300 shadow-sm focus:ring-2 focus:ring-lime-400 focus:border-lime-400 bg-white/80 backdrop-blur-sm outline-none text-sm sm:text-base py-2 sm:py-2.5 px-3" />
            </div>
          </div>

          <!-- Media Upload -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
            <!-- Image Upload -->
            <div>
              <label class="block text-sm font-medium text-lime-900 mb-2">Pujar Imatge:</label>
              <div :class="[
                'border-2 border-dashed rounded-lg sm:rounded-xl p-4 sm:p-6 text-center cursor-pointer transition-colors duration-300',
                messageClass === 'error' && !recipe.image ? 'border-red-500 bg-red-50' : 'border-lime-300 hover:border-lime-400 hover:bg-lime-50'
              ]" @click="$refs.imageInput.click()">
                <input type="file" ref="imageInput" @change="onImageChange" accept="image/*" class="hidden" />
                <p class="text-sm sm:text-base text-lime-700">Arrossega i deixa anar una imatge o fes clic per seleccionar-la.</p>
                <img v-if="recipe.image" :src="recipe.image" alt="Imatge pujada"
                  class="mt-3 sm:mt-4 max-h-32 sm:max-h-48 mx-auto rounded-lg sm:rounded-xl shadow-md" />
              </div>
            </div>

            <!-- Video Upload -->
            <div>
              <label class="block text-sm font-medium text-lime-900 mb-2">Pujar Vídeo:</label>
              <div :class="[
                'border-2 border-dashed rounded-lg sm:rounded-xl p-4 sm:p-6 text-center cursor-pointer transition-colors duration-300',
                messageClass === 'error' && !recipe.video ? 'border-red-500 bg-red-50' : 'border-lime-300 hover:border-lime-400 hover:bg-lime-50'
              ]" @click="$refs.videoInput.click()">
                <input type="file" ref="videoInput" @change="onVideoChange" accept="video/*" class="hidden" />
                <p class="text-sm sm:text-base text-lime-700">Arrossega i deixa anar un vídeo o fes clic per seleccionar-lo.</p>
                <video v-if="recipe.video" controls class="mt-3 sm:mt-4 max-h-32 sm:max-h-48 mx-auto rounded-lg sm:rounded-xl shadow-md">
                  <source :src="recipe.video" type="video/mp4">
                  El teu navegador no suporta l'element de vídeo.
                </video>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <button type="submit"
            class="w-full py-3 sm:py-4 px-4 sm:px-6 bg-gradient-to-r from-lime-500 via-lime-400 to-lime-300 text-lime-900 rounded-lg sm:rounded-xl hover:shadow-lg hover:bg-lime-400 hover:scale-105 transition-all duration-300 font-medium text-base sm:text-lg mb-20 sm:mb-24">
            Crear Recepta
          </button>
        </form>
      </div>
    </div>

    <!-- Login Required Message -->
    <div v-else class="max-w-md mx-auto mt-10 sm:mt-20 px-4 sm:px-6">
      <div class="bg-white rounded-xl sm:rounded-2xl shadow-xl p-6 sm:p-8 text-center">
        <p class="text-base sm:text-lg text-lime-700 mb-4 sm:mb-6">Per crear una recepta, has d'iniciar sessió</p>
        <button @click="goToLogin"
          class="w-full py-2.5 sm:py-3 px-4 sm:px-6 bg-gradient-to-r from-lime-500 via-lime-400 to-lime-300 text-lime-900 rounded-lg sm:rounded-xl hover:shadow-lg hover:bg-lime-400 hover:scale-105 transition-all duration-300 font-medium text-sm sm:text-base">
          Iniciar Sessió
        </button>
      </div>
    </div>

    <!-- Error/Success Popup -->
    <div v-if="message" class="fixed top-1/4 left-1/2 transform -translate-x-1/2 z-50 w-[90%] sm:w-auto max-w-md">
      <div :class="[
        'rounded-lg sm:rounded-xl p-3 sm:p-4 shadow-lg',
        messageClass === 'error' ? 'bg-red-500' : 'bg-green-500',
        'text-white'
      ]">
        <p class="text-sm sm:text-base">{{ message }}</p>
        <button @click="closePopup" class="mt-2 px-3 sm:px-4 py-1.5 sm:py-2 bg-white text-gray-800 rounded-lg hover:bg-gray-100 text-sm sm:text-base">
          Cerrar
        </button>
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
import draggable from 'vuedraggable';

const groq = new Groq({
  apiKey: import.meta.env.VITE_GROQ_API_KEY,
  dangerouslyAllowBrowser: true,
});

const cloudName = "dt5vjbgab";
const uploadPreset = "ejemplo1";

export default {
  components: {
    draggable
  },
  setup() {
    const router = useRouter();
    const authStore = useAuthStore();
    const user = ref(null);
    const categories = ref([]);
    const cuisines = ref([]);
    const recipe = ref({
      title: "",
      explanation: "",
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
        await new Promise(resolve => setTimeout(resolve, 3000));

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
- Explicació del que vol fer l'usuari: ${recipe.value.explanation}

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
  {"quantity": "quantitat exacta", "unit": "unitat (obligatori)", "name": "Nom ingredient"},
    ...
],
"steps": [
    "Descripció detallada de pas 1",
    "Descripció detallada de pas 2",
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

<style>
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

/* Estilos personalizados para los selectores */
select {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
}

select::-ms-expand {
  display: none;
}

/* Estilos para las opciones del select */
select option {
  padding: 12px;
  background-color: white;
}

/* Estilos para el hover de las opciones */
select option:hover {
  background-color: #f0fdf4;
}

/* Estilos para el focus del select */
select:focus {
  box-shadow: 0 0 0 2px rgba(132, 204, 22, 0.2);
}

/* Media queries para pantallas pequeñas */
@media (max-width: 640px) {
  select {
    font-size: 16px; /* Previene el zoom en iOS */
    padding: 12px;
  }
  
  .relative {
    margin-bottom: 1rem;
  }

  /* Ajustes adicionales para móviles */
  .recipe-form {
    padding: 1rem;
  }

  .form-group {
    margin-bottom: 1rem;
  }

  .button-group {
    flex-direction: column;
    gap: 0.5rem;
  }

  .button-group button {
    width: 100%;
  }

  /* Ajustes para inputs en móviles */
  input[type="text"],
  input[type="number"],
  textarea {
    font-size: 16px; /* Previene el zoom en iOS */
  }

  /* Ajustes para el contenedor de ingredientes */
  .ingredient-container {
    flex-direction: column;
  }

  .ingredient-container > div {
    width: 100%;
  }

  /* Ajustes para el contenedor de pasos */
  .step-container {
    padding: 0.75rem;
  }

  /* Ajustes para los botones de acción */
  .action-button {
    padding: 0.5rem;
    font-size: 0.875rem;
  }
}
</style>