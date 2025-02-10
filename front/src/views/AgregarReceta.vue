<template>
  <div class="container">
    <div class="glass-form">
      <h1>Crear Nueva Receta</h1>
      <div class="form-container">
        <form @submit.prevent="submitRecipe">
          <input type="hidden" v-model="recipe.user_id" />

          <!-- Category and Cuisine in one row -->
          <div class="form-group form-row">
            <div class="half-width">
              <label for="category">Categoría:</label>
              <select
                v-model="recipe.category_id"
                required
                class="full-width-input"
              >
                <option
                  v-for="category in categories"
                  :key="category.id"
                  :value="category.id"
                >
                  {{ category.name }}
                </option>
              </select>
            </div>
            <div class="half-width">
              <label for="cuisine">Cocina:</label>
              <select
                v-model="recipe.cuisine_id"
                required
                class="full-width-input"
              >
                <option
                  v-for="cuisine in cuisines"
                  :key="cuisine.id"
                  :value="cuisine.id"
                >
                  {{ cuisine.country }}
                </option>
              </select>
            </div>
          </div>

          <!-- Title and Description -->
          <div class="form-group">
            <label for="title">Título:</label>
            <input
              type="text"
              id="title"
              v-model="recipe.title"
              required
              class="full-width-input"
            />
          </div>

          <div class="form-group">
            <label for="description">Descripción:</label>
            <textarea
              id="description"
              v-model="recipe.description"
              required
              class="full-width-input"
            ></textarea>
          </div>

          <!-- Button to autofill the form -->
          <button
            type="button"
            @click="autofillRecipe"
            class="auto-fill-button"
          >
            Auto Rellenar
          </button>

          <!-- Ingredients -->
          <div class="form-group">
            <label>Ingredientes:</label>
            <div
              v-for="(ingredient, index) in recipe.ingredients"
              :key="index"
              class="input-group"
            >
              <input
                type="text"
                v-model="recipe.ingredients[index]"
                placeholder="Añadir ingrediente"
                class="full-width-input"
              />
            </div>
            <button type="button" @click="addIngredient" class="add-button">
              +
            </button>
          </div>

          <!-- Steps -->
          <div class="form-group">
            <label>Pasos:</label>
            <div
              v-for="(step, index) in recipe.steps"
              :key="index"
              class="input-group"
            >
              <input
                type="text"
                v-model="recipe.steps[index]"
                placeholder="Añadir paso"
                class="full-width-input"
              />
            </div>
            <button type="button" @click="addStep" class="add-button">+</button>
          </div>

          <!-- Nutritional Information -->
          <div class="form-group">
            <label>Información Nutricional:</label>
            <div class="input-group">
              <label for="calories">Calorías:</label>
              <input
                type="number"
                id="calories"
                v-model="recipe.calories"
                class="full-width-input"
              />
            </div>
            <div class="input-group">
              <label for="protein">Proteínas (g):</label>
              <input
                type="number"
                id="protein"
                v-model="recipe.protein"
                class="full-width-input"
              />
            </div>
            <div class="input-group">
              <label for="fats">Grasas (g):</label>
              <input
                type="number"
                id="fats"
                v-model="recipe.fats"
                class="full-width-input"
              />
            </div>
            <div class="input-group">
              <label for="carbs">Carbohidratos (g):</label>
              <input
                type="number"
                id="carbs"
                v-model="recipe.carbs"
                class="full-width-input"
              />
            </div>
          </div>

          <!-- Time and Portions in one row -->
          <div class="form-group form-row">
            <div class="third-width">
              <label for="prepTime">Tiempo de Preparación (minutos):</label>
              <input
                type="number"
                id="prepTime"
                v-model="recipe.prep_time"
                required
                class="full-width-input"
              />
            </div>
            <div class="third-width">
              <label for="cookTime">Tiempo de Cocción (minutos):</label>
              <input
                type="number"
                id="cookTime"
                v-model="recipe.cook_time"
                required
                class="full-width-input"
              />
            </div>
            <div class="third-width">
              <label for="servings">Porciones:</label>
              <input
                type="number"
                id="servings"
                v-model="recipe.servings"
                required
                class="full-width-input"
              />
            </div>
          </div>

          <!-- Image Upload -->
          <div class="form-group">
            <label for="image">Subir Imagen:</label>
            <input
              type="file"
              @change="onFileChange"
              class="full-width-input"
            />
            <button type="button" @click="uploadImage" class="upload-button">
              Subir Imagen
            </button>
            <img
              v-if="recipe.image"
              :src="recipe.image"
              alt="Imagen subida"
              class="uploaded-image"
            />
          </div>

          <!-- Submit Button -->
          <button type="submit" class="submit-button">Crear Receta</button>
        </form>
      </div>

      <div v-if="message" :class="messageClass" class="message-container">
        {{ message }}
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import communicationManager from "@/services/communicationManager";
import Groq from "groq-sdk";

// Initialize the Groq API
const groq = new Groq({
  apiKey: import.meta.env.VITE_GROQ_API_KEY,
  dangerouslyAllowBrowser: true,
});

const cloudName = "dt5vjbgab";
const uploadPreset = "ejemplo1";

