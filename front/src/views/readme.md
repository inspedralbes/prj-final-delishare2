<template>
  <div class="profile-container">
    <div class="profile-header">
      <label for="file-input" class="profile-picture">
        <img :src="user.img" alt="Foto de perfil" />
        <input id="file-input" type="file" @change="uploadImage" style="display: none;" />
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
          <input :type="showCurrentPassword ? 'text' : 'password'" v-model="currentPassword" />
          <button @click="toggleShowCurrentPassword" class="eye-icon">
            <img v-if="showCurrentPassword" src="@/assets/images/ojoAbierto.png" alt="Mostrar contraseña" />
            <img v-if="!showCurrentPassword" src="@/assets/images/ojo.png" alt="Ocultar contraseña" />
          </button>
        </div>
        <label>Nueva Contraseña:</label>
        <div class="password-field">
          <input :type="showNewPassword ? 'text' : 'password'" v-model="newPassword" />
          <button @click="toggleShowNewPassword" class="eye-icon">
            <img v-if="showNewPassword" src="@/assets/images/ojoAbierto.png" alt="Mostrar contraseña" />
            <img v-if="!showNewPassword" src="@/assets/images/ojo.png" alt="Ocultar contraseña" />
          </button>
        </div>
        <label>Confirmar Nueva Contraseña:</label>
        <div class="password-field">
          <input :type="showConfirmPassword ? 'text' : 'password'" v-model="confirmPassword" />
          <button @click="toggleShowConfirmPassword" class="eye-icon">
            <img v-if="showConfirmPassword" src="@/assets/images/ojoAbierto.png" alt="Mostrar contraseña" />
            <img v-if="!showConfirmPassword" src="@/assets/images/ojo.png" alt="Ocultar contraseña" />
          </button>
        </div>
        <button @click="updatePassword">Cambiar Contraseña</button>
      </div>
    </div>

    <!-- Botones de Ver Publicaciones y Mis Guardadas -->
    <div class="profile-actions">
      <button @click="activeTab = 'publications'">Ver Publicaciones</button>
      <button @click="activeTab = 'savedRecipes'">Mis Guardadas</button>
    </div>

    <!-- Mostrar las recetas creadas por el usuario en tarjetas -->
    <div v-if="activeTab === 'publications' && recipes.length > 0" class="user-recipes">
      <h3>Recetas creadas</h3>
      <div class="recipe-cards">
        <div class="recipe-card" v-for="recipe in recipes" :key="recipe.id">
          <router-link :to="{ name: 'InfoReceta', params: { recipeId: recipe.id } }">
            <img :src="recipe.image" :alt="recipe.title" class="recipe-image" />
            <div class="recipe-info">
              <p class="recipe-title">{{ recipe.title }}</p>
              <p class="recipe-description">{{ truncateDescription(recipe.description) }}</p>
            </div>
          </router-link>
          <button @click="deleteUserRecipe(recipe.id)">Eliminar</button>
        </div>
      </div>
    </div>

    <!-- Mostrar mis carpetas y recetas guardadas -->
    <div v-if="activeTab === 'savedRecipes'" class="saved-recipes">
      <input v-if="showCreateFolderInput" type="text" v-model="newFolderName" placeholder="Nombre de nueva carpeta" />
      <button v-if="showCreateFolderInput" @click="createFolder">Crear Carpeta</button>
      <button v-else @click="showCreateFolderInput = true">Añadir Carpeta</button>

      <div class="folders" v-if="folders.length > 0">
        <div class="folder-card" v-for="folder in folders" :key="folder.id" @click="fetchFolderRecipes(folder.id)">
          <h4>{{ folder.name }}</h4>
          <button v-if="folder && folder.id" @click="deleteFolder(folder.id)">Eliminar Carpeta</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useAuthStore } from '@/stores/authStore';
import communicationManager from '@/services/communicationManager';
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';

export default {
  setup() {
    const cloudName = 'dt5vjbgab';
    const uploadPreset = 'perfiles';
    const authStore = useAuthStore();
    const user = ref({ name: '', email: '' });
    const currentPassword = ref('');
    const newPassword = ref('');
    const confirmPassword = ref('');
    const userImage = ref('/default-avatar.png');
    const recipes = ref([]);
    const folders = ref([]);
    const activeTab = ref('publications');
    const showEditProfile = ref(false);
    
    onMounted(async () => {
      try {
        const userData = await communicationManager.getUser();
        user.value = userData;
        userImage.value = userData.img || '/default-avatar.png';

        const userRecipes = await communicationManager.getUserRecipes();
        recipes.value = userRecipes;
      } catch (error) {
        console.error('Error cargando datos del usuario o recetas', error);
      }
    });

    const truncateDescription = (description) => {
      if (!description) return '';
      const words = description.split(' ');
      return words.length > 15 ? words.slice(0, 15).join(' ') + '...' : description;
    };

    return {
      user,
      userImage,
      currentPassword,
      newPassword,
      confirmPassword,
      recipes,
      folders,
      activeTab,
      showEditProfile,
      truncateDescription
    };
  }
};
</script>

<style scoped>
.profile-container {
  padding: 20px;
}

.profile-header {
  text-align: center;
}

.recipe-cards {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
}

.recipe-card {
  border: 1px solid #ccc;
  border-radius: 8px;
  width: 200px;
  padding: 10px;
}

.recipe-card img {
  width: 100%;
  height: 150px;
  object-fit: cover;
}

.recipe-info {
  text-align: center;
  margin-top: 10px;
}

.recipe-description {
  font-size: 10px;
  color: #666;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
}

.profile-container {
  padding: 20px;
}

.profile-header {
  text-align: center;
}

.profile-header .profile-picture img {
  border-radius: 50%;
  width: 100px;
  height: 100px;
  object-fit: cover;
}

.profile-header h2,
.profile-header p {
  margin: 10px 0;
}

.profile-actions {
  display: flex;
  justify-content: space-around;
  margin-top: 20px;
}

.profile-actions button {
  padding: 10px;
  background-color: #4caf50;
  color: white;
  border: none;
  cursor: pointer;
}

.profile-actions button:hover {
  background-color: #45a049;
}

.edit-sections button {
  margin: 10px;
  padding: 10px;
  background-color: #2196f3;
  color: white;
  border: none;
  cursor: pointer;
}

.edit-sections button:hover {
  background-color: #1e88e5;
}

.profile-info {
  margin: 20px 0;
}

.password-field {
  display: flex;
  align-items: center;
}

.password-field input {
  flex: 1;
  padding: 10px;
  margin-right: 10px;
  font-size: 14px;
}

.eye-icon {
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 5px;
}

.eye-icon img {
  width: 18px;
  height: 18px;
}

.recipe-cards {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
}

.recipe-card {
  border: 1px solid #ccc;
  border-radius: 8px;
  width: 200px;
  padding: 10px;
}

.recipe-card img {
  width: 100%;
  height: 150px;
  object-fit: cover;
}

.recipe-info {
  text-align: center;
  margin-top: 10px;
}

.folders {
  display: flex;
  gap: 20px;
  margin-top: 20px;
}

.folder-card {
  background-color: #f1f1f1;
  padding: 15px;
  cursor: pointer;
  text-align: center;
  border-radius: 8px;
}

.saved-recipes {
  margin-top: 20px;
}

</style>
