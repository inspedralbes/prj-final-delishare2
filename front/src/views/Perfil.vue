<template>
  <div class="profile-container">
    <div v-if="authStore.isAuthenticated">
      <!-- Popup de notificación -->
      <div v-if="popupMessage" class="popup-notification">
        {{ popupMessage }}
      </div>

      <!-- Header de perfil -->
      <div class="profile-header">
        <button @click="toggleSettingsMenu" class="settings-btn">
          <img :src="settingsIcon" alt="Ajustes" class="settings-icon" />
        </button>

        <!-- Icono SVG para crear nuevo live -->
        <div v-if="isChef && !settingsMenuOpen" class="web-button-container">
          <button @click="handleLiveButtonClick" class="web-button" style="background: none; border: none;">
            <svg width="35px" height="25px" viewBox="0 0 72 72" xmlns="http://www.w3.org/2000/svg" fill="black"
              stroke="black" stroke-width="1">
              <path d="M67.128,30.823C67.128,13.828,53.305,0,36.307,0c-2.862,0-5.617,0.423-8.247,1.157l-0.062-0.042 
              c-0.014,0.021-0.025,0.044-0.04,0.065C15.013,4.831,5.484,16.723,5.484,30.821c0,6.211,1.862,11.991,5.033,16.831H6.934v24.96 
              h58.748v-24.96h-3.581C65.275,42.816,67.128,37.034,67.128,30.823z M64.062,29.332H52.953c-0.109-3.312-0.483-6.568-1.149-9.729 
              h9.952C63.086,22.601,63.882,25.886,64.062,29.332z M60.191,16.605h-9.125c-1.115-4.104-2.68-8.033-4.724-11.715 
              C52.159,7.155,57.022,11.31,60.191,16.605z M37.805,3.069c1.45,0.078,2.876,0.238,4.264,0.533c2.586,4.01,4.575,8.378,5.912,13.005 
              H37.805V3.069z M37.805,19.603h10.946c0.708,3.156,1.1,6.409,1.219,9.729H37.807v-9.729z M37.805,32.318h12.189 
              c-0.062,3.312-0.405,6.569-1.062,9.731H37.805V32.318z M37.805,45.042h10.406c-0.244,0.881-0.522,1.747-0.81,2.614h-9.597V45.042z 
              M29.902,3.763c1.589-0.373,3.229-0.606,4.91-0.695v13.538H24.406C25.635,12.062,27.48,7.743,29.902,3.763z M22.62,29.332 
              c0.062-3.31,0.408-6.568,1.063-9.729h11.129v9.729H22.62z M34.812,32.318v9.731H23.864c-0.705-3.157-1.097-6.413-1.215-9.731 
              H34.812z M25.705,5.106c-1.899,3.627-3.359,7.486-4.379,11.503h-8.904C15.488,11.483,20.134,7.407,25.705,5.106z M10.858,19.603 
              h9.787c-0.619,3.169-0.958,6.423-1.021,9.729H8.549C8.735,25.886,9.53,22.601,10.858,19.603z M8.551,32.318H19.66 
              c0.11,3.314,0.484,6.569,1.152,9.731H10.86C9.53,39.048,8.735,35.769,8.551,32.318z M21.55,45.042 
              c0.241,0.881,0.509,1.747,0.792,2.614h-8.12c-0.64-0.84-1.252-1.704-1.798-2.614H21.55z M25,66.822v1.455H13.5V51.988h1.696h1.705 
              v13.388H25V66.822z M24.635,45.042H34.81v2.614h-9.317C25.191,46.789,24.889,45.928,24.635,45.042z M29.801,68.277h-1.696H26.4 
              V51.988h1.696h1.705V68.277z M39.757,68.277h-1.616h-1.617l-5.517-16.289h1.806h1.808l3.579,12.427l3.567-12.427h1.747h1.762 
              L39.757,68.277z M59.114,66.822v1.455H46.756V51.988H58.74v1.437v1.456h-8.586v3.455h7.963v1.396v1.405h-7.963v4.242h8.965v1.442 
              H59.114z M58.396,47.656h-7.849c0.262-0.867,0.516-1.733,0.741-2.614h8.903C59.646,45.955,59.034,46.816,58.396,47.656z 
              M51.968,42.05c0.618-3.167,0.956-6.424,1.02-9.731h11.074c-0.18,3.448-0.976,6.727-2.306,9.731H51.968z" />
            </svg>
          </button>
        </div>

        <label for="file-input" class="profile-picture">
          <img :src="user.img || defaultProfile" alt="Foto de perfil" />
        </label>
        <h2>{{ user.name }}</h2>
        <p class="user-bio">{{ user.bio || 'No hay biografia disponible' }}</p>
      </div>

      <!-- Menú de ajustes -->
      <div v-if="settingsMenuOpen" class="settings-overlay" @click="toggleSettingsMenu">
        <div class="settings-menu" @click.stop>
          <button @click="setActiveTab('image')">Canviar imatge de perfil</button>
          <button @click="setActiveTab('name')">Canviar nom d'usuari i email</button>
          <button @click="setActiveTab('password')">Canviar contrasenya</button>
          <button @click="setActiveTab('liked')">Veure receptes que m'agraden</button>
          <div v-if="isAdmin" class="admin-button-container">
  <button @click="goToAdmin" class="admin-button">Administración</button>
