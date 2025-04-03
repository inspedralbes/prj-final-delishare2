<template>

  <div class="profile-container">
    <!-- Mostrar contenido solo si está autenticado -->
    <div v-if="authStore.isAuthenticated">
      <!-- Popup de notificación -->
      <div v-if="popupMessage" class="popup-notification">
        {{ popupMessage }}
      </div>

      <div class="profile-header">
        <button @click="toggleSettingsMenu" class="settings-btn">
          <img :src="settingsIcon" alt="Ajustes" class="settings-icon" />
        </button>
        <label for="file-input" class="profile-picture">
          <img :src="user.img || defaultProfile" alt="Foto de perfil" />
        </label>
        <h2>{{ user.name }}</h2>
        <p class="user-bio">{{ user.bio || 'No hay biografia disponible' }}</p>
      </div>

      <!-- Menu de ajustes con overlay -->
      <div v-if="settingsMenuOpen" class="settings-overlay" @click="toggleSettingsMenu">
        <div class="settings-menu" @click.stop>
          <button @click="setActiveTab('image')">Canviar imatge de perfil</button>
          <button @click="setActiveTab('name')">Canviar nom d'usuari i email</button>
          <button @click="setActiveTab('password')">Canviar contrasenya</button>
          <button @click="confirmLogout('logOut')">Tancar sessió</button>

        </div>
      </div>
      <!-- Popup de confirmación para cerrar sesión -->
      <div v-if="showLogoutConfirmation" class="popup-confirmation">
        <p>Estàs segur que vols tancar la sessió?</p>
        <button @click="logout" class="confirm-btn">Si</button>
        <button @click="cancelLogout" class="cancel-btn">Cancelar</button>
      </div>
      <!-- Formulario de cambio de imagen de perfil -->
      <div v-if="activeTab === 'image'" class="settings-form wide-form">
        <div class="upload-image-container">
          <label for="image" class="upload-label">Pujar Imatge:</label>
          <div class="upload-area">
            <input type="file" id="image" @change="uploadImage" accept="image/*" />
            <p class="upload-instructions">Arrossega i deixa anar una imatge o fes clic per seleccionar-la.</p>
          </div>
        </div>
        <button @click="cancelEdit" class="cancel-btn">Cancelar</button>
      </div>

      <div v-if="activeTab === 'name'" class="settings-form" style="margin-top: 70px;">
        <label for="newName">Nou nom:</label>
        <input type="text" id="newName" v-model="newName" :placeholder="user.name" />

        <label for="newEmail">Nou email:</label>
        <input type="email" id="newEmail" v-model="newEmail" :placeholder="user.email" />

        <label for="newBio">Biografia:</label>
        <textarea id="newBio" v-model="newBio" :placeholder="user.bio || 'Afegeix una biografia...'"
          rows="4"></textarea>

        <button @click="updateProfile" class="submit-btn">Guardar canvis</button>
        <button @click="cancelEdit" class="cancel-btn">Cancelar</button>
      </div>

      <!-- Formulario de cambio de contraseña -->
      <div v-if="activeTab === 'password'" class="settings-form" style="margin-top: 70px;">
        <label for="currentPassword">Contrasenya actual:</label>
        <div class="password-input-container">
          <input :type="showCurrentPassword ? 'text' : 'password'" id="currentPassword" v-model="currentPassword" />
          <img :src="showCurrentPassword ? eyeOpenIcon : eyeClosedIcon" alt="Ver contraseña"
            class="password-toggle-icon" @click="togglePasswordVisibility('current')" />
        </div>

        <label for="newPassword">Nova contrasenya:</label>
        <div class="password-input-container">
          <input :type="showNewPassword ? 'text' : 'password'" id="newPassword" v-model="newPassword" />
          <img :src="showNewPassword ? eyeOpenIcon : eyeClosedIcon" alt="Ver contraseña" class="password-toggle-icon"
            @click="togglePasswordVisibility('new')" />
        </div>

        <label for="confirmPassword">Confirmar nova contrasenya:</label>
        <div class="password-input-container">
          <input :type="showConfirmPassword ? 'text' : 'password'" id="confirmPassword" v-model="confirmPassword" />
          <img :src="showConfirmPassword ? eyeOpenIcon : eyeClosedIcon" alt="Ver contraseña"
            class="password-toggle-icon" @click="togglePasswordVisibility('confirm')" />
        </div>

        <button @click="updatePassword" class="submit-btn">Guardar canvis</button>
        <button @click="cancelEdit" class="cancel-btn">Cancelar</button>
      </div>

      <!-- Sección de recetas -->
      <div v-if="showRecipes" class="user-recipes">
        <h3>Les meves publicacions</h3>
        <div class="recipe-cards">
          <div v-for="recipe in recipes" :key="recipe.id">
            <RecipeCard :recipe-id="recipe.id" :title="recipe.title" :description="recipe.description"
              :image="recipe.image" />
            <button @click="deleteRecipe(recipe.id)" class="delete-btn">
              <img :src="binIcon" alt="Eliminar" class="delete-icon" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="auth-required-container">
      <div class="auth-required-message">
        <p>Per accedir al teu perfil, has d'iniciar sessió</p>
        <button @click="goToLogin" class="login-button">Iniciar Sessió</button>
      </div>
    </div>
  </div>
