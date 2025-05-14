<template>
  <div class="profile-container">
    <div v-if="authStore.isAuthenticated">
      <!-- Popup de notificación -->
      <div v-if="popupMessage" class="popup-notification">
        {{ popupMessage }}
      </div>

      <!-- Crear nueva carpeta -->
      <div v-if="showCreateFolderInput" class="live-form-overlay fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4" @click="showCreateFolderInput = false">
        <div class="live-form-container w-full max-w-[95%] sm:max-w-sm" @click.stop>
          <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 w-full">
            <h3 class="text-lg sm:text-xl font-bold text-lime-900 mb-3 sm:mb-4 text-center">Crear Nueva Carpeta</h3>
            <div class="space-y-3 sm:space-y-4">
              <div class="form-group">
                <label for="folder-name" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Nombre de la carpeta:</label>
                <input type="text" id="folder-name" v-model="newFolderName" placeholder="Nombre de la carpeta" @keyup.enter="createFolder"
                  ref="folderNameInput" autofocus class="w-full px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-700 bg-white border-2 border-lime-200 rounded-lg focus:outline-none focus:border-lime-400 focus:ring-2 focus:ring-lime-200 transition-all duration-300" />
              </div>
              <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 pt-2 sm:pt-3">
                <button @click="createFolder" 
                  class="w-full sm:flex-1 px-3 sm:px-4 py-1.5 sm:py-2 bg-gradient-to-r from-lime-400 to-green-500 text-white font-medium rounded-lg shadow-md hover:from-lime-500 hover:to-green-600 focus:outline-none focus:ring-2 focus:ring-lime-300 focus:ring-opacity-50 transition-all duration-300 transform hover:scale-105 text-xs sm:text-sm">
                  <span class="flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Crear Carpeta
                  </span>
                </button>
                <button @click="showCreateFolderInput = false" 
                  class="w-full sm:flex-1 px-3 sm:px-4 py-1.5 sm:py-2 bg-gray-100 text-gray-700 font-medium rounded-lg shadow-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-opacity-50 transition-all duration-300 text-xs sm:text-sm">
                  Cancelar
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Header de perfil -->
      <div class="relative mb-10 rounded-2xl bg-gradient-to-br from-lime-100 via-lime-200 to-green-200 p-8 shadow-lg">
        <button @click="toggleSettingsMenu" class="absolute right-2.5 top-2.5 z-10 rounded-full p-2.5 transition-all hover:bg-white/30">
          <img :src="settingsIcon" alt="Ajustes" class="h-6 w-6" />
        </button>

        <!-- Botón de crear carpeta -->
        <button v-if="!showCreateFolderInput" @click="handleCreateFolderClick" 
          class="absolute right-2.5 top-16 z-10 rounded-full p-2.5 transition-all hover:bg-white/30">
          <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
            <g id="SVGRepo_iconCarrier">
              <path d="M9 13H15M12 10V16M12.0627 6.06274L11.9373 5.93726C11.5914 5.59135 11.4184 5.4184 11.2166 5.29472C11.0376 5.18506 10.8425 5.10425 10.6385 5.05526C10.4083 5 10.1637 5 9.67452 5H6.2C5.0799 5 4.51984 5 4.09202 5.21799C3.71569 5.40973 3.40973 5.71569 3.21799 6.09202C3 6.51984 3 7.07989 3 8.2V15.8C3 16.9201 3 17.4802 3.21799 17.908C3.40973 18.2843 3.71569 18.5903 4.09202 18.782C4.51984 19 5.07989 19 6.2 19H17.8C18.9201 19 19.4802 19 19.908 18.782C20.2843 18.5903 20.5903 18.2843 20.782 17.908C21 17.4802 21 16.9201 21 15.8V10.2C21 9.0799 21 8.51984 20.782 8.09202C20.5903 7.71569 20.2843 7.40973 19.908 7.21799C19.4802 7 18.9201 7 17.8 7H14.3255C13.8363 7 13.5917 7 13.3615 6.94474C13.1575 6.89575 12.9624 6.81494 12.7834 6.70528C12.5816 6.5816 12.4086 6.40865 12.0627 6.06274Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </g>
          </svg>
        </button>

        <!-- Icono SVG para crear nuevo live -->
        <div v-if="isChef && !settingsMenuOpen" class="absolute left-2.5 top-2.5 z-10">
          <button @click="handleLiveButtonClick" class="transition-transform hover:scale-110">
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

        <div class="flex items-start gap-6">
          <div class="flex-shrink-0">
            <label for="file-input" class="block cursor-pointer">
              <div class="relative h-24 w-24 overflow-hidden rounded-full border-4 border-white/80 shadow-lg transition-all hover:scale-105 hover:shadow-xl">
                <img :src="user.img || defaultProfile" alt="Foto de perfil" class="h-full w-full object-cover" />
              </div>
            </label>
          </div>
          <div class="flex-grow">
            <h2 class="mb-3 text-3xl font-bold text-lime-900">{{ user.name }}</h2>
            <p class="rounded-xl bg-white/70 p-3 text-base text-lime-700 backdrop-blur-sm">
              {{ user.bio || 'No hay biografia disponible' }}
            </p>
          </div>
        </div>
      </div>

      <!-- Menú de ajustes -->
      <div v-if="settingsMenuOpen" class="settings-overlay" @click="toggleSettingsMenu">
        <div class="settings-menu" @click.stop>
          <button @click="setActiveTab('image')">Canviar imatge de perfil</button>
          <button @click="setActiveTab('name')">Canviar nom d'usuari i email</button>
          <button @click="setActiveTab('password')">Canviar contrasenya</button>
          <div v-if="isAdmin" class="admin-button-container">
            <button @click="goToAdmin" class="admin-button">Administración</button>
          </div>
          <!-- Dentro del settings-menu, después del botón de administración -->
