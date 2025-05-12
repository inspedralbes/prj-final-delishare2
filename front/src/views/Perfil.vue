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
        <button @click="toggleSection('saved')" :class="{ active: showSavedSection }">
          <svg width="25" height="25" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M5 6.2C5 5.07989 5 4.51984 5.21799 4.09202C5.40973 3.71569 5.71569 3.40973 6.09202 3.21799C6.51984 3 7.07989 3 8.2 3H15.8C16.9201 3 17.4802 3 17.908 3.21799C18.2843 3.40973 18.5903 3.71569 18.782 4.09202C19 4.51984 19 5.07989 19 6.2V21L12 16L5 21V6.2Z"
              stroke="#000000" stroke-width="2" stroke-linejoin="round" />
          </svg>
        </button>

       <!-- En la sección de toggle buttons, reemplaza el botón duplicado de saved por el de liked -->
<button @click="toggleSection('liked')" :class="{ active: showLikedSection }">
  <svg width="25" height="25" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
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
          <!-- Botones de navegación mejorados -->
          <div class="tabs-container">
            <div class="tabs">
              <button :class="{ active: savedActiveTab === 'guardades' }" @click="savedActiveTab = 'guardades'">
                <span class="tab-icon">📌</span> Guardades
              </button>
              <button :class="{ active: savedActiveTab === 'carpetes' }" @click="savedActiveTab = 'carpetes'">
                <span class="tab-icon">🗂️</span> Les meves carpetes
              </button>
            </div>
          </div>
          <!-- Sección de receptes guardades -->
          <div v-if="savedActiveTab === 'guardades'" class="guardades">
            <h3 class="section-title">📌 Receptes guardades</h3>
            <div v-if="loadingGuardades" class="loading-container">
              <div class="loading-spinner"></div>
              <p>Carregant...</p>
            </div>
            <div v-else>
              <div class="recipe-cards">
                <div v-for="recipe in savedRecipes" :key="recipe.id" class="recipe-item">
                  <div class="recipe-card-wrapper">
                    <RecipeCard :recipe-id="recipe.id" :title="recipe.title" :image="recipe.image"
                      :description="recipe.description" />
                    <button @click="removeSavedRecipe(recipe.id)" class="delete-btn-overlay">
                      <img :src="binIcon" alt="Eliminar" class="delete-icon" />
                    </button>
                  </div>
                </div>
              </div>
              <div v-if="savedRecipes.length === 0" class="no-recipes-container">
                <p class="no-recipes">No tens receptes guardades.</p>
                <button @click="goToExplore" class="explore-btn">Explorar receptes</button>
              </div>
            </div>
          </div>
          <!-- Sección de carpetes -->
          <div v-if="savedActiveTab === 'carpetes'" class="carpetes">
            <h3 class="section-title">🗂️ Les meves carpetes</h3>

            <!-- Vista de carpeta única -->
            <div v-if="selectedFolder" class="selected-folder-view">
              <button @click="goBackFromFolder" class="back-btn">
                <span class="back-icon">←</span> Tornar enrere
              </button>
              <h4 class="folder-title">"{{ selectedFolder.name }}"</h4>
              <div class="recipe-cards" v-if="selectedFolderRecipes.length > 0">
                <div v-for="recipe in selectedFolderRecipes" :key="recipe.id" class="recipe-item">
                  <div class="recipe-card-wrapper">
                    <RecipeCard :recipe-id="recipe.id" :title="recipe.title" :image="recipe.image"
                      :description="recipe.description" />
                    <button @click="removeRecipeFromFolder(recipe.id, selectedFolder.id)" class="delete-btn-overlay">
                      <img :src="binIcon" alt="Eliminar" class="delete-icon" />
                    </button>
                  </div>
                </div>
              </div>
              <div v-else class="no-recipes-container">
                <p class="no-recipes">No hi ha receptes en aquesta carpeta.</p>
              </div>
            </div>



            <div v-else class="folders-view">
              <div class="create-folder-section">
                <button v-if="!showCreateFolderInput" @click="showCreateFolderInput = true" class="create-folder-btn">
                  <span class="plus-icon">+</span> Crear carpeta
                </button>
                <div v-if="showCreateFolderInput" class="create-folder-input">
                  <input type="text" v-model="newFolderName" placeholder="Nom de la carpeta" @keyup.enter="createFolder"
                    ref="folderNameInput" autofocus />
                  <div class="button-group">
                    <button @click="createFolder" class="submit-btn">Guardar</button>
                    <button @click="showCreateFolderInput = false" class="cancel-btn">Cancelar</button>
                  </div>
                </div>
              </div>
              <div v-if="folders.length > 0" class="folders-grid">
                <div class="folder-card" v-for="folder in folders" :key="folder.id"
                  @click="fetchFolderRecipes(folder.id)">
                  <div class="folder-image-container">
                    <img src="@/assets/images/folder2.png" alt="Folder" class="folder-icon" />
                    <div class="folder-overlay">
                      <span class="folder-name">{{ folder.name }}</span>
                      <button @click.stop="deleteFolder(folder.id)" class="delete-folder-btn">
                        <img :src="binIcon" alt="Delete" />
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <div v-if="folders.length === 0" class="no-folders-container">
                <p class="no-folders">No tens cap carpeta.</p>
                <p class="folder-hint">Crea carpetes per organitzar les teves receptes favorites!</p>
              </div>
            </div>
          </div>
        </div>
        <div class="button-container">
          <button @click="cancelEdit" class="back-main-btn">🔙 Tornar</button>
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
    const authStore = useAuthStore();
    const router = useRouter();

    // Saved section variables
    const showSavedSection = ref(false);
    const savedActiveTab = ref('guardades');
    const loadingGuardades = ref(true);
    const savedRecipes = ref([]);
    const folders = ref([]);
    const selectedFolder = ref(null);
    const selectedFolderRecipes = ref([]);
    const showCreateFolderInput = ref(false);
    const newFolderName = ref('');

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

  // Activar solo la sección seleccionada
  if (section === 'recipes') showRecipes.value = true;
  else if (section === 'lives') showLivesSection.value = true;
  else if (section === 'saved') showSavedSection.value = true;
  else if (section === 'liked') showLikedSection.value = true;

  // Cargar datos si es necesario
  if (section === 'lives') loadScheduledLives();
  if (section === 'saved') {
    fetchSavedRecipes();
    fetchUserFolders();
  }
  if (section === 'liked') loadLikedRecipes();
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
      savedActiveTab,
      loadingGuardades,
      savedRecipes,
      folders,
      selectedFolder,
      selectedFolderRecipes,
      showCreateFolderInput,
      newFolderName,
      removeSavedRecipe,
      fetchFolderRecipes,
      createFolder,
      deleteFolder,
      removeRecipeFromFolder,
      goBackFromFolder,
      showLikedSection,
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
      goToExplore ,
      goToVerification
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
  padding: 15px 25px;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  z-index: 1000;
  animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
  from {
    top: -50px;
    opacity: 0;
  }

  to {
    top: 20px;
    opacity: 1;
  }
}