</template>

<script>
import { useAuthStore } from '@/stores/authStore';
import communicationManager from '@/services/communicationManager';
import { ref, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import RecipeCard from '@/components/RecipeCard.vue';
import axios from 'axios';

import defaultProfile from '@/assets/images/profile.svg';
import settingsIcon from '@/assets/images/settings.svg';
import eyeOpenIcon from '@/assets/images/eye-open.svg';
import eyeClosedIcon from '@/assets/images/eye-closed.svg';
import binIcon from '@/assets/images/bin.svg';

export default {
  components: { RecipeCard },
  setup() {
    const cloudName = 'dt5vjbgab';
    const uploadPreset = 'perfiles';
    const userImage = ref('/default-avatar.png');

    const authStore = useAuthStore();
    const router = useRouter();

    const user = ref({ name: '', email: '', img: '/default-avatar.png' });
    const recipes = ref([]);
    const settingsMenuOpen = ref(false);
    const activeTab = ref('');
    const newName = ref('');
    const newEmail = ref('');
    const newPassword = ref('');
    const confirmPassword = ref('');
    const currentPassword = ref('');
    const showRecipes = ref(true);
    const popupMessage = ref('');
    const showLogoutConfirmation = ref(false);
    const showCurrentPassword = ref(false);
    const showNewPassword = ref(false);
    const showConfirmPassword = ref(false);
    const newBio = ref('');

    const goToLogin = () => {
      router.push({
        name: 'login',
        query: { redirect: router.currentRoute.value.fullPath }
      });
    };

     onMounted ( async () => {
      try {
        const userData = await communicationManager.getUser();
        user.value = userData;
        newName.value = userData.name;
        newEmail.value = userData.email;
        newBio.value = userData.bio || '';
        const userRecipes = await communicationManager.getUserRecipes(userData.id);
        recipes.value = userRecipes.recipes;
      } catch (error) {
      }
    });

    const deleteRecipe = async (recipeId) => {
      try {
        await communicationManager.deleteRecipe(recipeId);
        recipes.value = recipes.value.filter(recipe => recipe.id !== recipeId);
        popupMessage.value = "La receta se ha eliminado correctamente";
        setTimeout(() => { popupMessage.value = ''; }, 3000);
      } catch (error) {
        console.error('Error eliminando receta', error);
        popupMessage.value = "Error al eliminar la receta";
        setTimeout(() => { popupMessage.value = ''; }, 3000);
      }
    };

    const uploadImage = async (event) => {
      const file = event.target.files[0];
      if (!file) return;

      const formData = new FormData();
      formData.append("file", file);
      formData.append("upload_preset", uploadPreset);

      try {
        const response = await axios.put(
          `https://api.cloudinary.com/v1_1/${cloudName}/image/upload`,
          formData
        );
        const uploadedImageUrl = response.data.secure_url;

        userImage.value = uploadedImageUrl;
        user.value.img = uploadedImageUrl;

        await communicationManager.updateProfilePicture({ img: uploadedImageUrl });

        popupMessage.value = "Foto de perfil actualizada correctamente";
        setTimeout(() => { popupMessage.value = ''; }, 3000);

        activeTab.value = '';
        showRecipes.value = true;
      } catch (error) {
        console.error("Error subiendo imagen de perfil:", error);
        popupMessage.value = "Error al subir la imagen de perfil";
        setTimeout(() => { popupMessage.value = ''; }, 3000);
      }
    };
    const toggleSettingsMenu = () => {
      settingsMenuOpen.value = !settingsMenuOpen.value;
    };

    const setActiveTab = (tab) => {
      activeTab.value = tab;
      showRecipes.value = false;
    };

    const togglePasswordVisibility = (field) => {
      if (field === 'current') {
        showCurrentPassword.value = !showCurrentPassword.value;
      } else if (field === 'new') {
        showNewPassword.value = !showNewPassword.value;
      } else if (field === 'confirm') {
        showConfirmPassword.value = !showConfirmPassword.value;
      }
    };

    // Cambiar updateName por updateProfile para incluir la bio
    const updateProfile = async () => {
      try {
        await communicationManager.updateProfile({
          name: newName.value,
          email: newEmail.value,
          bio: newBio.value
        });
        user.value.name = newName.value;
        user.value.email = newEmail.value;
        user.value.bio = newBio.value;
        popupMessage.value = "Perfil actualizado correctamente";
        setTimeout(() => { popupMessage.value = ''; }, 3000);
        activeTab.value = '';
        settingsMenuOpen.value = false;
        showRecipes.value = true;
      } catch (error) {
        console.error('Error actualizando perfil', error);
        popupMessage.value = "Error al actualizar perfil";
        setTimeout(() => { popupMessage.value = ''; }, 3000);
      }
    };

    const updatePassword = async () => {
      if (newPassword.value !== confirmPassword.value) {
        popupMessage.value = "Las contraseñas no coinciden";
        setTimeout(() => { popupMessage.value = ''; }, 3000);
        return;
      }

      try {
        await communicationManager.changePassword({
          contrasena_actual: currentPassword.value,
          nueva_contrasena: newPassword.value,
        });
        popupMessage.value = "Contraseña actualizada correctamente";
        setTimeout(() => { popupMessage.value = ''; }, 3000);

        const userRecipes = await communicationManager.getUserRecipes(user.value.id);
        recipes.value = userRecipes.recipes;

        activeTab.value = '';
        showRecipes.value = true;
      } catch (error) {
        console.error('Error actualitzant contrasenya:', error);
        if (error.response) {
          popupMessage.value = `Error: ${error.response.data.message || 'Detalles no disponibles'}`;
          setTimeout(() => { popupMessage.value = ''; }, 3000);
        }
      }
    };

    const cancelEdit = () => {
      settingsMenuOpen.value = false;
      activeTab.value = '';
      showRecipes.value = true;
    };

    const confirmLogout = () => {
      showLogoutConfirmation.value = true;
    };

    const logout = () => {
      authStore.clearAuth();
      window.location.href = '/';
      console.log('Cerrando sesión...');
      showLogoutConfirmation.value = false;
    };

    const cancelLogout = () => {
      showLogoutConfirmation.value = false;
    };
    return {
      authStore, // Asegúrate de incluir esto
      router,    // Y esto si lo usas en el template
      user,
      userImage,
      recipes,
      settingsMenuOpen,
      activeTab,
      setActiveTab,
      newBio,
      updateProfile,
      updatePassword,
      cancelEdit,
      showRecipes,
      newName,
      newEmail,
      newPassword,
      confirmPassword,
      currentPassword,
      showRecipes,
      popupMessage,
      showLogoutConfirmation,
      showCurrentPassword,
      showNewPassword,
      showConfirmPassword,
      settingsIcon,
      eyeOpenIcon,
      eyeClosedIcon,
      binIcon,
      defaultProfile,
      goToLogin,
      toggleSettingsMenu,
      setActiveTab,
      updatePassword,
      cancelEdit,
      confirmLogout,
      logout,
      cancelLogout,
      togglePasswordVisibility,
      deleteRecipe,
      uploadImage
    };
  }
};
</script>

<style scoped>
/* Añade estos nuevos estilos */
.auth-required-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 70vh;
}

