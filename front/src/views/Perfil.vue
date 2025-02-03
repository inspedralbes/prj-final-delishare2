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
          <input type="password" v-model="currentPassword" />
          
          <label>Nueva Contraseña:</label>
          <input type="password" v-model="newPassword" />
          
          <label>Confirmar Nueva Contraseña:</label>
          <input type="password" v-model="confirmPassword" />
          
          <button @click="updatePassword">Cambiar Contraseña</button>
        </div>
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
      const newPassword = ref('');
      const confirmPassword = ref('');
      const currentPassword = ref('');
      const userImage = ref('/default-avatar.png');
      const showEditProfile = ref(false);
      const activeTab = ref('profile');
  
      onMounted(async () => {
        try {
          const response = await communicationManager.getUser();
          user.value = response;
          userImage.value = response.profile_picture || '/default-avatar.png';
        } catch (error) {
          console.error('Error cargando datos del usuario', error);
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
  
        const token = localStorage.getItem('token');
        console.log("Token:", token); // Asegúrate de que el token esté presente
  
        try {
          // Enviar tanto la contraseña actual como la nueva contraseña
          const response = await communicationManager.changePassword({
            contrasena_actual: currentPassword.value, // Contraseña actual
            nueva_contrasena: newPassword.value,      // Nueva contraseña
          });
          alert("Contraseña actualizada correctamente");
        } catch (error) {
          console.error('Error cambiando la contraseña', error);
          alert('Error al cambiar la contraseña');
        }
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
        showEditProfile,
        activeTab,
        updateProfile,
        updatePassword,
        uploadImage,
      };
    },
  };
  </script>
  
  <style scoped>
  .profile-container {
    max-width: 400px;
    margin: auto;
    padding: 20px;
    text-align: center;
  }
  .profile-header {
    position: relative;
  }
  .profile-picture img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    cursor: pointer;
  }
  .edit-sections {
    margin-top: 20px;
  }
  .profile-info {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  button {
    margin-top: 10px;
    padding: 10px;
    background: #007bff;
    color: white;
    border: none;
    cursor: pointer;
    border-radius: 5px;
  }
  </style>
  