/* Header de perfil */
.profile-header {
  text-align: center;
  margin-bottom: 40px;
  position: relative;
}

.settings-btn {
  position: absolute;
  top: 0;
  right: 0;
  background: none;
  border: none;
  cursor: pointer;
  padding: 10px;
  border-radius: 50%;
  transition: all 0.3s ease;
}

.settings-btn:hover {
  background-color: #f0f0f0;
}

.settings-icon {
  width: 24px;
  height: 24px;
}

.web-button-container {
  position: absolute;
  top: 0;
  left: 0;
}

.web-button {
  cursor: pointer;
  transition: transform 0.3s ease;
}

.web-button:hover {
  transform: scale(1.1);
}

.profile-picture {
  display: inline-block;
  width: 150px;
  height: 150px;
  border-radius: 50%;
  overflow: hidden;
  border: 5px solid #0c0636;
  margin-bottom: 20px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.profile-picture:hover {
  transform: scale(1.05);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.profile-picture img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.profile-header h2 {
  font-size: 2rem;
  margin: 10px 0;
  color: #0c0636;
}

.user-bio {
  max-width: 600px;
  margin: 0 auto;
  color: #666;
  line-height: 1.6;
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
  margin-top: 40px;
  padding: 20px;
  background-color: white;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.section-title {
  font-size: 1.8rem;
  color: #0c0636;
  margin-bottom: 30px;
  text-align: center;
  font-weight: 700;
}

.recipe-cards {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 25px;
  margin-bottom: 40px;
}
.liked-section {
  padding: 20px;
}

.no-liked-recipes {
  text-align: center;
  margin: 30px 0;
  color: #666;
}

.no-liked-recipes p {
  margin-bottom: 15px;
}

.liked-recipe-card {
  margin-bottom: 20px;
  position: relative;
}

.explore-btn {
  background-color: #4CAF50;
  color: white;
  border: none;
  padding: 10px 15px;
  border-radius: 5px;
  cursor: pointer;
  font-size: 16px;
  transition: background-color 0.3s;
}

.explore-btn:hover {
  background-color: #45a049;
}
.no-liked-recipes,
.no-lives {
  text-align: center;
  padding: 40px 0;
  color: #666;
}

.liked-recipe-card {
  position: relative;
  transition: all 0.3s ease;
}

.liked-recipe-card:hover {
  transform: translateY(-5px);
}

/* Lives programados */
.live-cards {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
}

.live-card {
  background-color: #f9f9f9;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.live-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
}

.live-info h4 {
  margin-top: 0;
  color: #0c0636;
}

.live-actions {
  display: flex;
  gap: 10px;
  margin-top: 15px;
}

.edit-btn,
.delete-btn {
  padding: 8px 15px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 14px;
}

.edit-btn {
  background-color: #2196F3;
  color: white;
}

.edit-btn:hover {
  background-color: #0b7dda;
}

.delete-btn {
  background-color: #f44336;
  color: white;
}

.delete-btn:hover {
  background-color: #d32f2f;
}

/* Formulario de live */
.live-form-overlay {
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

.live-form-container {
  background-color: white;
  border-radius: 12px;
  padding: 30px;
  width: 90%;
  max-width: 500px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
  animation: fadeIn 0.3s ease;
}

.live-form-container h3 {
  margin-top: 0;
  color: #0c0636;
  text-align: center;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #0c0636;
}

.form-group input,
.form-group select {
  width: 100%;
  padding: 12px 15px;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 16px;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 15px;
  margin-top: 30px;
}

/* Botones de sección */
.toggle-buttons {
  display: flex;
  justify-content: center;
  gap: 20px;
  margin-bottom: 30px;
}

.toggle-buttons button {
  background: none;
  border: none;
  padding: 12px 20px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #f5f5f5;
}

.toggle-buttons button.active {
  background-color: #0c0636;
  color: white;
}

.toggle-buttons button:hover:not(.active) {
  background-color: #e0e0e0;
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
}

.tabs-container {
  display: flex;
  justify-content: center;
  margin-bottom: 30px;
}

.tabs {
  display: flex;
  background-color: #f5f5f5;
  border-radius: 12px;
  padding: 5px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  width: 100%;
  max-width: 400px;
}

.tabs button {
  flex: 1;
  padding: 12px 20px;
  font-size: 16px;
  font-weight: 600;
  color: #555;
  background: none;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.tabs button.active {
  background-color: #0c0636;
  color: white;
}

.tab-icon {
  margin-right: 8px;
  font-size: 18px;
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
  background-color: #0c0636;
  color: white;
  border: none;
  border-radius: 8px;
  padding: 12px 24px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.create-folder-btn:hover {
  background-color: #1a1464;
  transform: translateY(-2px);
}

.plus-icon {
  margin-right: 8px;
  font-size: 18px;
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
</style>