.auth-required-message {
  text-align: center;
  padding: 2rem;
  background-color: #f8f9fa;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  max-width: 400px;
  width: 100%;
}

.auth-required-message p {
  margin-bottom: 1.5rem;
  font-size: 1.1rem;
  color: #343a40;
}

.login-button {
  padding: 0.75rem 1.5rem;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.3s;
}

.login-button:hover {
  background-color: #45a049;
}

* {
  font-family: 'Times New Roman', Times, serif;
}

.profile-container {
  padding: 20px;
  position: relative;
}

.profile-header {
  text-align: center;
  position: relative;
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

.recipe-cards {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 50px;
}

.delete-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 5px;
}

.delete-icon {
  width: 20px;
  height: 20px;
}

.settings-btn {
  position: absolute;
  top: -10px;
  right: -10px;
  background: none;
  border: none;
  cursor: pointer;
}

.settings-icon {
  width: 24px;
  height: 24px;
}

.settings-menu {
  position: absolute;
  top: 30px;
  right: 0;
  background: white;
  border: 1px solid #ccc;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
  border-radius: 8px;
  padding: 20px;
  display: flex;
  flex-direction: column;
  width: 250px;
  height: auto;
}

.settings-form {
  margin-top: 70px;
}

.settings-menu button {
  padding: 12px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 8px;
  background-color: #030127;
  margin-bottom: 10px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.settings-menu button:hover {
  background-color: #322b5f;
}

.settings-form label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}