export default {
  setup() {
    const router = useRouter();
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
      servings: 0,
      category_id: null,
      cuisine_id: null,
      image: null,
    });
    const selectedFile = ref(null);
    const message = ref("");
    const messageClass = ref("");

    onMounted(async () => {
      try {
        user.value = await communicationManager.getUser();
        categories.value = await communicationManager.fetchCategories();
        cuisines.value = await communicationManager.fetchCuisines();
      } catch (error) {
        console.error("Error fetching data:", error);
      }
    });

    const autofillRecipe = async () => {
      if (!recipe.value.category_id || !recipe.value.cuisine_id) {
        message.value = "Select category and cuisine first.";
        messageClass.value = "error";
        return;
      }

      try {
        const userMessage = ` 
        Please return a JSON object with the following structure: Please make sure to return **only the JSON object** with the data of the recipe, without any extra text or explanation.
{
  "Ingredientes": [],
  "Pasos": [],
  calories: 0,
      "Calorías": 0,
    "Grasas": 0,
    "Proteínas": 0,
    "Carbohidratos": 0
  "Tiempo de Preparación (minutos)": 0,
  "Tiempo de Cocción (minutos)": 0,
  "Porciones": 0
}

        Name: ${recipe.value.title}, 
        Category: ${
          categories.value.find((c) => c.id === recipe.value.category_id).name
        }, 
        Cuisine: ${
          cuisines.value.find((c) => c.id === recipe.value.cuisine_id).country
        }, 
        Description: ${recipe.value.description}`;

        console.log("Sending to AI:", userMessage);

        const response = await groq.chat.completions.create({
          model: "llama-3.3-70b-versatile",
          messages: [{ role: "user", content: userMessage }],
        });

        console.log("AI Response:", response);

        const aiData = response.choices[0].message.content;
        console.log("AI Data:", aiData);

        try {
          const modifiedData = aiData.substring(
            aiData.indexOf("{"),
            aiData.lastIndexOf("}") + 1
          );
          console.log("Modified Data:", modifiedData);

          const parsedData = JSON.parse(modifiedData);

          console.log("Parsed JSON Data:", parsedData);

          recipe.value.ingredients = parsedData.Ingredientes || [];
          recipe.value.steps = parsedData.Pasos || [];
          recipe.value.prep_time =
            parsedData["Tiempo de Preparación (minutos)"] || 0;
          recipe.value.calories = parsedData["Calorías"] || 0;
          recipe.value.carbs = parsedData["Carbohidratos"] || 0;
          recipe.value.fats = parsedData["Grasas"] || 0;
          recipe.value.protein = parsedData["Proteínas"] || 0;
          recipe.value.cook_time =
            parsedData["Tiempo de Cocción (minutos)"] || 0;
          recipe.value.servings = parsedData.Porciones || 0;

          console.log("Updated Recipe:", recipe.value);
        } catch (error) {
          console.error("Error parsing JSON:", error);
          console.log("Raw Data:", aiData); // Log the raw response

        }

        message.value = "Formulario completado automáticamente.";
        messageClass.value = "success";
      } catch (error) {
        console.error("Error fetching AI data:", error);
        message.value = "Error al rellenar el formulario.";
        messageClass.value = "error";
      }
    };

    const addIngredient = () => recipe.value.ingredients.push("");
    const addStep = () => recipe.value.steps.push("");

    const onFileChange = (e) => {
      selectedFile.value = e.target.files[0];
    };

    const uploadImage = async () => {
      if (!selectedFile.value) return;

      const formData = new FormData();
      formData.append("file", selectedFile.value);
      formData.append("upload_preset", uploadPreset);

      try {
        const response = await axios.post(
          `https://api.cloudinary.com/v1_1/${cloudName}/image/upload`,
          formData
        );
        recipe.value.image = response.data.secure_url;
        console.log("Image uploaded successfully", response.data.secure_url);
      } catch (error) {
        console.error("Error uploading image:", error);
      }
    };

    const submitRecipe = async () => {
      if (!user.value) {
        console.error("User not authenticated");
        return;
      }

      try {
        await communicationManager.createRecipe({
          ...recipe.value,
          user_id: user.value.id,
        });

        message.value = "Recipe created successfully!";
        messageClass.value = "success";
        console.log("Recipe created successfully");

        router.push({ name: "LandingPage" });
      } catch (error) {
        console.error("Error creating recipe:", error);
        message.value = "Error creating recipe!";
        messageClass.value = "error";
      }
    };

    return {
      recipe,
      categories,
      cuisines,
      autofillRecipe,
      addIngredient,
      addStep,
      onFileChange,
      uploadImage,
      submitRecipe,
      message,
      messageClass,
    };
  },
};
</script>

<style scoped>
.container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
  padding: 20px;
}

.glass-form {
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
  border-radius: 15px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  padding: 2rem;
  width: 100%;
  margin-bottom: 70px;
}

h1 {
  text-align: center;
  margin-bottom: 1.5rem;
  font-size: 2rem;
  color: #333;
}

.form-container {
  overflow: hidden;
  width: 100%;
}

.form-group {
  margin-bottom: 1.5rem;
}

label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #333;
}

form {
  width: 100%;
}

input,
select,
textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ccc;
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.8);
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
}

.half-width {
  flex: 1;
}

.third-width {
  flex: 1;
}

.full-width-input {
  width: 100%;
}

.add-button,
.upload-button {
  padding: 0.5rem 1rem;
  background: #3b82f6;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1rem;
  margin-top: 0.5rem;
}

.add-button:hover,
.upload-button:hover {
  background: #2563eb;
}

.submit-button {
  width: 100%;
  padding: 0.75rem;
  background: #10b981;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1rem;
  margin-top: 1rem;
}

.submit-button:hover {
  background: #059669;
}

.uploaded-image {
  margin-top: 1rem;
  max-width: 100%;
  border-radius: 8px;
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
  color: #ef4444;
}

/* Responsive Design */
@media (max-width: 768px) {
  .form-row {
    flex-direction: column;
  }

  .half-width,
  .third-width {
    width: 100%;
  }

  .glass-form {
    padding: 1rem;
  }

  h1 {
    font-size: 1.5rem;
  }
}
</style>
