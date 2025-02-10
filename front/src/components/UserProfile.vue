<template>
    <div class="user-profile">
      <h2>Perfil de Usuario</h2>
      <p><strong>Nombre:</strong> {{ user.name }}</p>
      <p><strong>Email:</strong> {{ user.email }}</p>
  
      <div class="buttons">
        <button @click="showPublications = true">Ver Publicaciones</button>
        <button @click="showPublications = false">Ver Carpetas</button>
      </div>
  
      <!-- Mostrar recetas publicadas -->
      <div v-if="showPublications">
        <p><strong>Recetas publicadas:</strong></p>
        <div class="recipes">
          <div v-for="recipe in user.recipes" :key="recipe.id" class="recipe-card">
            <router-link :to="'/info/' + recipe.id">
              <div class="card">
                <!-- Foto de la receta -->
                <img v-if="recipe.image" :src="recipe.image" alt="Receta" class="recipe-image" />
                <h3>{{ recipe.title }}</h3>
                <p>{{ recipe.description }}</p>
              </div>
            </router-link>
          </div>
        </div>
        <p v-if="!user.recipes || user.recipes.length === 0">Este usuario no tiene recetas publicadas.</p>
      </div>
  
      <!-- Mostrar carpetas del usuario y sus recetas -->
      <div v-else>
        <div v-if="user.folders && user.folders.length > 0">
          <p><strong>Carpetas:</strong></p>
          <div v-for="folder in user.folders" :key="folder.id">
            <p><strong>{{ folder.name }}</strong></p>
            <div class="recipes">
              <div v-for="recipe in user.recipes_in_folders[folder.name]" :key="recipe.id" class="recipe-card">
                <router-link :to="'/info/' + recipe.id">
                  <div class="card">
                    <!-- Foto de la receta -->
                    <img v-if="recipe.image" :src="recipe.image" alt="Receta" class="recipe-image" />
                    <h3>{{ recipe.title }}</h3>
                    <p>{{ recipe.description }}</p>
                  </div>
                </router-link>
              </div>
            </div>
          </div>
        </div>
        <p v-if="!user.folders || user.folders.length === 0">Este usuario no tiene carpetas.</p>
      </div>
  
      <!-- Error message display -->
      <p v-if="error" class="error">{{ error }}</p>
    </div>
  </template>
  
  <script>
  import { ref, onMounted } from 'vue';
  import axios from 'axios';
  
  export default {
    props: {
      userId: {
        type: String,
        required: true,
      },
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
      const showPublications = ref(true); // Determina si se muestran las publicaciones o las carpetas
  
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
  };
  </script>
  
  <style scoped>
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
  
  .user-profile .buttons {
    margin-bottom: 20px;
  }
  
  .user-profile button {
    padding: 10px 20px;
    margin-right: 10px;
    background-color: #63c132;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }
  
  .user-profile button:hover {
    background-color: #56a92e;
  }
  
  .user-profile .recipes {
    display: flex;
    flex-wrap: wrap;
  }
  
  .user-profile .recipe-card {
    width: 100%;
    max-width: 200px;
    margin: 10px;
    padding: 15px;
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    text-align: center;
  }
  
  .user-profile .recipe-card h3 {
    font-size: 18px;
    margin-bottom: 10px;
  }
  
  .user-profile .recipe-card p {
    font-size: 14px;
    color: #555;
  }
  
  .user-profile .recipe-card img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 10px;
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
  

