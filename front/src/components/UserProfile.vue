<template>

  <div class="user-profile">
    <button class="back-button" @click="goBack">← </button>
    <h2>Perfil d'Usuari</h2>
    <p><strong>Nom:</strong> {{ user.name }}</p>
    <p><strong>Correu electrònic:</strong> {{ user.email }}</p>

    <div>
      <button class="btn" @click="showPublications = true">Veure Publicacions</button>
      <button class="btn" @click="showPublications = false">Veure Carpetes</button>
    </div>

    <!-- Mostrar receptes publicades -->
    <div v-if="showPublications">
      <p><strong>Receptes publicades:</strong></p>
      <div class="recipes">
        <RecipeCard
          v-for="recipe in user.recipes"
          :key="recipe.id"
          :recipeId="recipe.id"
          :title="recipe.title"
          :description="recipe.description"
          :image="recipe.image"
        />
      </div>
      <p v-if="!user.recipes || user.recipes.length === 0">Aquest usuari no té receptes publicades.</p>
    </div>

    <!-- Mostrar carpetes de l'usuari i les seves receptes -->
    <div v-else>
      <div v-if="user.folders && user.folders.length > 0">
        <p><strong>Carpetes:</strong></p>
        <div v-for="folder in user.folders" :key="folder.id">
          <p><strong>{{ folder.name }}</strong></p>
          <div class="recipes">
            <div v-for="recipe in user.recipes_in_folders[folder.name]" :key="recipe.id">
              <RecipeCard
                :recipeId="recipe.id"
                :title="recipe.title"
                :description="recipe.description"
                :image="recipe.image"
              />
            </div>
          </div>
        </div>
      </div>
      <p v-if="!user.folders || user.folders.length === 0">Aquest usuari no té carpetes.</p>
    </div>


    <!-- Error -->
    <p v-if="error" class="error">{{ error }}</p>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import RecipeCard from '@/components/RecipeCard.vue';

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
    });
    const error = ref(null);
    const showPublications = ref(true); 

    const fetchUserData = async () => {
      try {
        const response = await axios.get(`http://127.0.0.1:8000/api/userInfo/${props.userId}`);
        const data = response.data;
        user.value = {
          name: data.name,
          email: data.email,
          recipes: data.recipes,
          folders: data.folders,
          recipes_in_folders: data.recipes_in_folders,
        };
      } catch (err) {
        error.value = 'Hubo un error al cargar los datos del usuario.';
        console.error(err);
      }
    };

    onMounted(fetchUserData);

    return {
      user,
      error,
      showPublications,
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
.back-button {
  background: transparent;
  color: #0c0636;
  border: 2px solid #0c0636;
  padding: 8px 16px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.back-button:hover {
  background: #004080;
  color: #fff;
}
.user-profile {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
  background-color: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.user-profile h2 {
  margin-bottom: 20px;
}

.btn {
  margin-bottom: 20px;
}

.btn {
  padding: 10px 20px;
  margin-right: 10px;
  background-color: #0c0636;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.btn:hover {
  background-color: #322b5f;
}

.user-profile .recipes {
  display: grid;
  grid-template-columns: repeat(2, 1fr); 
  gap: 10px; 
  justify-items: center; 
  margin-top: 20px;
  margin-bottom: 40px; 
}


@media (min-width: 1024px) {
  .user-profile .recipes {
    grid-template-columns: repeat(4, 1fr); 
    gap: 20px; 
  }

  .user-profile .recipe-card {
    width: 200px;
    height: 250px; 
    padding: 20px;
  }

  .user-profile .recipe-card img {
    height: 60%; 
  }

  .user-profile .recipe-card h3 {
    font-size: 16px;
  }

  .user-profile .recipe-card p {
    font-size: 14px; 
  }
}
@media (max-width: 367px) {
  .user-profile .recipes {
    grid-template-columns: repeat(2, 1fr); /* Dos columnas por fila en pantallas pequeñas */
  }

  .user-profile .recipe-card {
    width: 100%; /* Las tarjetas ocupan todo el ancho disponible */
    height: auto; /* La altura es automática */
    padding: 10px;
  }

  .user-profile .recipe-card img {
    height: 70%; /* Mantener la proporción de imagen */
  }

  .user-profile .recipe-card h3 {
    font-size: 12px;
  }

  .user-profile .recipe-card p {
    font-size: 10px;
  }
}

.user-profile .recipe-card {
  width: 150px; /* Ancho fijo de 140px para pantallas pequeñas */
  height: 150px; /* Alto fijo de 140px */
  
  background-color: #f9f9f9;
  border-radius: 8px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.user-profile .recipe-card img {
  width: 100%;
  height: 70%; /* Ajustamos la altura de la imagen */
  object-fit: cover;
  border-radius: 8px;
  margin-bottom: 10px;
}

.user-profile .recipe-card h3 {
  font-size: 12px; /* Texto más pequeño para ajustarse al tamaño de la tarjeta */
  margin-bottom: 5px;
}

.user-profile .recipe-card p {
  font-size: 10px; /* Texto aún más pequeño para ajustarse a la tarjeta */
  color: #555;
}

.user-profile .recipe-card a {
  text-decoration: none;
  color: inherit;
}

.user-profile p {
  font-size: 16px;
  color: #555;
}

.user-profile .error {
  color: red;
  font-weight: bold;
}


</style>