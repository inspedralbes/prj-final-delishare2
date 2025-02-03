<template>
    <div class="profile-container">
      <div class="profile-header">
        <label for="file-input" class="profile-picture">
          <img :src="userImage" alt="Foto de perfil" />
          <input id="file-input" type="file" @change="uploadImage" hidden />
        </label>
        <h2>{{ user.name }}</h2>
        <p>{{ user.email }}</p>
        <button @click="showEditProfile = !showEditProfile">Editar perfil</button>
      </div>
      
      <div v-if="showEditProfile" class="edit-sections">
        <button @click="activeTab = 'profile'">Editar Información</button>
        <button @click="activeTab = 'password'">Cambiar Contraseña</button>
        
        <div v-if="activeTab === 'profile'" class="profile-info">
          <label>Nombre:</label>
          <input type="text" v-model="user.name" />
          
          <label>Email:</label>
          <input type="email" v-model="user.email" />
          
          <button @click="updateProfile">Guardar cambios</button>
        </div>
        
        <div v-if="activeTab === 'password'" class="profile-info">
          <label>Contraseña Actual:</label>
          <div class="password-field">
            <input
              :type="showCurrentPassword ? 'text' : 'password'"
              v-model="currentPassword"
            />
            <button @click="toggleShowCurrentPassword" class="eye-icon">
              <img v-if="showCurrentPassword" src="@/assets/images/ojoAbierto.png" alt="Mostrar contraseña" />
              <img v-if="!showCurrentPassword" src="@/assets/images/ojo.png" alt="Ocultar contraseña" />
            </button>
          </div>
          <label>Nueva Contraseña:</label>
          <div class="password-field">
            <input
              :type="showNewPassword ? 'text' : 'password'"
              v-model="newPassword"
            />
            <button @click="toggleShowNewPassword" class="eye-icon">
              <img v-if="showNewPassword" src="@/assets/images/ojoAbierto.png" alt="Mostrar contraseña" />
              <img v-if="!showNewPassword" src="@/assets/images/ojo.png" alt="Ocultar contraseña" />
            </button>
          </div>
          <label>Confirmar Nueva Contraseña:</label>
          <div class="password-field">
            <input
              :type="showConfirmPassword ? 'text' : 'password'"
              v-model="confirmPassword"
            />
            <button @click="toggleShowConfirmPassword" class="eye-icon">
              <img v-if="showConfirmPassword" src="@/assets/images/ojoAbierto.png" alt="Mostrar contraseña" />
              <img v-if="!showConfirmPassword" src="@/assets/images/ojo.png" alt="Ocultar contraseña" />
            </button>
          </div>
          <button @click="updatePassword">Cambiar Contraseña</button>
        </div>
      </div>
      
      <!-- Mostrar las recetas creadas por el usuario en tarjetas -->
      <div class="user-recipes" v-if="recipes.length > 0">
        <h3>Recetas creadas</h3>
        <div class="recipe-cards">
          <div
            class="recipe-card"
            v-for="recipe in recipes"
            :key="recipe.id"
          >
            <router-link :to="{ name: 'InfoReceta', params: { recipeId: recipe.id } }">
              <img :src="recipe.image" :alt="recipe.title" class="recipe-image" />
              <div class="recipe-info">
                <p class="recipe-title">{{ recipe.title }}</p>
                <p class="recipe-description">{{ recipe.description }}</p>
              </div>
            </router-link>
          </div>
        </div>
      </div>
      <div v-else>
        <p>No has creado ninguna receta aún.</p>
      </div>
    </div>
  </template>
  
  <script>
  import { useAuthStore } from '@/stores/authStore';
  import communicationManager from '@/services/communicationManager';
  import { ref, onMounted } from 'vue';
  
  export default {
    setup() {
      const authStore = useAuthStore();
      const user = ref({ name: '', email: '' });
      const currentPassword = ref('');
      const newPassword = ref('');
      const confirmPassword = ref('');
      const userImage = ref('/default-avatar.png');
      const recipes = ref([]);
      const showEditProfile = ref(false);
      const activeTab = ref('profile');
  
      const showCurrentPassword = ref(false);
      const showNewPassword = ref(false);
      const showConfirmPassword = ref(false);
  
      // Obtener los datos del usuario y recetas creadas
      onMounted(async () => {
        try {
          const userData = await communicationManager.getUser();
          user.value = userData;
          userImage.value = userData.profile_picture || '/default-avatar.png';
  
          const userRecipes = await communicationManager.getUserRecipes();  // Asumimos que tienes un método para obtener recetas creadas
          recipes.value = userRecipes;
        } catch (error) {
          console.error('Error cargando datos del usuario o recetas', error);
        }
      });
  
      const updateProfile = async () => {
        try {
          await communicationManager.updateProfile({
            name: user.value.name,
            email: user.value.email,
          });
          alert("Perfil actualizado correctamente");
        } catch (error) {
          console.error('Error actualizando perfil', error);
        }
      };
  
      const updatePassword = async () => {
        if (newPassword.value !== confirmPassword.value) {
          alert("Las contraseñas no coinciden");
          return;
        }
  
        try {
          const response = await communicationManager.changePassword({
            contrasena_actual: currentPassword.value,
            nueva_contrasena: newPassword.value,
          });
          alert("Contraseña actualizada correctamente");
        } catch (error) {
          console.error('Error cambiando la contraseña', error);
          alert('Error al cambiar la contraseña');
        }
      };
  
      const toggleShowCurrentPassword = () => {
        showCurrentPassword.value = !showCurrentPassword.value;
      };
  
      const toggleShowNewPassword = () => {
        showNewPassword.value = !showNewPassword.value;
      };
  
      const toggleShowConfirmPassword = () => {
        showConfirmPassword.value = !showConfirmPassword.value;
      };
  
      const uploadImage = async (event) => {
        const file = event.target.files[0];
        if (file) {
          const formData = new FormData();
          formData.append('profile_picture', file);
  
          try {
            const response = await communicationManager.updateProfilePicture(formData);
            userImage.value = response.profile_picture;
          } catch (error) {
            console.error('Error subiendo la imagen', error);
          }
        }
      };
  
      return {
        user,
        currentPassword,
        newPassword,
        confirmPassword,
        userImage,
        recipes,
        showEditProfile,
        activeTab,
        showCurrentPassword,
        showNewPassword,
        showConfirmPassword,
        updateProfile,
        updatePassword,
        toggleShowCurrentPassword,
        toggleShowNewPassword,
        toggleShowConfirmPassword,
        uploadImage,
      };
    },
  };
  </script>
  
  <style scoped>
  /* Tu estilo original para el perfil */
  .profile-container {
    padding: 20px;
  }
  
  .profile-header {
    text-align: center;
    margin-bottom: 20px;
  }
  
  .profile-picture img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
  }
  
  .profile-info {
    margin-bottom: 20px;
  }
  
  .profile-info label {
    display: block;
    margin-bottom: 5px;
  }
  
  .profile-info input {
    width: 100%;
    padding: 8px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
  }
  
  .profile-info button {
    padding: 10px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  
  .profile-info button:hover {
    background-color: #0056b3;
  }
  
  /* Estilos para las recetas */
  .user-recipes {
    margin-top: 20px;
  }
  
  .recipe-cards {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
  }
  
  .recipe-card {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    cursor: pointer;
    transition: transform 0.3s ease-in-out;
  }
  
  .recipe-card:hover {
    transform: scale(1.05);
  }
  
  .recipe-image {
    width: 100%;
    height: 150px;
    object-fit: cover;
  }
  
  .recipe-info {
    padding: 10px;
  }
  
  .recipe-title {
    font-size: 1.2em;
    font-weight: bold;
  }
  
  .recipe-description {
    font-size: 0.9em;
    color: #666;
  }
  
  /* Estilos para la vista de contraseña */
  .password-field {
    display: flex;
    align-items: center;
  }
  
  .password-field input {
    width: calc(100% - 40px);
  }
  
  .eye-icon {
    background: none;
    border: none;
    cursor: pointer;
    margin-left: 10px;
  }
  
  .eye-icon img {
    width: 20px;
    height: 20px;
  }
  </style>
  