</div>

          <button @click="confirmLogout('logOut')">Tancar sessió</button>
        </div>
      </div>

      <!-- Confirmar logout -->
      <div v-if="showLogoutConfirmation" class="popup-confirmation">
        <p>Estàs segur que vols tancar la sessió?</p>
        <button @click="logout" class="confirm-btn">Si</button>
        <button @click="cancelLogout" class="cancel-btn">Cancelar</button>
      </div>

      <!-- Formularios de ajustes -->
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

      <div v-if="activeTab === 'password'" class="settings-form" style="margin-top: 70px;">
        <label for="currentPassword">Contrasenya actual:</label>
        <div class="password-input-container">
          <input :type="showCurrentPassword ? 'text' : 'password'" id="currentPassword" v-model="currentPassword" />
          <img :src="showCurrentPassword ? eyeOpenIcon : eyeClosedIcon" class="password-toggle-icon"
            @click="togglePasswordVisibility('current')" />
        </div>

        <label for="newPassword">Nova contrasenya:</label>
        <div class="password-input-container">
          <input :type="showNewPassword ? 'text' : 'password'" id="newPassword" v-model="newPassword" />
          <img :src="showNewPassword ? eyeOpenIcon : eyeClosedIcon" class="password-toggle-icon"
            @click="togglePasswordVisibility('new')" />
        </div>

        <label for="confirmPassword">Confirmar nova contrasenya:</label>
        <div class="password-input-container">
          <input :type="showConfirmPassword ? 'text' : 'password'" id="confirmPassword" v-model="confirmPassword" />
          <img :src="showConfirmPassword ? eyeOpenIcon : eyeClosedIcon" class="password-toggle-icon"
            @click="togglePasswordVisibility('confirm')" />
        </div>

        <button @click="updatePassword" class="submit-btn">Guardar canvis</button>
        <button @click="cancelEdit" class="cancel-btn">Cancelar</button>
      </div>

      <div v-if="activeTab === 'liked'" class="user-recipes liked-section">
        <h3 class="section-title">🍽️ Receptes que m'agraden</h3>
        <div v-if="likedRecipes.length === 0" class="no-liked-recipes">
          <p>No has donat like a cap recepta encara.</p>
        </div>
        <div v-else class="recipe-cards">
          <div v-for="recipe in likedRecipes" :key="recipe.id" class="liked-recipe-card">
            <RecipeCard :recipe-id="recipe.id" :title="recipe.title" :description="recipe.description"
              :image="recipe.image" />
          </div>
        </div>
        <div class="button-container">
          <button @click="cancelEdit" class="cancel-btn">🔙 Tornar</button>
        </div>
      </div>


      <!-- Formulario editar live -->
      <div v-if="showEditForm" class="live-form-overlay" @click="showEditForm = false">
        <div class="live-form-container" @click.stop>
          <h3>Editar Live</h3>
          <div class="form-group">
            <label for="edit-recipe">Receta:</label>
            <select id="edit-recipe" v-model="editingLive.recipe_id">
              <option v-for="recipe in recipes" :key="recipe.id" :value="recipe.id">{{ recipe.title }}</option>
            </select>
          </div>
          <div class="form-group">
            <label for="edit-date">Fecha:</label>
            <input type="date" id="edit-date" v-model="editingLive.dia" :min="minDate">
          </div>
          <div class="form-group">
            <label for="edit-time">Hora:</label>
            <input type="time" id="edit-time" v-model="editingLive.hora">
          </div>
          <div class="form-actions">
            <button @click="saveEditedLive" class="submit-btn">💾 Guardar canvis</button>
            <button @click="() => { showEditForm = false; editingLive = null }" class="cancel-btn">Cancelar</button>
          </div>
        </div>
      </div>

      <!-- Crear nuevo live -->
      <div v-if="showLiveForm" class="live-form-overlay" @click="toggleLiveForm">
        <div class="live-form-container" @click.stop>
          <h3>Programar Nuevo Live</h3>
          <div class="form-group">
            <label for="recipe">Receta:</label>
            <select id="recipe" v-model="newLive.recipe_id" required>
              <option value="">Selecciona una receta</option>
              <option v-for="recipe in recipes" :key="recipe.id" :value="recipe.id">{{ recipe.title }}</option>
            </select>
          </div>
          <div class="form-group">
            <label for="date">Fecha:</label>
            <input type="date" id="date" v-model="newLive.dia" :min="minDate" required>
          </div>
          <div class="form-group">
            <label for="time">Hora:</label>
            <input type="time" id="time" v-model="newLive.hora" required>
          </div>
          <div class="form-actions">
            <button @click="createLive" class="submit-btn">Programar Live</button>
            <button @click="toggleLiveForm" class="cancel-btn">Cancelar</button>
          </div>
        </div>
      </div>
      <!-- Botones para ver secciones -->
      <div v-if="isChef && !showLiveForm && !settingsMenuOpen && activeTab === ''" class="toggle-buttons">
        <button @click="toggleSection('recipes')" :class="{ active: showRecipes && !showLivesSection }">
          <svg width="25" height="25" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M14 5.6C14 5.03995 14 4.75992 14.109 4.54601C14.2049 4.35785 14.3578 4.20487 14.546 4.10899C14.7599 4 15.0399 4 15.6 4H18.4C18.9601 4 19.2401 4 19.454 4.10899C19.6422 4.20487 19.7951 4.35785 19.891 4.54601C20 4.75992 20 5.03995 20 5.6V8.4C20 8.96005 20 9.24008 19.891 9.45399C19.7951 9.64215 19.6422 9.79513 19.454 9.89101C19.2401 10 18.9601 10 18.4 10H15.6C15.0399 10 14.7599 10 14.546 9.89101C14.3578 9.79513 14.2049 9.64215 14.109 9.45399C14 9.24008 14 8.96005 14 8.4V5.6Z"
              stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            <path
              d="M4 5.6C4 5.03995 4 4.75992 4.10899 4.54601C4.20487 4.35785 4.35785 4.20487 4.54601 4.10899C4.75992 4 5.03995 4 5.6 4H8.4C8.96005 4 9.24008 4 9.45399 4.10899C9.64215 4.20487 9.79513 4.35785 9.89101 4.54601C10 4.75992 10 5.03995 10 5.6V8.4C10 8.96005 10 9.24008 9.89101 9.45399C9.79513 9.64215 9.64215 9.79513 9.45399 9.89101C9.24008 10 8.96005 10 8.4 10H5.6C5.03995 10 4.75992 10 4.54601 9.89101C4.35785 9.79513 4.20487 9.64215 4.10899 9.45399C4 9.24008 4 8.96005 4 8.4V5.6Z"
              stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            <path
              d="M4 15.6C4 15.0399 4 14.7599 4.10899 14.546C4.20487 14.3578 4.35785 14.2049 4.54601 14.109C4.75992 14 5.03995 14 5.6 14H8.4C8.96005 14 9.24008 14 9.45399 14.109C9.64215 14.2049 9.79513 14.3578 9.89101 14.546C10 14.7599 10 15.0399 10 15.6V18.4C10 18.9601 10 19.2401 9.89101 19.454C9.79513 19.6422 9.64215 19.7951 9.45399 19.891C9.24008 20 8.96005 20 8.4 20H5.6C5.03995 20 4.75992 20 4.54601 19.891C4.35785 19.7951 4.20487 19.6422 4.10899 19.454C4 19.2401 4 18.9601 4 18.4V15.6Z"
              stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            <path
              d="M14 15.6C14 15.0399 14 14.7599 14.109 14.546C14.2049 14.2049 14.3578 14.2049 14.546 14.1089C14.7599 14 15.0399 14 15.6 14H18.4C18.9601 14 19.2401 14 19.454 14.1089C19.6422 14.2049 19.7951 14.3578 19.891 14.546C20 14.7599 20 15.0399 20 15.6V18.4C20 18.9601 20 19.2401 19.891 19.4539C19.7951 19.6422 19.6422 19.7951 19.454 19.891C19.2401 20 18.9601 20 18.4 20H15.6C15.0399 20 14.7599 20 14.546 19.891C14.3578 19.7951 14.2049 19.6422 14.109 19.4539C14 19.2401 14 18.9601 14 18.4V15.6Z"
              stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
          </svg>
        </button>

        <button @click="toggleSection('lives')" :class="{ active: showLivesSection }">
          <svg width="25" height="25" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
            <g id="SVGRepo_iconCarrier">
              <path
                d="M22 12C22 14.7578 20.8836 17.2549 19.0782 19.064M2 12C2 9.235 3.12222 6.73208 4.93603 4.92188M19.1414 5.00003C19.987 5.86254 20.6775 6.87757 21.1679 8.00003M5 19.1415C4.08988 18.2493 3.34958 17.1845 2.83209 16"
                stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
              <path
                d="M16.2849 8.04397C17.3458 9.05877 18 10.4488 18 11.9822C18 13.5338 17.3302 14.9386 16.2469 15.9564M7.8 16C6.68918 14.9789 6 13.556 6 11.9822C6 10.4266 6.67333 9.01843 7.76162 8"
                stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
              <path
                d="M13.6563 10.4511C14.5521 11.1088 15 11.4376 15 12C15 12.5624 14.5521 12.8912 13.6563 13.5489C13.4091 13.7304 13.1638 13.9014 12.9384 14.0438C12.7407 14.1688 12.5168 14.298 12.2849 14.4249C11.3913 14.914 10.9444 15.1586 10.5437 14.8878C10.1429 14.617 10.1065 14.0502 10.0337 12.9166C10.0131 12.596 10 12.2817 10 12C10 11.7183 10.0131 11.404 10.0337 11.0834C10.1065 9.94977 10.1429 9.38296 10.5437 9.1122C10.9444 8.84144 11.3913 9.08599 12.2849 9.57509C12.5168 9.70198 12.7407 9.83123 12.9384 9.95619C13.1638 10.0986 13.4091 10.2696 13.6563 10.4511Z"
                stroke="#000000" stroke-width="1.5"></path>
            </g>
          </svg>
        </button>
      </div>

      <!-- Lives programados -->
      <div v-if="showLivesSection && !showLiveForm" class="user-recipes">
    
        <h3 class="section-title">📅 Mis lives programados</h3>
        <div v-if="scheduledLives.length === 0" class="no-lives">
          <p>No tienes ningún live programado todavía.</p>
        </div>
        <div v-else class="live-cards">
          <div v-for="live in scheduledLives" :key="live.id" class="live-card">
            <div class="live-info">
              <h4>{{ live.recipe.title }}</h4>
              <p>📅 {{ formatDate(live.dia) }} 🕒 {{ live.hora }}</p>
              <div class="live-actions">
                <button @click="editLive(live)" class="edit-btn">✏️ Editar</button>
                <button @click="deleteLive(live.id)" class="delete-btn">🗑️ Eliminar</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recetas propias -->
      <div v-if="showRecipes && !showLiveForm && !showLivesSection" class="user-recipes">
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

    <!-- No autenticado -->
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
import { ref, onMounted, computed } from 'vue';
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
    const goToAdmin = () => {
  router.push('/recetas');
};
    const minDate = computed(() => {
      const today = new Date();
      const dd = String(today.getDate()).padStart(2, '0');
      const mm = String(today.getMonth() + 1).padStart(2, '0');
      const yyyy = today.getFullYear();
      return `${yyyy}-${mm}-${dd}`;
    });
    const scheduledLives = ref([]);
    const showLivesSection = ref(false);

    const isChef = computed(() => {
      const role = authStore.user?.role || user.value?.role;
      return authStore.isAuthenticated && role === 'chef';
    });

    const isAdmin = computed(() => {
  const role = authStore.user?.role || user.value?.role;
  if (!role) {
    console.log("El rol no está definido.");
  }
  return authStore.isAuthenticated && role === 'admin';
});


    const newLive = ref({
      recipe_id: '',
      dia: '',
      hora: ''
    });

    const editingLive = ref(null);
    const showEditForm = ref(false);
    const showLiveForm = ref(false);
    const cloudName = 'dt5vjbgab';
    const uploadPreset = 'perfiles';
    const userImage = ref('/default-avatar.png');
    const likedRecipes = ref([]);
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

    // Manejar clic en el botón de live
    const handleLiveButtonClick = () => {
      showLivesSection.value = false;
      showRecipes.value = false;
      toggleLiveForm();
    };

    function toggleSection(section) {
      if (section === 'recipes') {
        showRecipes.value = true;
        showLivesSection.value = false;
      } else if (section === 'lives') {
        showRecipes.value = false;
        showLivesSection.value = true;
        loadScheduledLives(); // Añadir esta línea para cargar los lives al cambiar a esta sección
      }
    }
    const toggleSettingsMenu = () => {
  settingsMenuOpen.value = !settingsMenuOpen.value;
};

    // Mostrar la sección de lives programados
    const showScheduledLives = async () => {
      showRecipes.value = false;
      showLivesSection.value = true;
      showLiveForm.value = false;
      await loadScheduledLives();
    };

    // Ocultar la sección de lives programados
    const hideLivesSection = () => {
      showLivesSection.value = false;
      showRecipes.value = true;
      showLiveForm.value = false;
    };

    const toggleLiveForm = () => {
      showLiveForm.value = !showLiveForm.value;
      if (showLiveForm.value) {
        showLivesSection.value = false;
        showRecipes.value = false;
        newLive.value = {
          recipe_id: '',
          dia: '',
          hora: ''
        };
      } else {
        showRecipes.value = true;
      }
    };

    const goToLogin = () => {
      router.push({
        name: 'login',
        query: { redirect: router.currentRoute.value.fullPath }
      });
    };

    const loadScheduledLives = async () => {
      try {
        if (!isChef.value) return;

        const response = await communicationManager.getChefLives();
        scheduledLives.value = response.lives;
      } catch (error) {
        popupMessage.value = error.message || "No s'han pogut carregar els lives";
        setTimeout(() => { popupMessage.value = ''; }, 3000);
      }
    };

    const formatDate = (dateString) => {
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(dateString).toLocaleDateString('ca-ES', options);
    };

    const deleteLive = async (liveId) => {
      try {
        await communicationManager.deleteLive(liveId);
        scheduledLives.value = scheduledLives.value.filter(live => live.id !== liveId);
        popupMessage.value = "Live eliminat correctament";
        setTimeout(() => { popupMessage.value = ''; }, 3000);
      } catch (error) {
        popupMessage.value = error.message || "Error en eliminar el live";
        setTimeout(() => { popupMessage.value = ''; }, 3000);
      }
    };

    const editLive = (live) => {
      editingLive.value = { ...live };
      showEditForm.value = true;
    };

    const saveEditedLive = async () => {
      try {
        if (!editingLive.value || !editingLive.value.id) return;

        const { id, hora, dia, recipe_id } = editingLive.value;

        await communicationManager.updateLive(id, { hora, dia, recipe_id });
        popupMessage.value = 'Live actualitzat correctament';
        showEditForm.value = false;
        editingLive.value = null;
        await loadScheduledLives();
      } catch (error) {
        popupMessage.value = error.message || 'Error al actualitzar el live';
      } finally {
        setTimeout(() => { popupMessage.value = ''; }, 3000);
      }
    };


    const createLive = async () => {
      try {
        if (!newLive.value.recipe_id || !newLive.value.dia || !newLive.value.hora) {
          popupMessage.value = 'Por favor, completa todos los campos';
          setTimeout(() => { popupMessage.value = ''; }, 3000);
          return;
        }

        await communicationManager.createLive({
          recipe_id: newLive.value.recipe_id,
          dia: newLive.value.dia,
          hora: newLive.value.hora
        });

        popupMessage.value = 'Live programado correctamente';
        setTimeout(() => { popupMessage.value = ''; }, 3000);

        toggleLiveForm();
        await loadScheduledLives();
        showScheduledLives(); // Mostrar la sección de lives después de crear uno nuevo

      } catch (error) {
        console.error('Error al crear el live:', error);
        const errorMessage = error.response?.data?.message ||
          error.response?.data?.error ||
          error.message ||
          'Error al programar el live';
        popupMessage.value = errorMessage;
        setTimeout(() => { popupMessage.value = ''; }, 3000);
      }
    };

    // También asegúrate de cargar los lives programados en el onMounted:
    onMounted(async () => {
      try {
        const userData = await communicationManager.getUser();
        user.value = userData;
        newName.value = userData.name;
        newEmail.value = userData.email;
        newBio.value = userData.bio || '';
        const userRecipes = await communicationManager.getUserRecipes(userData.id);
        recipes.value = userRecipes.recipes;

        // Añadir esta línea para cargar los lives al montar el componente
        if (isChef.value) {
          await loadScheduledLives();
        }
      } catch (error) {
        console.error('Error obteniendo datos del usuario:', error);
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

    const loadLikedRecipes = async () => {
      try {
        const response = await communicationManager.getUserLikedRecipes();
        likedRecipes.value = response.recipes;
      } catch (error) {
        popupMessage.value = error.message || "No s'han pogut carregar les receptes likeades";
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
    const setActiveTab = (tab) => {
      activeTab.value = tab;
      showRecipes.value = false;
      showLivesSection.value = false;

      if (tab === 'liked') {
        loadLikedRecipes();
      }
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
      showLivesSection.value = false;
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
      authStore,
      toggleSection,
      router,
      handleLiveButtonClick,
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
      uploadImage,
      likedRecipes,
      loadLikedRecipes,
      showLiveForm,
      newLive,
      minDate,
      toggleLiveForm,
      isChef,
      isAdmin,
      goToAdmin,
      createLive,
      scheduledLives,
      loadScheduledLives,
      formatDate,
      deleteLive,
      editLive,
      editingLive,
      showEditForm,
      editLive,
      saveEditedLive,
      showLivesSection,
      showScheduledLives,
      hideLivesSection
    };
  }
};
</script>

<style scoped>
/* Nuevos estilos para el botón de ver lives */
.view-lives-button {
  margin: 20px 0;
  text-align: center;
}

.view-lives-btn {
  background-color: #4CAF50;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 5px;
  cursor: pointer;
  font-size: 16px;
  transition: background-color 0.3s;
}

.view-lives-btn:hover {
  background-color: #45a049;
}

/* Estilos para la sección de lives */
.live-cards {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
  margin: 20px 0;
}

.live-card {
  background: white;
  border-radius: 10px;
  padding: 15px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.live-info h4 {
  margin-top: 0;
  color: #0c0636;
}

.live-actions {
  display: flex;
  gap: 10px;
  margin-top: 10px;
}

.edit-btn {
  background-color: #2196F3;
  color: white;
  border: none;
  padding: 5px 10px;
  border-radius: 4px;
  cursor: pointer;
}

.delete-btn {
  background-color: #f44336;
  color: white;
  border: none;
  padding: 5px 10px;
  border-radius: 4px;
  cursor: pointer;
}

.no-lives {
  text-align: center;
  padding: 20px;
  color: #666;
}

.web-button-container {
  position: absolute;
  top: 10px;
  right: -23px;
  background: none;
  border: none;
  cursor: pointer;
}

/* Estilos modificados para el botón de programar live */
.program-live-btn-container {
  position: fixed;
  bottom: 80px;
  /* Aumentado para evitar superposición con el navbar */
  right: 30px;
  z-index: 950;
  /* Aumentado el z-index para asegurar visibilidad */
}

.program-live-btn {
  background: linear-gradient(135deg, #ff4757, #ff6b81);
  color: white;
  border: none;
  border-radius: 50px;
  padding: 14px 24px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  box-shadow: 0 6px 16px rgba(255, 71, 87, 0.4);
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 10px;
  transform: translateY(0);
}

.program-live-btn:hover {
  background: linear-gradient(135deg, #ff6b81, #ff4757);
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(255, 71, 87, 0.5);
}

.program-live-btn:active {
  transform: translateY(1px);
  box-shadow: 0 4px 12px rgba(255, 71, 87, 0.4);
}

.live-icon {
  font-size: 20px;
  display: inline-block;
  animation: pulse 2s infinite;
}

.btn-text {
  letter-spacing: 0.5px;
}

@keyframes pulse {
  0% {
    transform: scale(1);
  }

  50% {
    transform: scale(1.1);
  }

  100% {
    transform: scale(1);
  }
}


@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Aseguramos que el menú de configuración no tape el botón */
.settings-menu {
  max-height: 80vh;
  overflow-y: auto;
}

@media screen and (max-width: 768px) {
  .program-live-btn-container {
    bottom: 100px;
    /* Más espacio en móviles para evitar el navbar */
    right: 20px;
  }

  .program-live-btn {
    padding: 12px 20px;
  }
}

/* También mejoramos el formulario de live para que combine mejor */
.live-form-container {
  background: white;
  padding: 35px;
  border-radius: 16px;
  width: 90%;
  max-width: 500px;
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
  animation: slideUp 0.4s ease-out;
}

.live-form-container h3 {
  margin-top: 0;
  color: #ff4757;
  text-align: center;
  margin-bottom: 25px;
  font-size: 24px;
  font-weight: 600;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 500;
  color: #333;
}

.form-group select,
.form-group input {
  width: 100%;
  padding: 12px;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 16px;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 25px;
}

.form-actions button {
  padding: 10px 20px;
  border-radius: 8px;
  font-size: 16px;
  cursor: pointer;
}

.submit-btn {
  background-color: #0c0636;
  color: white;
  border: none;
}

.submit-btn:hover {
  background-color: #322b5f;
}

.cancel-btn {
  background-color: #f8f9fa;
  color: #333;
  border: 1px solid #ddd;
}

.cancel-btn:hover {
  background-color: #e9ecef;
}

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

.web-button {
  width: 60px;
  height: 60px;
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

.liked-section {
  margin-top: 60px;
}

.section-title {
  font-size: 1.6rem;
  color: #030127;
  margin-bottom: 20px;
  text-align: center;
}

.no-liked-recipes {
  text-align: center;
  font-size: 1.1rem;
  color: #666;
  padding: 30px 0;
}

.liked-recipe-card {
  width: 100%;
  max-width: 350px;
}

.button-container {
  display: flex;
  justify-content: center;
  margin-top: 20px;
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

.toggle-buttons button {
  margin-right: 10px;
  padding: 8px 12px;
  border: none;
  cursor: pointer;
  background-color: #ffffff;
  border-radius: 5px;
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