<div v-if="!isChef && !isAdmin" class="verification-button-container">
  <button @click="goToVerification" class="verification-button">Pedir Verificación</button>
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
      <div v-if="showLiveForm" class="live-form-overlay fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4" @click="toggleLiveForm">
        <div class="live-form-container w-full max-w-[95%] sm:max-w-sm" @click.stop>
          <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 w-full">
            <h3 class="text-lg sm:text-xl font-bold text-lime-900 mb-3 sm:mb-4 text-center">Programar Nuevo Live</h3>
            <div class="space-y-3 sm:space-y-4">
              <div class="form-group">
                <label for="recipe" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Receta:</label>
                <select id="recipe" v-model="newLive.recipe_id" required
                  class="w-full px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-700 bg-white border-2 border-lime-200 rounded-lg focus:outline-none focus:border-lime-400 focus:ring-2 focus:ring-lime-200 transition-all duration-300">
                  <option value="">Selecciona una receta</option>
                  <option v-for="recipe in recipes" :key="recipe.id" :value="recipe.id" class="py-1">{{ recipe.title }}</option>
                </select>
              </div>
              <div class="form-group">
                <label for="date" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Fecha:</label>
                <div class="relative">
                  <input type="date" id="date" v-model="newLive.dia" :min="minDate" required
                    class="w-full px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-700 bg-white border-2 border-lime-200 rounded-lg focus:outline-none focus:border-lime-400 focus:ring-2 focus:ring-lime-200 transition-all duration-300" />
                  <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="time" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Hora:</label>
                <div class="relative">
                  <input type="time" id="time" v-model="newLive.hora" required
                    class="w-full px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-700 bg-white border-2 border-lime-200 rounded-lg focus:outline-none focus:border-lime-400 focus:ring-2 focus:ring-lime-200 transition-all duration-300" />
                  <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                </div>
              </div>
              <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 pt-2 sm:pt-3">
                <button @click="createLive" 
                  class="w-full sm:flex-1 px-3 sm:px-4 py-1.5 sm:py-2 bg-gradient-to-r from-lime-400 to-green-500 text-white font-medium rounded-lg shadow-md hover:from-lime-500 hover:to-green-600 focus:outline-none focus:ring-2 focus:ring-lime-300 focus:ring-opacity-50 transition-all duration-300 transform hover:scale-105 text-xs sm:text-sm">
                  <span class="flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Programar Live
                  </span>
                </button>
                <button @click="toggleLiveForm" 
                  class="w-full sm:flex-1 px-3 sm:px-4 py-1.5 sm:py-2 bg-gray-100 text-gray-700 font-medium rounded-lg shadow-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-opacity-50 transition-all duration-300 text-xs sm:text-sm">
                  Cancelar
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Botones para ver secciones -->
      <div v-if="!showLiveForm && !settingsMenuOpen && activeTab === ''" class="flex justify-center gap-4 mt-8 mb-6 px-6 md:gap-2 md:mt-4 md:mb-2 md:px-2">
        <button @click="toggleSection('recipes')" :class="[
          'flex-1 py-2 px-2 text-sm rounded-full font-semibold transition-all duration-300',
          'md:py-4 md:px-4 md:text-base',
          showRecipes && !showLivesSection ? 'bg-gradient-to-r from-green-500 via-lime-400 to-lime-300 text-lime-900 shadow-lg hover:shadow-xl hover:brightness-110' : 'bg-gradient-to-r from-green-100 via-lime-50 to-lime-100 text-lime-700 hover:from-green-200 hover:via-lime-100 hover:to-lime-200 hover:shadow-md'
        ]">
          <svg width="25" height="25" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="inline-block">
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

        <button v-if="isChef" @click="toggleSection('lives')" :class="[
          'flex-1 py-2 px-2 text-sm rounded-full font-semibold transition-all duration-300',
          'md:py-4 md:px-4 md:text-base',
          showLivesSection ? 'bg-gradient-to-r from-green-500 via-lime-400 to-lime-300 text-lime-900 shadow-lg hover:shadow-xl hover:brightness-110' : 'bg-gradient-to-r from-green-100 via-lime-50 to-lime-100 text-lime-700 hover:from-green-200 hover:via-lime-100 hover:to-lime-200 hover:shadow-md'
        ]">
          <svg width="25" height="25" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="inline-block">
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

        <button @click="toggleSection('saved')" :class="[
          'flex-1 py-2 px-2 text-sm rounded-full font-semibold transition-all duration-300',
          'md:py-4 md:px-4 md:text-base',
          showSavedSection ? 'bg-gradient-to-r from-green-500 via-lime-400 to-lime-300 text-lime-900 shadow-lg hover:shadow-xl hover:brightness-110' : 'bg-gradient-to-r from-green-100 via-lime-50 to-lime-100 text-lime-700 hover:from-green-200 hover:via-lime-100 hover:to-lime-200 hover:shadow-md'
        ]">
          <svg width="25" height="25" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="inline-block">
            <path
              d="M5 6.2C5 5.07989 5 4.51984 5.21799 4.09202C5.40973 3.71569 5.71569 3.40973 6.09202 3.21799C6.51984 3 7.07989 3 8.2 3H15.8C16.9201 3 17.4802 3 17.908 3.21799C18.2843 3.40973 18.5903 3.71569 18.782 4.09202C19 4.51984 19 5.07989 19 6.2V21L12 16L5 21V6.2Z"
              stroke="#000000" stroke-width="2" stroke-linejoin="round" />
          </svg>
        </button>

        <button @click="toggleSection('folders')" :class="[
          'flex-1 py-2 px-2 text-sm rounded-full font-semibold transition-all duration-300',
          'md:py-4 md:px-4 md:text-base',
          showFoldersSection ? 'bg-gradient-to-r from-green-500 via-lime-400 to-lime-300 text-lime-900 shadow-lg hover:shadow-xl hover:brightness-110' : 'bg-gradient-to-r from-green-100 via-lime-50 to-lime-100 text-lime-700 hover:from-green-200 hover:via-lime-100 hover:to-lime-200 hover:shadow-md'
        ]">
          <svg width="25" height="25" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="inline-block">
            <path d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" 
                  stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>

        <button @click="toggleSection('liked')" :class="[
          'flex-1 py-2 px-2 text-sm rounded-full font-semibold transition-all duration-300',
          'md:py-4 md:px-4 md:text-base',
          showLikedSection ? 'bg-gradient-to-r from-green-500 via-lime-400 to-lime-300 text-lime-900 shadow-lg hover:shadow-xl hover:brightness-110' : 'bg-gradient-to-r from-green-100 via-lime-50 to-lime-100 text-lime-700 hover:from-green-200 hover:via-lime-100 hover:to-lime-200 hover:shadow-md'
        ]">
          <svg width="25" height="25" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="inline-block">
            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" 
                  stroke="#000000" stroke-width="2" stroke-linejoin="round"/>
          </svg>
        </button>
      </div>

      <!-- Sección de guardadas -->
      <div v-if="showSavedSection" class="user-recipes saved-section">
        <div v-if="popupMessage" class="popup-notification">
          {{ popupMessage }}
        </div>

        <div class="guardades-container">
          <div class="bg-white rounded-xl p-6 shadow-lg mb-8">
            <h3 class="text-2xl font-bold text-lime-900 mb-4">Mis Recetas Guardadas</h3>
            <p class="text-gray-600 mb-4">Aquí encontrarás todas las recetas que has guardado para ti. Estas recetas son privadas y solo tú puedes verlas.</p>
          </div>
          <div v-if="loadingGuardades" class="loading-container">
            <div class="loading-spinner"></div>
            <p>Cargando...</p>
          </div>
          <div v-else>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              <div v-for="recipe in savedRecipes" :key="recipe.id" class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="relative">
                  <img :src="recipe.image" :alt="recipe.title" class="w-full h-48 object-cover" />
                  <button @click="removeSavedRecipe(recipe.id)" class="absolute top-3 right-3 bg-white/90 p-2 rounded-full shadow-md hover:bg-red-100 transition-all duration-300">
                    <img :src="binIcon" alt="Eliminar" class="w-5 h-5" />
                  </button>
                </div>
                <div class="p-6">
                  <h4 class="text-xl font-semibold text-lime-900 mb-2 truncate">{{ recipe.title }}</h4>
                  <p class="text-gray-600 text-sm line-clamp-2">{{ recipe.description }}</p>
                </div>
              </div>
            </div>
            <div v-if="savedRecipes.length === 0" class="bg-white rounded-xl p-8 text-center shadow-lg max-w-2xl mx-auto">
              <p class="text-gray-600 text-lg mb-4">No tienes recetas guardadas todavía.</p>
              <button @click="goToExplore" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-lime-400 to-green-500 text-white font-medium rounded-lg shadow-md hover:from-lime-500 hover:to-green-600 focus:outline-none focus:ring-2 focus:ring-lime-300 focus:ring-opacity-50 transition-all duration-300 transform hover:scale-105">
                Explorar recetas
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Sección de carpetas -->
      <div v-if="showFoldersSection" class="user-recipes">
        <div v-if="selectedFolder" class="selected-folder-view">
          <h3 class="text-2xl font-bold text-lime-900 mb-8 text-center">{{ selectedFolder.name }}</h3>
          
          <div v-if="selectedFolderRecipes.length === 0" class="bg-white rounded-xl p-8 text-center shadow-lg max-w-2xl mx-auto">
            <p class="text-gray-600 text-lg mb-4">No hay recetas en esta carpeta.</p>
          </div>
          
          <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 px-4">
            <div v-for="recipe in selectedFolderRecipes" :key="recipe.id" class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
              <div class="relative">
                <img :src="recipe.image_url || recipe.image" :alt="recipe.title" class="w-full h-48 object-cover" />
                <button @click="removeRecipeFromFolder(recipe.id, selectedFolder.id)" class="absolute top-3 right-3 bg-white/90 p-2 rounded-full shadow-md hover:bg-red-100 transition-all duration-300">
                  <img :src="binIcon" alt="Eliminar" class="w-5 h-5" />
                </button>
              </div>
              <div class="p-6">
                <h4 class="text-xl font-semibold text-lime-900 mb-2 truncate">{{ recipe.title }}</h4>
                <p class="text-gray-600 text-sm line-clamp-2">{{ recipe.description }}</p>
              </div>
            </div>
          </div>
        </div>

        <div v-else>
          <div class="bg-white rounded-xl p-6 shadow-lg mb-8">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-2xl font-bold text-lime-900">Mis Carpetas Públicas</h3>
            </div>
            <p class="text-gray-600">Crea carpetas públicas para organizar y compartir tus recetas favoritas con otros usuarios. Estas carpetas serán visibles para todos.</p>
          </div>

          <div v-if="folders.length > 0" class="grid grid-cols-2 gap-3 max-w-md mx-auto">
            <div v-for="folder in folders" :key="folder.id" 
              @click="fetchFolderRecipes(folder.id)"
              class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 cursor-pointer aspect-square">
              <div class="relative h-full bg-gradient-to-br from-lime-100 to-green-100 flex items-center justify-center">
                <svg class="w-12 h-12 text-lime-500 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                </svg>
                <button @click.stop="deleteFolder(folder.id)" class="absolute top-1 right-1 bg-white/90 p-1 rounded-full shadow-md hover:bg-red-100 transition-all duration-300">
                  <img :src="binIcon" alt="Eliminar" class="w-3 h-3" />
                </button>
                <div class="absolute bottom-0 left-0 right-0 p-2 bg-white/90">
                  <h4 class="text-xs font-semibold text-lime-900 mb-0.5 truncate">{{ folder.name }}</h4>
                  <p class="text-gray-600 text-[10px]">Carpeta pública</p>
                </div>
              </div>
            </div>
          </div>

          <div v-if="folders.length === 0" class="bg-white rounded-xl p-8 text-center shadow-lg max-w-2xl mx-auto">
            <p class="text-gray-600 text-lg mb-4">No tienes carpetas creadas.</p>
            <p class="text-gray-500 text-sm mb-6">Crea carpetas para organizar y compartir tus recetas favoritas.</p>
          </div>
        </div>
      </div>

      <!-- Sección de recetas que me gustan -->
      <div v-if="showLikedSection" class="user-recipes liked-section">
        <h3 class="section-title">🍽️ Receptes que m'agraden</h3>
        <div v-if="likedRecipes.length === 0" class="no-liked-recipes">
          <p>No has donat like a cap recepta encara.</p>
          <button @click="goToExplore" class="explore-btn">Explorar receptes</button>
        </div>
        <div v-else class="recipe-cards">
          <div v-for="recipe in likedRecipes" :key="recipe.id" class="liked-recipe-card">
            <RecipeCard 
              :recipe-id="recipe.id" 
              :title="recipe.title" 
              :description="recipe.description"
              :image="recipe.image" 
            />
          </div>
        </div>
        <div class="button-container">
          <button @click="cancelEdit" class="cancel-btn">🔙 Tornar</button>
        </div>
      </div>

      <!-- Lives programados -->
      <div v-if="showLivesSection && !showLiveForm" class="user-recipes">
        <h3 class="text-2xl font-bold text-lime-900 mb-8 text-center"> Mis lives programados</h3>
        <div v-if="scheduledLives.length === 0" class="bg-white rounded-xl p-8 text-center shadow-lg max-w-2xl mx-auto">
          <p class="text-gray-600 text-lg mb-4">No tienes ningún live programado todavía.</p>
          <button @click="toggleLiveForm" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-lime-400 to-green-500 text-white font-medium rounded-lg shadow-md hover:from-lime-500 hover:to-green-600 focus:outline-none focus:ring-2 focus:ring-lime-300 focus:ring-opacity-50 transition-all duration-300 transform hover:scale-105">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Programar nuevo live
          </button>
        </div>
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 px-4">
          <div v-for="live in scheduledLives" :key="live.id" class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <h4 class="text-xl font-semibold text-lime-900 truncate">{{ live.recipe.title }}</h4>
                <span class="px-3 py-1 bg-lime-100 text-lime-800 rounded-full text-sm font-medium">Live</span>
              </div>
              <div class="space-y-3">
                <div class="flex items-center text-gray-600">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                  <span>{{ formatDate(live.dia) }}</span>
                </div>
                <div class="flex items-center text-gray-600">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <span>{{ live.hora }}</span>
                </div>
              </div>
              <div class="mt-6 flex space-x-3">
                <button @click="editLive(live)" class="flex-1 px-4 py-2 bg-gradient-to-r from-lime-400 to-green-500 text-white font-medium rounded-lg shadow-sm hover:from-lime-500 hover:to-green-600 focus:outline-none focus:ring-2 focus:ring-lime-300 focus:ring-opacity-50 transition-all duration-300">
                  <span class="flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Editar
                  </span>
                </button>
                <button @click="deleteLive(live.id)" class="flex-1 px-4 py-2 bg-red-100 text-red-600 font-medium rounded-lg shadow-sm hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-300 focus:ring-opacity-50 transition-all duration-300">
                  <span class="flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Eliminar
                  </span>
                </button>
              </div>
            </div>
          </div>
        </div>
        <div class="mt-8 text-center">
          <button @click="toggleLiveForm" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-lime-400 to-green-500 text-white font-medium rounded-lg shadow-md hover:from-lime-500 hover:to-green-600 focus:outline-none focus:ring-2 focus:ring-lime-300 focus:ring-opacity-50 transition-all duration-300 transform hover:scale-105">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Programar nuevo live
          </button>
        </div>
      </div>

      <!-- Recetas propias -->
      <div v-if="showRecipes && !showLiveForm && !showLivesSection" class="user-recipes">
        <h3 class="text-2xl font-bold text-lime-900 mb-8 text-center"> Mis Publicaciones</h3>
        <div v-if="recipes.length === 0" class="bg-white rounded-xl p-8 text-center shadow-lg max-w-2xl mx-auto">
          <p class="text-gray-600 text-lg mb-4">No tienes ninguna publicación todavía.</p>
        </div>
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 px-4">
          <div v-for="recipe in recipes" :key="recipe.id" class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="relative">
              <img :src="recipe.image" :alt="recipe.title" class="w-full h-48 object-cover" />
              <button @click="deleteRecipe(recipe.id)" class="absolute top-3 right-3 bg-white/90 p-2 rounded-full shadow-md hover:bg-red-100 transition-all duration-300">
                <img :src="binIcon" alt="Eliminar" class="w-5 h-5" />
              </button>
            </div>
            <div class="p-6">
              <h4 class="text-xl font-semibold text-lime-900 mb-2 truncate">{{ recipe.title }}</h4>
              <p class="text-gray-600 text-sm line-clamp-2">{{ recipe.description }}</p>
            </div>
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
    const authStore = useAuthStore();
    const router = useRouter();

    // Saved section variables
    const showSavedSection = ref(false);
    const showFoldersSection = ref(false);
    const loadingGuardades = ref(true);
    const savedRecipes = ref([]);
    const folders = ref([]);
    const showCreateFolderInput = ref(false);
    const newFolderName = ref('');
    const selectedFolder = ref(null);
    const selectedFolderRecipes = ref([]);

    // Profile variables
    const minDate = computed(() => {
      const today = new Date();
      const dd = String(today.getDate()).padStart(2, '0');
      const mm = String(today.getMonth() + 1).padStart(2, '0');
      const yyyy = today.getFullYear();
      return `${yyyy}-${mm}-${dd}`;
    });
    const scheduledLives = ref([]);
    const showLivesSection = ref(false);
    const showLikedSection = ref(false);
    const goToExplore = () => {
  router.push('/explorar');
};
    const newLive = ref({
      recipe_id: '',
      dia: '',
      hora: ''
    });
    const editingLive = ref(null);
    const showEditForm = ref(false);
    const showLiveForm = ref(false);
    const userImage = ref('/default-avatar.png');
    const likedRecipes = ref([]);
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

    const isChef = computed(() => {
      const role = authStore.user?.role || user.value?.role;
      return authStore.isAuthenticated && role === 'chef';
    });

    const isAdmin = computed(() => {
      const role = authStore.user?.role || user.value?.role;
      return authStore.isAuthenticated && role === 'admin';
    });

    // Saved section methods
    const fetchSavedRecipes = async () => {
      loadingGuardades.value = true;
      try {
        const response = await communicationManager.getSavedRecipes();
        savedRecipes.value = response;
      } catch (error) {
        console.error('Error al obtenir receptes guardades:', error);
        popupMessage.value = "Error al carregar receptes guardades";
        setTimeout(() => { popupMessage.value = ''; }, 3000);
      } finally {
        loadingGuardades.value = false;
      }
    };

    const removeSavedRecipe = async (recipeId) => {
      try {
        await communicationManager.toggleSaveRecipe(recipeId);
        savedRecipes.value = savedRecipes.value.filter(r => r.id !== recipeId);
        popupMessage.value = "Recepta eliminada de guardades";
        setTimeout(() => { popupMessage.value = ''; }, 3000);
      } catch (error) {
        console.error('Error en eliminar la recepta:', error);
        popupMessage.value = "Error en eliminar la recepta";
        setTimeout(() => { popupMessage.value = ''; }, 3000);
      }
    };

    const fetchUserFolders = async () => {
      try {
        const userFolders = await communicationManager.fetchUserFolders();
        folders.value = userFolders;
      } catch (error) {
        console.error('Error carregant carpetes', error);
        popupMessage.value = "Error al carregar carpetes";
        setTimeout(() => { popupMessage.value = ''; }, 3000);
      }
    };

    const fetchFolderRecipes = async (folderId) => {
      try {
        const recipesFromFolder = await communicationManager.fetchFolderRecipes(folderId);
        selectedFolderRecipes.value = recipesFromFolder;
        selectedFolder.value = folders.value.find(folder => folder.id === folderId);
      } catch (error) {
        console.error('Error obtenint receptes de la carpeta', error);
        popupMessage.value = "Error al carregar receptes de la carpeta";
        setTimeout(() => { popupMessage.value = ''; }, 3000);
      }
    };

    const createFolder = async () => {
      if (newFolderName.value.trim() === '') {
        popupMessage.value = "El nom de la carpeta no pot estar buit";
        setTimeout(() => { popupMessage.value = ''; }, 3000);
        return;
      }
      try {
        await communicationManager.createFolder(newFolderName.value);
        popupMessage.value = 'Carpeta creada correctament';
        setTimeout(() => { popupMessage.value = ''; }, 3000);
        showCreateFolderInput.value = false;
        newFolderName.value = '';
        await fetchUserFolders();
        showFoldersSection.value = true; // Mostrar la sección de carpetas después de crear
      } catch (error) {
        console.error('Error creant la carpeta', error);
        popupMessage.value = "Error en crear la carpeta";
        setTimeout(() => { popupMessage.value = ''; }, 3000);
      }
    };

    const deleteFolder = async (folderId) => {
      try {
        await communicationManager.deleteFolder(folderId);
        popupMessage.value = 'Carpeta eliminada correctament';
        setTimeout(() => { popupMessage.value = ''; }, 3000);
        await fetchUserFolders();
        if (selectedFolder.value && selectedFolder.value.id === folderId) {
          selectedFolder.value = null;
          selectedFolderRecipes.value = [];
        }
      } catch (error) {
        console.error('Error eliminant la carpeta', error);
        popupMessage.value = "Error en eliminar la carpeta";
        setTimeout(() => { popupMessage.value = ''; }, 3000);
      }
    };

    const removeRecipeFromFolder = async (recipeId, folderId) => {
      try {
        await communicationManager.removeRecipeFromFolder(recipeId, folderId);
        selectedFolderRecipes.value = selectedFolderRecipes.value.filter(
          recipe => recipe.id !== recipeId
        );
        popupMessage.value = 'Recepta eliminada de la carpeta';
        setTimeout(() => { popupMessage.value = ''; }, 3000);
      } catch (error) {
        console.error('Error eliminant la recepta de la carpeta', error);
        popupMessage.value = "Error en eliminar la recepta";
        setTimeout(() => { popupMessage.value = ''; }, 3000);
      }
    };

    const goBackFromFolder = () => {
      selectedFolder.value = null;
      selectedFolderRecipes.value = [];
    };
    const toggleSection = (section) => {
      // Resetear todas las secciones
      showRecipes.value = false;
      showLivesSection.value = false;
      showSavedSection.value = false;
      showLikedSection.value = false;
      showFoldersSection.value = false;

      // Resetear la carpeta seleccionada si se hace clic en el botón de carpetas
      if (section === 'folders') {
        selectedFolder.value = null;
        selectedFolderRecipes.value = [];
      }

      // Activar solo la sección seleccionada
      if (section === 'recipes') showRecipes.value = true;
      else if (section === 'lives') showLivesSection.value = true;
      else if (section === 'saved') showSavedSection.value = true;
      else if (section === 'liked') showLikedSection.value = true;
      else if (section === 'folders') showFoldersSection.value = true;

      // Cargar datos si es necesario
      if (section === 'lives') loadScheduledLives();
      if (section === 'saved') {
        fetchSavedRecipes();
      }
      if (section === 'folders') {
        fetchUserFolders();
      }
      if (section === 'liked') loadLikedRecipes();
    };

    // Modificar el botón de crear carpeta para que oculte las demás secciones
    const handleCreateFolderClick = () => {
      showCreateFolderInput.value = true;
      showRecipes.value = false;
      showLivesSection.value = false;
      showSavedSection.value = false;
      showLikedSection.value = false;
      showFoldersSection.value = false;
    };

    const handleLiveButtonClick = () => {
      showLivesSection.value = false;
      showRecipes.value = false;
      toggleLiveForm();
    };

    const toggleSettingsMenu = () => {
      settingsMenuOpen.value = !settingsMenuOpen.value;
    };

    const showScheduledLives = async () => {
      showRecipes.value = false;
      showLivesSection.value = true;
      showLiveForm.value = false;
      await loadScheduledLives();
    };

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
        if (response.success && Array.isArray(response.data)) {
          scheduledLives.value = response.data;
        } else {
          scheduledLives.value = [];
          console.warn('Unexpected response format from getChefLives:', response);
        }
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
        showScheduledLives();
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
      formData.append("upload_preset", 'perfiles');

      try {
        const response = await axios.put(
          `https://api.cloudinary.com/v1_1/dt5vjbgab/image/upload`,
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
    const goToVerification = () => {
  router.push('/verificacion');
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
      showSavedSection.value = false;
      showLikedSection.value = false;
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

    const goToAdmin = () => {
      router.push('/recetas');
    };

    const goToRecipe = (recipeId) => {
      router.push({
        name: 'inforeceta',
        params: { id: recipeId }
      });
    };

    onMounted(async () => {
      try {
        const userData = await communicationManager.getUser();
        user.value = userData;
        newName.value = userData.name;
        newEmail.value = userData.email;
        newBio.value = userData.bio || '';
        const userRecipes = await communicationManager.getUserRecipes(userData.id);
        recipes.value = userRecipes.recipes;

        if (isChef.value) {
          await loadScheduledLives();
        }

        // Load saved data
        await fetchSavedRecipes();
        await fetchUserFolders();
      } catch (error) {
        console.error('Error obteniendo datos del usuario:', error);
      }
    });

    return {
      // Saved section
      showSavedSection,
      showFoldersSection,
      loadingGuardades,
      savedRecipes,
      folders,
      showCreateFolderInput,
      newFolderName,
      removeSavedRecipe,
      createFolder,
      deleteFolder,
      showLikedSection,
      selectedFolder,
      selectedFolderRecipes,
      fetchFolderRecipes,
      goBackFromFolder,
      removeRecipeFromFolder,
      // Profile
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
      updatePassword,
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
      saveEditedLive,
      showLivesSection,
      showScheduledLives,
      hideLivesSection,
      goToExplore,
      goToVerification,
      goToRecipe,
      handleCreateFolderClick
    };
  }
};
</script>
<style scoped>
/* Estilos generales */
.profile-container {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
  color: #333;
}

/* Popup de notificación */
.popup-notification {
  position: fixed;
  top: 20px;
  left: 50%;
  transform: translateX(-50%);
  background-color: #4CAF50;
  color: white;
  padding: 1rem 2rem;
  border-radius: 0.5rem;
  z-index: 1000;
  animation: fadeInOut 3s ease-in-out;
}

@keyframes fadeInOut {
  0% { opacity: 0; transform: translate(-50%, -20px); }
  10% { opacity: 1; transform: translate(-50%, 0); }
  90% { opacity: 1; transform: translate(-50%, 0); }
  100% { opacity: 0; transform: translate(-50%, -20px); }
}

/* Header de perfil */
.profile-header {
  margin-bottom: 40px;
  position: relative;
  background: linear-gradient(135deg, #e0ff00, #ffff00);
  border-radius: 20px;
  padding: 30px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.settings-btn {
  position: absolute;
  top: 10px;
  right: 10px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 10px;
  border-radius: 50%;
  transition: all 0.3s ease;
  z-index: 2;
}

.settings-btn:hover {
  background-color: rgba(255, 255, 255, 0.3);
}

.settings-icon {
  width: 24px;
  height: 24px;
}

.web-button-container {
  position: absolute;
  top: 10px;
  left: 10px;
  z-index: 2;
}

.profile-content {
  display: flex;
  align-items: flex-start;
  gap: 30px;
}

.profile-left {
  flex-shrink: 0;
}

.profile-right {
  flex-grow: 1;
  padding-top: 10px;
}

.profile-picture {
  display: block;
  width: 150px;
  height: 150px;
  border-radius: 50%;
  overflow: hidden;
  border: 5px solid rgba(255, 255, 255, 0.8);
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.profile-picture:hover {
  transform: scale(1.05);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
}

.profile-picture img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.profile-right h2 {
  font-size: 2.2rem;
  margin: 0 0 15px 0;
  color: #333;
  font-weight: 700;
}

.user-bio {
  font-size: 1.1rem;
  color: #444;
  line-height: 1.6;
  margin: 0;
  padding: 15px;
  background: rgba(255, 255, 255, 0.7);
  border-radius: 12px;
  backdrop-filter: blur(5px);
}

@media screen and (max-width: 768px) {
  .profile-content {
    flex-direction: row;
    align-items: flex-start;
    text-align: left;
    gap: 15px;
  }

  .profile-right {
    padding-top: 0;
  }

  .profile-picture {
    width: 100px;
    height: 100px;
  }

  .profile-right h2 {
    font-size: 1.5rem;
    margin-bottom: 10px;
  }

  .user-bio {
    font-size: 0.9rem;
    padding: 10px;
  }
}

@media screen and (max-width: 480px) {
  .profile-header {
    padding: 20px 15px;
  }

  .profile-content {
    gap: 10px;
  }

  .profile-picture {
    width: 80px;
    height: 80px;
  }

  .profile-right h2 {
    font-size: 1.3rem;
  }

  .user-bio {
    font-size: 0.85rem;
    padding: 8px;
  }
}

/* Menú de ajustes */
.settings-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 999;
  display: flex;
  justify-content: center;
  align-items: center;
}

.settings-menu {
  background-color: white;
  border-radius: 12px;
  padding: 20px;
  width: 90%;
  max-width: 400px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
  animation: fadeIn 0.3s ease;
}

.settings-menu button {
  display: block;
  width: 100%;
  padding: 12px 20px;
  margin: 8px 0;
  background: none;
  border: none;
  text-align: left;
  font-size: 16px;
  color: #333;
  cursor: pointer;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.settings-menu button:hover {
  background-color: #f0f0f0;
  color: #0c0636;
}

.admin-button-container {
  margin-top: 20px;
  border-top: 1px solid #eee;
  padding-top: 20px;
}

.admin-button {
  background-color: #0c0636;
  color: white !important;
  text-align: center !important;
  font-weight: 600;
}

.admin-button:hover {
  background-color: #1a1464 !important;
}

/* Confirmación de logout */
.popup-confirmation {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: white;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
  z-index: 1001;
  text-align: center;
  max-width: 90%;
  width: 400px;
}

.popup-confirmation p {
  margin-bottom: 20px;
  font-size: 18px;
}

.confirm-btn,
.cancel-btn {
  padding: 10px 20px;
  margin: 0 10px;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.confirm-btn {
  background-color: #4CAF50;
  color: white;
}

.confirm-btn:hover {
  background-color: #3e8e41;
}

.cancel-btn {
  background-color: #f1f1f1;
  color: #333;
}

.cancel-btn:hover {
  background-color: #e0e0e0;
}

/* Formularios de ajustes */
.settings-form {
  background-color: white;
  border-radius: 12px;
  padding: 30px;
  margin: 20px auto;
  max-width: 600px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.wide-form {
  max-width: 800px;
}

.settings-form label {
  display: block;
  margin: 15px 0 8px;
  font-weight: 600;
  color: #0c0636;
}

.settings-form input,
.settings-form textarea,
.settings-form select {
  width: 100%;
  padding: 12px 15px;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 16px;
  transition: all 0.3s ease;
}

.settings-form input:focus,
.settings-form textarea:focus,
.settings-form select:focus {
  border-color: #0c0636;
  box-shadow: 0 0 0 2px rgba(12, 6, 54, 0.2);
  outline: none;
}

.settings-form textarea {
  min-height: 100px;
  resize: vertical;
}

.submit-btn {
  background-color: #0c0636;
  color: white;
  border: none;
  padding: 12px 25px;
  margin-top: 20px;
  margin-right: 15px;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.submit-btn:hover {
  background-color: #1a1464;
  transform: translateY(-2px);
}

/* Subida de imagen */
.upload-image-container {
  text-align: center;
  padding: 30px;
  border: 2px dashed #ddd;
  border-radius: 12px;
  margin-bottom: 20px;
  transition: all 0.3s ease;
}

.upload-image-container:hover {
  border-color: #0c0636;
}

.upload-label {
  font-weight: 600;
  font-size: 18px;
  margin-bottom: 15px;
  display: block;
  color: #0c0636;
}

.upload-area {
  cursor: pointer;
}

.upload-area input[type="file"] {
  display: none;
}

.upload-instructions {
  color: #666;
  margin-top: 15px;
}

/* Contraseña */
.password-input-container {
  position: relative;
}

.password-toggle-icon {
  position: absolute;
  right: 15px;
  top: 50%;
  transform: translateY(-50%);
  width: 20px;
  height: 20px;
  cursor: pointer;
  opacity: 0.7;
  transition: all 0.3s ease;
}

.password-toggle-icon:hover {
  opacity: 1;
}

/* Sección de recetas */
.user-recipes {
  margin-top: 2rem;
  margin-bottom: 60px;
}

.recipe-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-top: 1.5rem;
}

.recipe-card {
  background: white;
  border-radius: 1rem;
  overflow: hidden;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  transition: transform 0.2s;
}

.recipe-card:hover {
  transform: translateY(-4px);
}

.recipe-image {
  width: 100%;
  height: 200px;
  object-fit: cover;
}

.recipe-content {
  padding: 1rem;
}

.recipe-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #166534;
  margin-bottom: 0.5rem;
}

.recipe-description {
  color: #4B5563;
  font-size: 0.875rem;
  line-height: 1.5;
}

.delete-button {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  background: rgba(255, 255, 255, 0.9);
  border-radius: 9999px;
  padding: 0.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: all 0.2s;
}

.delete-button:hover {
  background: #FEE2E2;
}

.delete-button img {
  width: 1.25rem;
  height: 1.25rem;
}

.live-card {
  background: white;
  border-radius: 1rem;
  padding: 1.5rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  transition: all 0.2s;
}

.live-card:hover {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.live-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #166534;
  margin-bottom: 0.5rem;
}

.live-date {
  color: #4B5563;
  margin-bottom: 1rem;
}

.live-actions {
  display: flex;
  gap: 0.75rem;
}

.edit-button {
  flex: 1;
  padding: 0.5rem 1rem;
  background: linear-gradient(to right, #22c55e, #a3e635);
  color: #166534;
  border-radius: 0.5rem;
  font-weight: 500;
  transition: all 0.2s;
}

.edit-button:hover {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.delete-live-button {
  flex: 1;
  padding: 0.5rem 1rem;
  background: #EF4444;
  color: white;
  border-radius: 0.5rem;
  font-weight: 500;
  transition: all 0.2s;
}

.delete-live-button:hover {
  background: #DC2626;
}

.empty-state {
  background: white;
  border-radius: 1rem;
  padding: 2rem;
  text-align: center;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.empty-state p {
  color: #4B5563;
  margin-bottom: 1rem;
}

.explore-button {
  padding: 0.75rem 1.5rem;
  background: linear-gradient(to right, #22c55e, #a3e635);
  color: #166534;
  border-radius: 0.5rem;
  font-weight: 500;
  transition: all 0.2s;
}

.explore-button:hover {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

@media (max-width: 768px) {
  .recipe-grid {
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
  }

  .recipe-image {
    height: 150px;
  }

  .recipe-title {
    font-size: 1rem;
  }

  .recipe-description {
    font-size: 0.75rem;
  }

  .live-card {
    padding: 1rem;
  }

  .live-title {
    font-size: 1rem;
  }

  .live-actions {
    flex-direction: column;
  }
}

/* No autenticado */
.auth-required-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 60vh;
}

.auth-required-message {
  text-align: center;
  max-width: 500px;
  padding: 40px;
  background-color: white;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.auth-required-message p {
  font-size: 18px;
  margin-bottom: 30px;
  color: #333;
}

.login-button {
  background-color: #0c0636;
  color: white;
  border: none;
  padding: 12px 30px;
  font-size: 16px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.login-button:hover {
  background-color: #1a1464;
  transform: translateY(-2px);
}

/* Animaciones */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Responsive */
@media screen and (max-width: 768px) {
  .profile-header {
    padding-top: 40px;
  }

  .profile-picture {
    width: 120px;
    height: 120px;
  }

  .recipe-cards,
  .live-cards {
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  }

  .settings-form {
    padding: 20px;
  }
}

@media screen and (max-width: 480px) {
  .profile-header h2 {
    font-size: 1.5rem;
  }

  .recipe-cards {
    grid-template-columns: 1fr;
  }

  .form-actions {
    flex-direction: column;
  }

  .submit-btn,
  .cancel-btn {
    width: 100%;
    margin: 5px 0;
  }

  .toggle-buttons {
    flex-direction: column;
    gap: 10px;
  }
}

/* Estilos para la sección de guardadas (copiados del código anterior) */
.guardades-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  margin-bottom: 60px;
}

.tabs-container {
  display: flex;
  justify-content: center;
  margin-bottom: 30px;
}

.tabs {
  display: flex;
  background-color: transparent;
  border-radius: 12px;
  padding: 5px;
  box-shadow: none;
  width: auto;
  gap: 10px;
}

.tab-button {
  padding: 8px;
  font-size: 16px;
  font-weight: 600;
  color: #555;
  background: linear-gradient(to right, #e5e5e5, #f5f5f5);
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
}

.tab-button:hover {
  background: linear-gradient(to right, #d5d5d5, #e5e5e5);
  transform: translateY(-2px);
}

.tab-button.active {
  background: linear-gradient(to right, #22c55e, #a3e635);
  color: #166534;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.tab-button svg {
  width: 20px;
  height: 20px;
}

.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px 0;
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 4px solid rgba(12, 6, 54, 0.2);
  border-radius: 50%;
  border-top-color: #0c0636;
  animation: spin 1s ease-in-out infinite;
  margin-bottom: 15px;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.recipe-item {
  position: relative;
  transition: transform 0.3s ease;
}

.recipe-item:hover {
  transform: translateY(-5px);
}

.recipe-card-wrapper {
  position: relative;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.recipe-card-wrapper:hover {
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.delete-btn-overlay {
  position: absolute;
  top: 10px;
  right: 10px;
  background-color: rgba(255, 255, 255, 0.9);
  border: none;
  border-radius: 50%;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  opacity: 0;
  transform: scale(0.8);
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.recipe-card-wrapper:hover .delete-btn-overlay {
  opacity: 1;
  transform: scale(1);
}

.delete-btn-overlay:hover {
  background-color: #ff4d4d;
}

.delete-btn-overlay:hover .delete-icon {
  filter: brightness(10);
}

.delete-icon {
  width: 18px;
  height: 18px;
  transition: all 0.3s ease;
}

.no-recipes-container,
.no-folders-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  background-color: #f9f9f9;
  border-radius: 12px;
  text-align: center;
}

.no-recipes,
.no-folders {
  font-size: 18px;
  color: #555;
  margin-bottom: 15px;
}

.folder-hint {
  font-size: 16px;
  color: #777;
  max-width: 300px;
  margin: 0 auto;
}

.explore-btn {
  margin-top: 20px;
  padding: 12px 24px;
  background-color: #0c0636;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.explore-btn:hover {
  background-color: #1a1464;
  transform: translateY(-2px);
}

.selected-folder-view {
  animation: fadeIn 0.4s ease;
}

.back-btn {
  display: inline-flex;
  align-items: center;
  background: none;
  border: none;
  color: #0c0636;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  margin-bottom: 20px;
  padding: 8px 16px;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.back-btn:hover {
  background-color: #eeeeff;
}

.back-icon {
  margin-right: 8px;
  font-size: 18px;
}

.folder-title {
  font-size: 22px;
  color: #0c0636;
  margin-bottom: 30px;
  text-align: center;
  font-weight: 600;
}

.create-folder-section {
  display: flex;
  justify-content: center;
  margin-bottom: 30px;
}

.create-folder-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(to right, #22c55e, #a3e635);
  color: #166534;
  border: none;
  border-radius: 8px;
  padding: 12px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  width: 48px;
  height: 48px;
}

.create-folder-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.create-folder-btn svg {
  width: 24px;
  height: 24px;
}

.create-folder-input {
  background-color: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 400px;
  animation: fadeIn 0.3s ease;
}

.create-folder-input input {
  width: 100%;
  padding: 12px 15px;
  font-size: 16px;
  border: 1px solid #ddd;
  border-radius: 8px;
  margin-bottom: 15px;
  transition: all 0.3s ease;
}

.create-folder-input input:focus {
  border-color: #0c0636;
  box-shadow: 0 0 0 2px rgba(12, 6, 54, 0.2);
  outline: none;
}

.button-group {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

.folders-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 25px;
  margin-top: 30px;
}

.folder-card {
  cursor: pointer;
  transition: all 0.3s ease;
}

.folder-card:hover {
  transform: translateY(-5px);
}

.folder-image-container {
  position: relative;
  border-radius: 12px;
  overflow: hidden;
  aspect-ratio: 1 / 1;
  background-color: #f5f5f5;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.folder-card:hover .folder-image-container {
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.folder-icon {
  width: 70%;
  height: auto;
  transition: all 0.3s ease;
}

.folder-card:hover .folder-icon {
  transform: scale(1.05);
}

.folder-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
  padding: 15px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.folder-name {
  color: white;
  font-size: 16px;
  font-weight: 600;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 80%;
}

.delete-folder-btn {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  border-radius: 50%;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
}

.delete-folder-btn:hover {
  background: rgba(255, 77, 77, 0.8);
}

.delete-folder-btn img {
  width: 16px;
  height: 16px;
  filter: brightness(10);
}

.button-container {
  display: flex;
  justify-content: center;
  margin-top: 40px;
  margin-bottom: 60px;
}

.back-main-btn {
  padding: 12px 25px;
  background-color: #f5f5f5;
  color: #333;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.back-main-btn:hover {
  background-color: #e5e5e5;
}

/* Responsive adjustments para sección guardadas */
@media screen and (max-width: 768px) {
  .recipe-cards {
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 15px;
  }

  .folders-grid {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
  }

  .tabs button {
    padding: 10px 12px;
    font-size: 14px;
  }

  .section-title {
    font-size: 1.5rem;
  }
}

@media screen and (max-width: 480px) {
  .recipe-cards {
    grid-template-columns: 1fr;
  }

  .folders-grid {
    grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
    gap: 10px;
  }

  .create-folder-input {
    padding: 15px;
  }
}

.section-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #0c0636;
  margin-bottom: 30px;
  text-align: center;
}
</style>