.settings-form input {
  width: 100%;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #ccc;
  margin-bottom: 10px;
  box-sizing: border-box;
}

.submit-btn,
.cancel-btn {
  padding: 5px 15px;
  font-size: 16px;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  margin-top: 10px;
  margin-right: 10px;
}

.submit-btn {
  background-color: #0c0636;
  color: white;
}

.submit-btn:hover {
  background-color: #322b5f;
}

.cancel-btn {
  background-color: #9c1208;
  color: white;
}

.cancel-btn:hover {
  background-color: #e53935;
}

.password-input-container {
  position: relative;
  width: 100%;
}

.password-toggle-icon {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  width: 20px;
  height: 20px;
  cursor: pointer;
}

button {
  background-color: #0c0636;
  color: white;
  border-radius: 5px;

}

.wide-form {
  width: 250px;
  height: 150px;
  padding: 20px;
  margin: 20px auto;
  margin-top: 60px;
  background: #ffffff;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  border: 2px solid #ccc;
}

.wide-form input[type="file"] {
  width: 80%;
  padding: 10px;
  border-radius: 5px;
  border: 1px solid #ccc;
  background: #f5f5f5;
  margin-top: 15px;
  font-size: 14px;
  cursor: pointer;
}

.wide-form input[type="file"]::-webkit-file-upload-button {
  background-color: #0c0636;
  color: white;
  border: none;
  padding: 10px 15px;
  border-radius: 5px;
  cursor: pointer;
  transition: background 0.3s ease;
}

.wide-form input[type="file"]::-webkit-file-upload-button:hover {
  background-color: #7582e7;
}

.wide-form button {
  width: 80%;
  padding: 10px;
  margin-top: 15px;
  background-color: #0c0636;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background 0.3s ease;
}

.wide-form button:hover {
  background-color: #7582e7;
}

.custom-file-upload input[type="file"] {
  display: none;
}

.custom-file-upload {
  display: inline-block;
  padding: 10px 15px;
  color: #0c0636;
  font-size: 14px;
  border: 2px solid #0c0636;
  font-weight: bold;
  border-radius: 5px;
  cursor: pointer;
  text-align: center;
  transition: background 0.3s ease;
}

.custom-file-upload:hover {
  background-color: #7582e7;
}

.popup-notification {
  position: fixed;
  top: 20px;
  right: 20px;
  background: #3498db;
  color: #fff;
  padding: 10px 20px;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  animation: fadeIn 0.5s ease;
}

.settings-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: transparent;
  z-index: 900;
}

.popup-confirmation {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 10px;
}

.popup-confirmation p {
  background-color: white;
  padding: 20px;
  border-radius: 8px;
  text-align: center;
  width: 100%;
  max-width: 300px;
}

.popup-confirmation button {
  font-size: 16px;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  margin: 5px;
  padding: 10px 20px;
  transition: background-color 0.3s ease;
  width: 100px;
}

.upload-image-container {
  margin-top: -50px;
  margin-bottom: 1.5rem;
  text-align: center;
}

.upload-label {
  display: block;
  margin-top: 150px;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #333;

}

.user-bio {
  max-width: 80%;
  margin: 0 auto;
  padding: 10px;
  font-style: italic;
  color: #555;
  line-height: 1.4;
  white-space: pre-line;
  /* Para mantener los saltos de línea */
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
  border-color: #059669;
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


.cancel-btn:hover {
  background-color: #e53935;
}

.popup-confirmation .confirm-btn {
  background-color: #0c0636;
  color: white;
}

.popup-confirmation .confirm-btn:hover {
  background-color: #322b5f;
}

.popup-confirmation .cancel-btn {
  background-color: #9c1208;
  color: white;
}

.popup-confirmation .cancel-btn:hover {
  background-color: #e53935;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>