<template>
  <div class="page-container">
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
            <input type="number" id="servings" v-model="recipe.servings" required class="full-width-input" />
          </div>

          <button type="button" @click="autofillRecipe" class="auto-fill-button">
            Omplir automàticament
          </button>

          <div class="form-group">
            <label for="description">Descripció:</label>
            <textarea id="description" v-model="recipe.description" class="full-width-input"></textarea>
          </div>


          <div class="form-group">
            <label>Ingredients:</label>
            <div v-for="(ingredient, index) in recipe.ingredients" :key="index" class="input-group">
              <input type="text" v-model="recipe.ingredients[index]" placeholder="Afegeix un ingredient"
                class="full-width-input" />
            </div>
            <button type="button" @click="addIngredient" class="add-button">+</button>
          </div>

          <div class="form-group">
            <label>Passos:</label>
            <div v-for="(step, index) in recipe.steps" :key="index" class="input-group">
              <input type="text" v-model="recipe.steps[index]" placeholder="Afegeix un pas" class="full-width-input" />
            </div>
            <button type="button" @click="addStep" class="add-button">+</button>
          </div>

          <div class="form-group">
            <label>Informació Nutricional:</label>
            <div class="input-group">
              <label for="calories">Calories:</label>
              <input type="number" id="calories" v-model="recipe.calories" class="full-width-input" />
            </div>
            <div class="input-group">
              <label for="protein">Proteïnes (g):</label>
              <input type="number" id="protein" v-model="recipe.protein" class="full-width-input" />
            </div>
            <div class="input-group">
              <label for="fats">Greixos (g):</label>
              <input type="number" id="fats" v-model="recipe.fats" class="full-width-input" />
            </div>
            <div class="input-group">
              <label for="carbs">Carbohidrats (g):</label>
              <input type="number" id="carbs" v-model="recipe.carbs" class="full-width-input" />
            </div>
          </div>

          <div class="form-group form-row">
            <div class="third-width">
              <label for="prepTime">Temps de Preparació (minuts):</label>
              <input type="number" id="prepTime" v-model="recipe.prep_time" required class="full-width-input" />
            </div>
            <div class="third-width">
              <label for="cookTime">Temps de Cocció (minuts):</label>
              <input type="number" id="cookTime" v-model="recipe.cook_time" required class="full-width-input" />
            </div>

          </div>

          <div class="form-group">
            <div class="upload-image-container">
              <label for="image" class="upload-label">Pujar Imatge:</label>
              <div class="upload-area">
                <input type="file" id="image" @change="onFileChange" accept="image/*" />
                <p class="upload-instructions">Arrossega i deixa anar una imatge o fes clic per seleccionar-la.</p>
              </div>
              <img v-if="recipe.image" :src="recipe.image" alt="Imatge pujada" class="uploaded-image-preview" />
            </div>
          </div>
          <button type="submit" class="submit-button">Crear Recepta</button>
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
        message.value = "Selecciona una categoria i una cuina primer.";
        messageClass.value = "error";
        return;
      }

      try {
        const userMessage = ` 
    Genera un objecte JSON per a una recepta basada en aquestes dades:
    - Nom: ${recipe.value.title}
    - Categoria: ${categories.value.find((c) => c.id === recipe.value.category_id).name}
    - Cuina: ${cuisines.value.find((c) => c.id === recipe.value.cuisine_id).country}
    - Racions: ${recipe.value.servings}

    Assegura't que el JSON segueix aquesta estructura sense cap text addicional:

    {
      "description": "Descripció breu de la recepta",
      "ingredients": ["Ingredient 1", "Ingredient 2"],
      "steps": ["Pas 1", "Pas 2"],
      "calories": 0,
      "fats": 0,
      "protein": 0,
      "carbs": 0,
      "prep_time": 0,
      "cook_time": 0
    }`;

        console.log("Enviant a la IA:", userMessage);

        const response = await groq.chat.completions.create({
          model: "llama-3.3-70b-versatile",
          messages: [{ role: "user", content: userMessage }],
          response_format: { type: "json_object" },
        });

        console.log("Resposta de la IA:", response);

        const aiData = response.choices[0].message.content;

        const parsedData = JSON.parse(aiData);

        // Assignar els valors a la recepta
        recipe.value.description = parsedData.description || "Sense descripció.";
        recipe.value.ingredients = parsedData.ingredients || [];
        recipe.value.steps = parsedData.steps || [];
        recipe.value.prep_time = parsedData.prep_time || 0;
        recipe.value.cook_time = parsedData.cook_time || 0;
        recipe.value.calories = parsedData.calories || 0;
        recipe.value.carbs = parsedData.carbs || 0;
        recipe.value.fats = parsedData.fats || 0;
        recipe.value.protein = parsedData.protein || 0;

        console.log("Recepta actualitzada:", recipe.value);

        message.value = "Formulari completat automàticament.";
        messageClass.value = "success";
      } catch (error) {
        console.error("Error obtenint dades de la IA:", error);
        message.value = "Error en omplir el formulari.";
        messageClass.value = "error";
      }
    };

    const addIngredient = () => recipe.value.ingredients.push("");
    const addStep = () => recipe.value.steps.push("");

    const onFileChange = async (e) => {
      const file = e.target.files[0];
      if (!file) return;

      const formData = new FormData();
      formData.append("file", file);
      formData.append("upload_preset", uploadPreset);

      try {
        const response = await axios.post(
          `https://api.cloudinary.com/v1_1/${cloudName}/image/upload`,
          formData
        );
        recipe.value.image = response.data.secure_url;
        console.log("Imagen subida correctamente:", response.data.secure_url);
      } catch (error) {
        console.error("Error al subir la imagen:", error);
      }
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
        console.log("Imagen subida correctamente:", response.data.secure_url);
      } catch (error) {
        console.error("Error al subir la imagen:", error);
      }
    };

    const submitRecipe = async () => {
  if (!user.value) {
    console.error("Usuario no autenticado");
    return;
  }
  try {
    await communicationManager.createRecipe({
      ...recipe.value,
      user_id: user.value.id,
      nutrition: {
        calories: recipe.value.calories,
        protein: recipe.value.protein,
        fats: recipe.value.fats,
        carbs: recipe.value.carbs,
      },
    });
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
  color: #322b5f;
}

.upload-image-container {
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

.uploaded-image-preview {
  margin-top: 1rem;
  max-width: 100%;
  border-radius: 10px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
}
</style>
