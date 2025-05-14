<template>
  <div class="profile-container pb-24">
    <div v-if="authStore.isAuthenticated">
      <!-- Popup de notificación -->
      <div v-if="popupMessage" class="popup-notification">
        {{ popupMessage }}
      </div>

      <!-- Crear nova carpeta -->
      <div v-if="showCreateFolderInput"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50"
        @click="showCreateFolderInput = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform transition-all duration-300"
          @click.stop>
          <div class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-2xl font-bold text-lime-900">Crear Nova Carpeta</h3>
              <button @click="showCreateFolderInput = false"
                class="text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
            <div class="space-y-4">
              <div class="form-group">
                <label for="folder-name" class="block text-sm font-medium text-gray-700 mb-1">Nom de la carpeta:</label>
                <input type="text" id="folder-name" v-model="newFolderName" placeholder="Nom de la carpeta"
                  @keyup.enter="createFolder" ref="folderNameInput" autofocus
                  class="w-full px-3 py-2 text-sm text-gray-700 bg-white border-2 border-lime-200 rounded-lg focus:outline-none focus:border-lime-400 focus:ring-2 focus:ring-lime-200 transition-all duration-300" />
              </div>
              <div class="flex space-x-4 pt-4">
                <button @click="createFolder"
                  class="flex-1 px-4 py-2 bg-gradient-to-r from-lime-400 to-green-500 text-white font-medium rounded-lg shadow-md hover:from-lime-500 hover:to-green-600 focus:outline-none focus:ring-2 focus:ring-lime-300 focus:ring-opacity-50 transition-all duration-300 transform hover:scale-105">
                  <span class="flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Crear Carpeta
                  </span>
                </button>
                <button @click="showCreateFolderInput = false"
                  class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg shadow-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-opacity-50 transition-all duration-300">
                  Cancelar
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Header de perfil -->
      <div
        class="relative mb-10 rounded-2xl bg-gradient-to-br from-lime-100 via-lime-200 to-green-200 p-4 sm:p-6 shadow-lg">
        <div class="flex justify-between items-start">
          <!-- Información del usuario a la izquierda -->
          <div class="flex items-start gap-3">
            <div class="flex-shrink-0">
              <label for="file-input" class="block cursor-pointer">
                <div
                  class="relative h-24 w-24 overflow-hidden rounded-full border-4 border-white/80 shadow-lg transition-all hover:scale-105 hover:shadow-xl">
                  <img :src="user.img || defaultProfile" alt="Foto de perfil" class="h-full w-full object-cover" />
                </div>
              </label>
            </div>
            <div class="flex-grow">
              <h2 class="mb-2 text-2xl sm:text-3xl font-bold text-lime-900">{{ user.name }}</h2>
              <p class="rounded-xl bg-white/70 p-2 sm:p-3 text-sm sm:text-base text-lime-700 backdrop-blur-sm">
                {{ user.bio || 'No hi ha biografia disponible' }}
              </p>
            </div>
          </div>

          <!-- Botones de acción alineados a la derecha -->
          <div class="flex flex-col items-center space-y-1">
            <!-- Botón de ajustes -->
            <button @click="toggleSettingsMenu" class="rounded-full p-2 transition-all hover:bg-white/30">
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                <g id="SVGRepo_iconCarrier">
                  <path
                    d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z"
                    stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  <path
                    d="M19.4 15C19.2669 15.3016 19.2272 15.6362 19.286 15.9606C19.3448 16.285 19.4995 16.5843 19.73 16.82L19.79 16.88C19.976 17.0657 20.1235 17.2863 20.2241 17.5291C20.3248 17.7719 20.3766 18.0322 20.3766 18.295C20.3766 18.5578 20.3248 18.8181 20.2241 19.0609C20.1235 19.3037 19.976 19.5243 19.79 19.71C19.6043 19.896 19.3837 20.0435 19.1409 20.1441C18.8981 20.2448 18.6378 20.2966 18.375 20.2966C18.1122 20.2966 17.8519 20.2448 17.6091 20.1441C17.3663 20.0435 17.1457 19.896 16.96 19.71L16.9 19.65C16.6643 19.4195 16.365 19.2648 16.0406 19.206C15.7162 19.1472 15.3816 19.1869 15.08 19.32C14.7842 19.4468 14.532 19.6572 14.3543 19.9255C14.1766 20.1938 14.0813 20.5082 14.08 20.83V21C14.08 21.5304 13.8693 22.0391 13.4942 22.4142C13.1191 22.7893 12.6104 23 12.08 23C11.5496 23 11.0409 22.7893 10.6658 22.4142C10.2907 22.0391 10.08 21.5304 10.08 21V20.91C10.0723 20.579 9.96512 20.258 9.77251 19.9887C9.5799 19.7194 9.31074 19.5143 9 19.4C8.69838 19.2669 8.36381 19.2272 8.03941 19.286C7.71502 19.3448 7.41568 19.4995 7.18 19.73L7.12 19.79C6.93425 19.976 6.71368 20.1235 6.47088 20.2241C6.22808 20.3248 5.96783 20.3766 5.705 20.3766C5.44217 20.3766 5.18192 20.3248 4.93912 20.2241C4.69632 20.1235 4.47575 19.976 4.29 19.79C4.10405 19.6043 3.95653 19.3837 3.85588 19.1409C3.75523 18.8981 3.70343 18.6378 3.70343 18.375C3.70343 18.1122 3.75523 17.8519 3.85588 17.6091C3.95653 17.3663 4.10405 17.1457 4.29 16.96L4.35 16.9C4.58054 16.6643 4.73519 16.365 4.794 16.0406C4.85282 15.7162 4.81312 15.3816 4.68 15.08C4.55324 14.7842 4.34276 14.532 4.07447 14.3543C3.80618 14.1766 3.49179 14.0813 3.17 14.08H3C2.46957 14.08 1.96086 13.8693 1.58579 13.4942C1.21071 13.1191 1 12.6104 1 12.08C1 11.5496 1.21071 11.0409 1.58579 10.6658C1.96086 10.2907 2.46957 10.08 3 10.08H3.09C3.42099 10.0723 3.742 9.96512 4.0113 9.77251C4.28059 9.5799 4.48572 9.31074 4.6 9C4.73312 8.69838 4.77282 8.36381 4.714 8.03941C4.65519 7.71502 4.50054 7.41568 4.27 7.18L4.21 7.12C4.02405 6.93425 3.87653 6.71368 3.77588 6.47088C3.67523 6.22808 3.62343 5.96783 3.62343 5.705C3.62343 5.44217 3.67523 5.18192 3.77588 4.93912C3.87653 4.69632 4.02405 4.47575 4.21 4.29C4.39575 4.10405 4.61632 3.95653 4.85912 3.85588C5.10192 3.75523 5.36217 3.70343 5.625 3.70343C5.88783 3.70343 6.14808 3.75523 6.39088 3.85588C6.63368 3.95653 6.85425 4.10405 7.04 4.29L7.1 4.35C7.33568 4.58054 7.63502 4.73519 7.95941 4.794C8.28381 4.85282 8.61838 4.81312 8.92 4.68H9C9.29577 4.55324 9.54802 4.34276 9.72569 4.07447C9.90337 3.80618 9.99872 3.49179 10 3.17V3C10 2.46957 10.2107 1.96086 10.5858 1.58579C10.9609 1.21071 11.4696 1 12 1C12.5304 1 13.0391 1.21071 13.4142 1.58579C13.7893 1.96086 14 2.46957 14 3V3.09C14.0013 3.41179 14.0966 3.72618 14.2743 3.99447C14.452 4.26276 14.7042 4.47324 15 4.6C15.3016 4.73312 15.6362 4.77282 15.9606 4.714C16.285 4.65519 16.5843 4.50054 16.82 4.27L16.88 4.21C17.0657 4.02405 17.2863 3.87653 17.5291 3.77588C17.7719 3.67523 18.0322 3.62343 18.295 3.62343C18.5578 3.62343 18.8181 3.67523 19.0609 3.77588C19.3037 3.87653 19.5243 4.02405 19.71 4.21C19.896 4.39575 20.0435 4.61632 20.1441 4.85912C20.2448 5.10192 20.2966 5.36217 20.2966 5.625C20.2966 5.88783 20.2448 6.14808 20.1441 6.39088C20.0435 6.63368 19.896 6.85425 19.71 7.04L19.65 7.1C19.4195 7.33568 19.2648 7.63502 19.206 7.95941C19.1472 8.28381 19.1869 8.61838 19.32 8.92V9C19.4468 9.29577 19.6572 9.54802 19.9255 9.72569C20.1938 9.90337 20.5082 9.99872 20.83 10H21C21.5304 10 22.0391 10.2107 22.4142 10.5858C22.7893 10.9609 23 11.4696 23 12C23 12.5304 22.7893 13.0391 22.4142 13.4142C22.0391 13.7893 21.5304 14 21 14H20.91C20.5882 14.0013 20.2738 14.0966 20.0055 14.2743C19.7372 14.452 19.5268 14.7042 19.4 15Z"
                    stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </g>
              </svg>
            </button>

            <!-- Icono SVG para crear nuevo live -->
            <div v-if="isChef && !settingsMenuOpen">
              <button @click="handleLiveButtonClick" class="rounded-full p-2 transition-all hover:bg-white/30">
                <svg class="h-5 w-5" viewBox="0 0 72 72" xmlns="http://www.w3.org/2000/svg" fill="black" stroke="black"
                  stroke-width="1">
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

            <!-- Botón de crear carpeta -->
            <button v-if="!showCreateFolderInput" @click="handleCreateFolderClick"
              class="rounded-full p-2 transition-all hover:bg-white/30">
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                <g id="SVGRepo_iconCarrier">
                  <path
                    d="M9 13H15M12 10V16M12.0627 6.06274L11.9373 5.93726C11.5914 5.59135 11.4184 5.4184 11.2166 5.29472C11.0376 5.18506 10.8425 5.10425 10.6385 5.05526C10.4083 5 10.1637 5 9.67452 5H6.2C5.0799 5 4.51984 5 4.09202 5.21799C3.71569 5.40973 3.40973 5.71569 3.21799 6.09202C3 6.51984 3 7.07989 3 8.2V15.8C3 16.9201 3 17.4802 3.21799 17.908C3.40973 18.2843 3.71569 18.5903 4.09202 18.782C4.51984 19 5.07989 19 6.2 19H17.8C18.9201 19 19.4802 19 19.908 18.782C20.2843 18.5903 20.5903 18.2843 20.782 17.908C21 17.4802 21 16.9201 21 15.8V10.2C21 9.0799 21 8.51984 20.782 8.09202C20.5903 7.71569 20.2843 7.40973 19.908 7.21799C19.4802 7 18.9201 7 17.8 7H14.3255C13.8363 7 13.5917 7 13.3615 6.94474C13.1575 6.89575 12.9624 6.81494 12.7834 6.70528C12.5816 6.5816 12.4086 6.40865 12.0627 6.06274Z"
                    stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                </g>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Menú de ajustes -->
      <div v-if="settingsMenuOpen"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50"
        @click="toggleSettingsMenu">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform transition-all duration-300"
          @click.stop>
          <div class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-2xl font-bold text-lime-900">Ajustes</h3>
              <button @click="toggleSettingsMenu" class="text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
            <div class="space-y-2">
              <button @click="setActiveTab('image')"
                class="w-full flex items-center px-4 py-3 text-left text-gray-700 hover:bg-lime-50 rounded-xl transition-all duration-300">
                <svg class="w-5 h-5 mr-3 text-lime-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                  </path>
                </svg>
                Canviar imatge de perfil
              </button>
              <button @click="setActiveTab('name')"
                class="w-full flex items-center px-4 py-3 text-left text-gray-700 hover:bg-lime-50 rounded-xl transition-all duration-300">
                <svg class="w-5 h-5 mr-3 text-lime-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Canviar dades d'usuari
              </button>
              <button @click="setActiveTab('password')"
                class="w-full flex items-center px-4 py-3 text-left text-gray-700 hover:bg-lime-50 rounded-xl transition-all duration-300">
                <svg class="w-5 h-5 mr-3 text-lime-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                  </path>
                </svg>
                Canviar contrasenya
              </button>

              <!-- Nueva opción de Editar Recomendaciones -->
              <button @click="goToFormulario"
                class="w-full flex items-center px-4 py-3 text-left text-gray-700 hover:bg-lime-50 rounded-xl transition-all duration-300">
                <svg class="w-5 h-5 mr-3 text-lime-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                  </path>
                </svg>
                Editar Recomendaciones
              </button>

              <div v-if="isAdmin" class="mt-4">
                <button @click="goToAdmin"
                  class="w-full flex items-center px-4 py-3 text-left text-white bg-gradient-to-r from-lime-600 to-green-600 hover:from-lime-700 hover:to-green-700 rounded-xl transition-all duration-300">
                  <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                    </path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  </svg>
                  Administración
                </button>
              </div>
              <div v-if="!isChef && !isAdmin" class="mt-4">
                <button @click="goToVerification"
                  class="w-full flex items-center px-4 py-3 text-left text-white bg-gradient-to-r from-green-500 to-lime-500 hover:from-green-600 hover:to-lime-600 rounded-xl transition-all duration-300">
                  <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Pedir Verificación
                </button>
              </div>
              <div class="mt-4 pt-4 border-t border-gray-200">
                <button @click="confirmLogout('logOut')"
                  class="w-full flex items-center px-4 py-3 text-left text-white bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 rounded-xl transition-all duration-300">
                  <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                    </path>
                  </svg>
                  Tancar sessió
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Confirmar logout -->
      <div v-if="showLogoutConfirmation"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6 transform transition-all duration-300">
          <div class="text-center">
            <svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
              </path>
            </svg>
            <h3 class="text-xl font-bold text-gray-900 mb-2">¿Estás seguro?</h3>
            <p class="text-gray-600 mb-6">¿Estàs segur que vols tancar la sessió?</p>
            <div class="flex space-x-4">
              <button @click="logout"
                class="flex-1 px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white font-medium rounded-lg hover:from-red-600 hover:to-red-700 transition-all duration-300">
                Sí, cerrar sesión
              </button>
              <button @click="cancelLogout"
                class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-all duration-300">
                Cancelar
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Formularios de ajustes -->
      <div v-if="activeTab === 'image'"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 p-6 transform transition-all duration-300">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-lime-900">Cambiar Foto de Perfil</h3>
            <button @click="cancelEdit" class="text-gray-500 hover:text-gray-700 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gray-50 rounded-xl p-4">
              <h4 class="text-sm font-medium text-gray-700 mb-2">Foto actual</h4>
              <div class="aspect-square rounded-lg overflow-hidden bg-gray-100">
                <img :src="user.img || defaultProfile" alt="Foto de perfil actual" class="w-full h-full object-cover" />
              </div>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
              <h4 class="text-sm font-medium text-gray-700 mb-2">Nueva foto</h4>
              <div
                class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-lime-500 transition-colors">
                <input type="file" id="image" @change="uploadImage" accept="image/*" class="hidden" />
                <label for="image" class="cursor-pointer">
                  <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                    </path>
                  </svg>
                  <p class="text-sm text-gray-600">Haz clic para seleccionar una imagen</p>
                  <p class="text-xs text-gray-500 mt-1">o arrastra y suelta una imagen aquí</p>
                </label>
              </div>
            </div>
          </div>
          <div class="mt-6 flex justify-end">
            <button @click="cancelEdit"
              class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-all duration-300">
              Cancelar
            </button>
          </div>
        </div>
      </div>

      <div v-if="activeTab === 'name'"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6 transform transition-all duration-300">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-lime-900">Editar Perfil</h3>
            <button @click="cancelEdit" class="text-gray-500 hover:text-gray-700 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          <div class="space-y-4">
            <div>
              <label for="newName" class="block text-sm font-medium text-gray-700 mb-1">Nou nom:</label>
              <input type="text" id="newName" v-model="newName" :placeholder="user.name"
                class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-lime-500 transition-colors" />
            </div>
            <div>
              <label for="newEmail" class="block text-sm font-medium text-gray-700 mb-1">Nou email:</label>
              <input type="email" id="newEmail" v-model="newEmail" :placeholder="user.email"
                class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-lime-500 transition-colors" />
            </div>
            <div>
              <label for="newBio" class="block text-sm font-medium text-gray-700 mb-1">Biografia:</label>
              <textarea id="newBio" v-model="newBio" :placeholder="user.bio || 'Afegeix una biografia...'" rows="4"
                class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-lime-500 transition-colors"></textarea>
            </div>
          </div>
          <div class="mt-6 flex space-x-4">
            <button @click="updateProfile"
              class="flex-1 px-4 py-2 bg-gradient-to-r from-lime-500 to-green-500 text-white font-medium rounded-lg hover:from-lime-600 hover:to-green-600 transition-all duration-300">
              Guardar canvis
            </button>
            <button @click="cancelEdit"
              class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-all duration-300">
              Cancelar
            </button>
          </div>
        </div>
      </div>

      <div v-if="activeTab === 'password'"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6 transform transition-all duration-300">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-lime-900">Cambiar Contraseña</h3>
            <button @click="cancelEdit" class="text-gray-500 hover:text-gray-700 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          <div class="space-y-4">
            <div>
              <label for="currentPassword" class="block text-sm font-medium text-gray-700 mb-1">Contrasenya
                actual:</label>
              <div class="relative">
                <input :type="showCurrentPassword ? 'text' : 'password'" id="currentPassword" v-model="currentPassword"
                  class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-lime-500 transition-colors" />
                <button @click="togglePasswordVisibility('current')"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                  <img :src="showCurrentPassword ? eyeOpenIcon : eyeClosedIcon" class="w-5 h-5" />
                </button>
              </div>
            </div>
            <div>
              <label for="newPassword" class="block text-sm font-medium text-gray-700 mb-1">Nova contrasenya:</label>
              <div class="relative">
                <input :type="showNewPassword ? 'text' : 'password'" id="newPassword" v-model="newPassword"
                  class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-lime-500 transition-colors" />
                <button @click="togglePasswordVisibility('new')"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                  <img :src="showNewPassword ? eyeOpenIcon : eyeClosedIcon" class="w-5 h-5" />
                </button>
              </div>
            </div>
            <div>
              <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-1">Confirmar nova
                contrasenya:</label>
              <div class="relative">
                <input :type="showConfirmPassword ? 'text' : 'password'" id="confirmPassword" v-model="confirmPassword"
                  class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-lime-500 transition-colors" />
                <button @click="togglePasswordVisibility('confirm')"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                  <img :src="showConfirmPassword ? eyeOpenIcon : eyeClosedIcon" class="w-5 h-5" />
                </button>
              </div>
            </div>
          </div>
          <div class="mt-6 flex space-x-4">
            <button @click="updatePassword"
              class="flex-1 px-4 py-2 bg-gradient-to-r from-lime-500 to-green-500 text-white font-medium rounded-lg hover:from-lime-600 hover:to-green-600 transition-all duration-300">
              Guardar canvis
            </button>
            <button @click="cancelEdit"
              class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-all duration-300">
              Cancelar
            </button>
          </div>
        </div>
      </div>

      <!-- Botones para ver secciones -->
      <div v-if="!showLiveForm && !settingsMenuOpen && activeTab === ''"
        class="flex justify-center gap-4 mt-8 mb-6 px-6 md:gap-2 md:mt-4 md:mb-2 md:px-2">
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
            <path d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" stroke="#000000"
              stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>

        <button @click="toggleSection('liked')" :class="[
          'flex-1 py-2 px-2 text-sm rounded-full font-semibold transition-all duration-300',
          'md:py-4 md:px-4 md:text-base',
          showLikedSection ? 'bg-gradient-to-r from-green-500 via-lime-400 to-lime-300 text-lime-900 shadow-lg hover:shadow-xl hover:brightness-110' : 'bg-gradient-to-r from-green-100 via-lime-50 to-lime-100 text-lime-700 hover:from-green-200 hover:via-lime-100 hover:to-lime-200 hover:shadow-md'
        ]">
          <svg width="25" height="25" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="inline-block">
            <path
              d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"
              stroke="#000000" stroke-width="2" stroke-linejoin="round" />
          </svg>
        </button>
      </div>

      <!-- Sección de guardadas -->
      <div v-if="showSavedSection" class="user-recipes saved-section pb-20">
        <div v-if="popupMessage" class="popup-notification">
          {{ popupMessage }}
        </div>

        <div class="guardades-container">
          <div class="bg-white rounded-xl p-6 shadow-lg mb-8">
            <h3 class="text-2xl font-bold text-lime-900 mb-4">Les meves receptes desades</h3>
            <p class="text-gray-600 mb-4">Aquí trobaràs totes les receptes que has desat per a tu. Aquestes receptes són
              privades i només tu les pots veure.</p>
          </div>
          <div v-if="loadingGuardades" class="loading-container">
            <div class="loading-spinner"></div>
            <p>Cargando...</p>
          </div>
          <div v-else>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              <div v-for="recipe in savedRecipes" :key="recipe.id"
                class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 cursor-pointer">
                <router-link :to="'/info/' + recipe.id" class="absolute inset-0 z-10" style="display:block" />
                <div class="relative">
                  <img :src="recipe.image" :alt="recipe.title" class="w-full h-48 object-cover" />
                  <div class="absolute top-3 right-3 flex gap-2">
                    <button @click.stop="editRecipe(recipe)"
                      class="bg-white/90 p-2 rounded-full shadow-md hover:bg-blue-100 transition-all duration-300">
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                      </svg>
                    </button>
                    <button @click.stop="deleteRecipe(recipe.id)"
                      class="bg-white/90 p-2 rounded-full shadow-md hover:bg-red-100 transition-all duration-300">
                      <img :src="binIcon" alt="Eliminar" class="w-5 h-5" />
                    </button>
                  </div>
                </div>
                <div class="p-6">
                  <h4 class="text-xl font-semibold text-lime-900 mb-2 truncate">{{ recipe.title }}</h4>
                  <p class="text-gray-600 text-sm line-clamp-2">{{ recipe.description }}</p>
                </div>
              </div>
            </div>
            <div v-if="savedRecipes.length === 0"
              class="bg-white rounded-xl p-8 text-center shadow-lg max-w-2xl mx-auto">
              <p class="text-gray-600 text-lg mb-4">Encara no tens receptes desades.</p>
              <button @click="goToExplore"
                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-lime-400 to-green-500 text-white font-medium rounded-lg shadow-md hover:from-lime-500 hover:to-green-600 focus:outline-none focus:ring-2 focus:ring-lime-300 focus:ring-opacity-50 transition-all duration-300 transform hover:scale-105">
                Explorar receptes
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Sección de carpetas -->
      <div v-if="showFoldersSection" class="user-recipes pb-20">
        <div v-if="selectedFolder" class="selected-folder-view">
          <h3 class="text-2xl font-bold text-lime-900 mb-8 text-center">{{ selectedFolder.name }}</h3>

          <div v-if="selectedFolderRecipes.length === 0"
            class="bg-white rounded-xl p-8 text-center shadow-lg max-w-2xl mx-auto">
            <p class="text-gray-600 text-lg mb-4">No hi ha receptes en aquesta carpeta.</p>
          </div>

          <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 px-4">
            <div v-for="recipe in selectedFolderRecipes" :key="recipe.id"
              class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 cursor-pointer">
              <router-link :to="'/info/' + recipe.id" class="absolute inset-0 z-10" style="display:block" />
              <div class="relative">
                <img :src="recipe.image_url || recipe.image" :alt="recipe.title" class="w-full h-48 object-cover" />
                <button @click="removeRecipeFromFolder(recipe.id, selectedFolder.id)"
                  class="absolute top-3 right-3 bg-white/90 p-2 rounded-full shadow-md hover:bg-red-100 transition-all duration-300">
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
              <h3 class="text-2xl font-bold text-lime-900">Les meves carpetes públiques</h3>
            </div>
            <p class="text-gray-600">Crea carpetes públiques per organitzar i compartir les teves receptes preferides
              amb altres usuaris. Aquestes carpetes seran visibles per a tothom.</p>
          </div>

          <div v-if="folders.length > 0" class="grid grid-cols-2 gap-3 max-w-md mx-auto">
            <div v-for="folder in folders" :key="folder.id" @click="fetchFolderRecipes(folder.id)"
              class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 cursor-pointer aspect-square">
              <div
                class="relative h-full bg-gradient-to-br from-lime-100 to-green-100 flex items-center justify-center">
                <svg class="w-12 h-12 text-lime-500 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                </svg>
                <button @click.stop="deleteFolder(folder.id)"
                  class="absolute top-1 right-1 bg-white/90 p-1 rounded-full shadow-md hover:bg-red-100 transition-all duration-300">
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
            <p class="text-gray-600 text-lg mb-4">Encara no tens cap carpeta creada.</p>
            <p class="text-gray-500 text-sm mb-6">Crea carpetes per organitzar i compartir les teves receptes
              preferides.</p>
          </div>
        </div>
      </div>

      <!-- Lives programados -->
      <div v-if="showLivesSection && !showLiveForm" class="user-recipes pb-20">
        <h3 class="text-2xl font-bold text-lime-900 mb-8 text-center"> Els meus lives programats</h3>
        <div v-if="scheduledLives.length === 0" class="bg-white rounded-xl p-8 text-center shadow-lg max-w-2xl mx-auto">
          <p class="text-gray-600 text-lg mb-4">Encara no tens cap live programat.</p>
          <div class="mt-8 text-center">
            <button @click="toggleLiveForm"
              class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-lime-400 to-green-500 text-white font-medium rounded-lg shadow-md hover:from-lime-500 hover:to-green-600 focus:outline-none focus:ring-2 focus:ring-lime-300 focus:ring-opacity-50 transition-all duration-300 transform hover:scale-105">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                </path>
              </svg>
              Programar nou live
            </button>
          </div>
        </div>
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 px-4">
          <div v-for="live in scheduledLives" :key="live.id"
            class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <h4 class="text-xl font-semibold text-lime-900 truncate">{{ live.recipe.title }}</h4>
                <span class="px-3 py-1 bg-lime-100 text-lime-800 rounded-full text-sm font-medium">Live</span>
              </div>
              <div class="space-y-3">
                <div class="flex items-center text-gray-600">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                  <span>{{ formatDate(live.dia) }}</span>
                </div>
                <div class="flex items-center text-gray-600">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <span>{{ live.hora }}</span>
                </div>
              </div>
              <div class="mt-6 flex space-x-3">
                <button @click="editLive(live)"
                  class="flex-1 px-4 py-2 bg-gradient-to-r from-lime-400 to-green-500 text-white font-medium rounded-lg shadow-sm hover:from-lime-500 hover:to-green-600 focus:outline-none focus:ring-2 focus:ring-lime-300 focus:ring-opacity-50 transition-all duration-300">
                  <span class="flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                      </path>
                    </svg>
                    Editar
                  </span>
                </button>
                <button @click="deleteLive(live.id)"
                  class="flex-1 px-4 py-2 bg-red-100 text-red-600 font-medium rounded-lg shadow-sm hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-300 focus:ring-opacity-50 transition-all duration-300">
                  <span class="flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                      </path>
                    </svg>
                    Eliminar
                  </span>
                </button>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- Recetas propias -->
      <div v-if="showRecipes && !showLiveForm && !showLivesSection" class="user-recipes pb-20">
        <h3 class="text-2xl font-bold text-lime-900 mb-8 text-center"> Les meves publicacions</h3>
        <div v-if="recipes.length === 0" class="bg-white rounded-xl p-8 text-center shadow-lg max-w-2xl mx-auto">
          <p class="text-gray-600 text-lg mb-4">Encara no tens cap publicació.</p>
        </div>
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 px-4">
          <div v-for="recipe in recipes" :key="recipe.id"
            class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 cursor-pointer">
            <div class="relative">
              <img :src="recipe.image" :alt="recipe.title" class="w-full h-48 object-cover" />
              <div class="absolute top-3 right-3 flex gap-2">
                <button @click="editRecipe(recipe)"
                  class="bg-white/90 p-2 rounded-full shadow-md hover:bg-blue-100 transition-all duration-300">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                  </svg>
                </button>
                <button @click="deleteRecipe(recipe.id)"
                  class="bg-white/90 p-2 rounded-full shadow-md hover:bg-red-100 transition-all duration-300">
                  <img :src="binIcon" alt="Eliminar" class="w-5 h-5" />
                </button>
              </div>
            </div>
            <router-link :to="'/info/' + recipe.id" class="block p-6">
              <h4 class="text-xl font-semibold text-lime-900 mb-2 truncate">{{ recipe.title }}</h4>
              <p class="text-gray-600 text-sm line-clamp-2">{{ recipe.description }}</p>
            </router-link>
          </div>
        </div>
      </div>

      <!-- Edit Recipe Form -->
      <div v-if="showEditRecipeForm"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
        <div
          class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-2 sm:mx-4 p-4 sm:p-6 transform transition-all duration-300 max-h-[90vh] overflow-y-auto my-4">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-lime-900">Editar Recepta</h3>
            <button @click="cancelEditRecipe" class="text-gray-500 hover:text-gray-700 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          <form @submit.prevent="saveEditedRecipe" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
              <!-- Título -->
              <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Títol:</label>
                <input type="text" id="title" v-model="editingRecipe.title" required
                  class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-lime-500 transition-colors" />
              </div>
              <!-- Descripción -->
              <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descripció:</label>
                <input type="text" id="description" v-model="editingRecipe.description" required
                  class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-lime-500 transition-colors" />
              </div>
              <!-- Ingredientes -->
              <div class="md:col-span-2">
                <label for="ingredients" class="block text-sm font-medium text-gray-700 mb-1">Ingredients:</label>
                <textarea id="ingredients" v-model="editingRecipe.ingredientsText" required rows="4"
                  class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-lime-500 transition-colors"></textarea>
              </div>
              <!-- Passos -->
              <div class="md:col-span-2">
                <label for="steps" class="block text-sm font-medium text-gray-700 mb-1">Passos:</label>
                <textarea id="steps" v-model="editingRecipe.instructionsText" required rows="4"
                  class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-lime-500 transition-colors"></textarea>
              </div>
              <!-- Tiempo de preparación -->
              <div>
                <label for="preparation_time" class="block text-sm font-medium text-gray-700 mb-1">Temps de preparació
                  (minuts):</label>
                <input type="number" id="preparation_time" v-model="editingRecipe.prep_time" required
                  class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-lime-500 transition-colors" />
              </div>
              <!-- Tiempo de cocción -->
              <div>
                <label for="cooking_time" class="block text-sm font-medium text-gray-700 mb-1">Temps de cocció
                  (minuts):</label>
                <input type="number" id="cooking_time" v-model="editingRecipe.cook_time" required
                  class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-lime-500 transition-colors" />
              </div>

              <!-- Raciones -->
              <div>
                <label for="servings" class="block text-sm font-medium text-gray-700 mb-1">Racions:</label>
                <input type="number" id="servings" v-model="editingRecipe.servings" required
                  class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-lime-500 transition-colors" />
              </div>
              <!-- Categoría -->
              <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Categoria:</label>
                <select id="category_id" v-model="editingRecipe.category_id" required
                  class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-lime-500 transition-colors">
                  <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
              </div>
              <!-- Cocina -->
              <div>
                <label for="cuisine_id" class="block text-sm font-medium text-gray-700 mb-1">Cocina:</label>
                <select id="cuisine_id" v-model="editingRecipe.cuisine_id" required
                  class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-lime-500 transition-colors">
                  <option v-for="cui in cuisines" :key="cui.id" :value="cui.id">{{ cui.country }}</option>
                </select>
              </div>
              <!-- Imagen -->
              <div class="md:col-span-2">
                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Imatge (URL):</label>
                <input type="text" id="image" v-model="editingRecipe.image"
                  class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-lime-500 transition-colors" />
              </div>
              <!-- Video -->
              <div class="md:col-span-2">
                <label for="video" class="block text-sm font-medium text-gray-700 mb-1">Video (URL):</label>
                <input type="text" id="video" v-model="editingRecipe.video"
                  class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-lime-500 transition-colors" />
              </div>
              <!-- Nutrición -->
              <div class="md:col-span-2 grid grid-cols-2 gap-4">
                <div>
                  <label for="calories" class="block text-sm font-medium text-gray-700 mb-1">Calories:</label>
                  <input type="number" id="calories" v-model="editingRecipe.nutrition.calories"
                    class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-lime-500 transition-colors" />
                </div>
                <div>
                  <label for="protein" class="block text-sm font-medium text-gray-700 mb-1">Proteïnes (g):</label>
                  <input type="number" id="protein" v-model="editingRecipe.nutrition.protein"
                    class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-lime-500 transition-colors" />
                </div>
                <div>
                  <label for="carbs" class="block text-sm font-medium text-gray-700 mb-1">Carbohidrats (g):</label>
                  <input type="number" id="carbs" v-model="editingRecipe.nutrition.carbs"
                    class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-lime-500 transition-colors" />
                </div>
                <div>
                  <label for="fats" class="block text-sm font-medium text-gray-700 mb-1">Greixos (g):</label>
                  <input type="number" id="fats" v-model="editingRecipe.nutrition.fats"
                    class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-lime-500 transition-colors" />
                </div>
              </div>
            </div>
            <div class="flex flex-col sm:flex-row justify-end sm:space-x-4 space-y-3 sm:space-y-0 mt-6">
              <button type="submit"
                class="w-full sm:w-auto px-6 py-2 bg-gradient-to-r from-lime-400 to-green-500 text-white font-medium rounded-lg shadow-md hover:from-lime-500 hover:to-green-600 focus:outline-none focus:ring-2 focus:ring-lime-300 focus:ring-opacity-50 transition-all duration-300">
                Guardar canvis
              </button>
              <button type="button" @click="cancelEditRecipe"
                class="w-full sm:w-auto px-6 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-all duration-300">
                Cancelar
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Sección de recetas que me gustan -->
      <div v-if="showLikedSection" class="user-recipes liked-section pb-20">
        <div class="bg-white rounded-xl p-6 shadow-lg mb-8">
          <h3 class="text-2xl font-bold text-lime-900 mb-4">Receptes que m'agraden</h3>
          <p class="text-gray-600">Aquí trobaràs totes les receptes a les quals has donat like. Pots veure-les,
            desar-les o eliminar-les d'aquesta llista.</p>
        </div>

        <div class="px-4">
          <div v-if="likedRecipes.length === 0" class="bg-white rounded-xl p-8 text-center shadow-lg max-w-2xl mx-auto">
            <p class="text-gray-600 text-lg mb-4">Encara no has donat like a cap recepta.</p>
            <button @click="goToExplore"
              class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-lime-400 to-green-500 text-white font-medium rounded-lg shadow-md hover:from-lime-500 hover:to-green-600 focus:outline-none focus:ring-2 focus:ring-lime-300 focus:ring-opacity-50 transition-all duration-300 transform hover:scale-105">
              Explorar receptes
            </button>
          </div>
          <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="recipe in likedRecipes" :key="recipe.id"
              class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 cursor-pointer">
              <router-link :to="'/info/' + recipe.id" class="absolute inset-0 z-10" style="display:block" />
              <div class="relative">
                <img :src="recipe.image" :alt="recipe.title" class="w-full h-48 object-cover" />
                <button @click="removeLikedRecipe(recipe.id)"
                  class="absolute top-3 right-3 bg-white/90 p-2 rounded-full shadow-md hover:bg-red-100 transition-all duration-300">
                  <img :src="binIcon" alt="Eliminar" class="w-5 h-5" />
                </button>
              </div>
              <div class="p-6">
                <h4 class="text-xl font-semibold text-lime-900 mb-2 truncate">{{ recipe.title }}</h4>
                <p class="text-gray-600 text-sm line-clamp-2 mb-4">{{ recipe.description }}</p>
                <div class="flex justify-between items-center">

                  <router-link :to="'/info/' + recipe.id"
                    class="text-green-600 hover:text-green-800 text-sm font-medium">
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Formulario para programar un nou live -->
      <div v-if="showLiveForm" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6 transform transition-all duration-300">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-lime-900">Programar nou live</h3>
            <button @click="toggleLiveForm" class="text-gray-500 hover:text-gray-700 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          <form @submit.prevent="createLive" class="space-y-6">
            <div>
              <label for="recipe" class="block text-sm font-medium text-gray-700 mb-1">Recepta:</label>
              <select v-model="newLive.recipe_id" id="recipe" required
                class="w-full px-3 py-2 border-2 border-lime-200 rounded-lg focus:outline-none focus:border-lime-400 focus:ring-2 focus:ring-lime-200 transition-all duration-300">
                <option value="" disabled selected>Selecciona una recepta</option>
                <option v-for="recipe in recipes" :key="recipe.id" :value="recipe.id">{{ recipe.title }}</option>
              </select>
            </div>
            <div>
              <label for="dia" class="block text-sm font-medium text-gray-700 mb-1">Dia:</label>
              <input type="date" id="dia" v-model="newLive.dia" :min="minDate" required
                class="w-full px-3 py-2 border-2 border-lime-200 rounded-lg focus:outline-none focus:border-lime-400 focus:ring-2 focus:ring-lime-200 transition-all duration-300" />
            </div>
            <div>
              <label for="hora" class="block text-sm font-medium text-gray-700 mb-1">Hora:</label>
              <input type="time" id="hora" v-model="newLive.hora" required
                class="w-full px-3 py-2 border-2 border-lime-200 rounded-lg focus:outline-none focus:border-lime-400 focus:ring-2 focus:ring-lime-200 transition-all duration-300" />
            </div>
            <div class="flex justify-end pt-4">
              <button type="submit"
                class="px-6 py-2 bg-gradient-to-r from-lime-400 to-green-500 text-white font-medium rounded-lg shadow-md hover:from-lime-500 hover:to-green-600 focus:outline-none focus:ring-2 focus:ring-lime-300 focus:ring-opacity-50 transition-all duration-300">
                Guardar live
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Formulario para editar un live -->
      <div v-if="showEditForm" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6 transform transition-all duration-300">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-lime-900">Editar live</h3>
            <button @click="showEditForm = false" class="text-gray-500 hover:text-gray-700 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          <form @submit.prevent="saveEditedLive" class="space-y-6">
            <div>
              <label for="edit-recipe" class="block text-sm font-medium text-gray-700 mb-1">Recepta:</label>
              <select v-model="editingLive.recipe_id" id="edit-recipe" required
                class="w-full px-3 py-2 border-2 border-lime-200 rounded-lg focus:outline-none focus:border-lime-400 focus:ring-2 focus:ring-lime-200 transition-all duration-300">
                <option value="" disabled>Selecciona una recepta</option>
                <option v-for="recipe in recipes" :key="recipe.id" :value="recipe.id">{{ recipe.title }}</option>
              </select>
            </div>
            <div>
              <label for="edit-dia" class="block text-sm font-medium text-gray-700 mb-1">Dia:</label>
              <input type="date" id="edit-dia" v-model="editingLive.dia" :min="minDate" required
                class="w-full px-3 py-2 border-2 border-lime-200 rounded-lg focus:outline-none focus:border-lime-400 focus:ring-2 focus:ring-lime-200 transition-all duration-300" />
            </div>
            <div>
              <label for="edit-hora" class="block text-sm font-medium text-gray-700 mb-1">Hora:</label>
              <input type="time" id="edit-hora" v-model="editingLive.hora" required
                class="w-full px-3 py-2 border-2 border-lime-200 rounded-lg focus:outline-none focus:border-lime-400 focus:ring-2 focus:ring-lime-200 transition-all duration-300" />
            </div>
            <div class="flex justify-end pt-4">
              <button type="submit"
                class="px-6 py-2 bg-gradient-to-r from-lime-400 to-green-500 text-white font-medium rounded-lg shadow-md hover:from-lime-500 hover:to-green-600 focus:outline-none focus:ring-2 focus:ring-lime-300 focus:ring-opacity-50 transition-all duration-300">
                Guardar canvis
              </button>
            </div>
          </form>
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
    const editingLive = ref({
      id: null,
      recipe_id: '',
      dia: '',
      hora: ''
    });
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
    const editingRecipe = ref(null);
    const showEditRecipeForm = ref(false);

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
      editingLive.value = {
        id: live.id,
        recipe_id: live.recipe_id,
        dia: live.dia,
        hora: live.hora
      };
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

    const goToFormulario = () => {
      router.push('/formulario');
    };

    const editRecipe = async (recipe) => {
      await fetchCategoriesAndCuisines();
      editingRecipe.value = { ...recipe };
      if (!editingRecipe.value.category_id && categories.value.length > 0) {
        editingRecipe.value.category_id = categories.value[0].id;
      }
      if (!editingRecipe.value.cuisine_id && cuisines.value.length > 0) {
        editingRecipe.value.cuisine_id = cuisines.value[0].id;
      }
      editingRecipe.value.ingredientsText = ingredientsArrayToText(recipe.ingredients);
      editingRecipe.value.instructionsText = instructionsArrayToText(recipe.steps);
      showEditRecipeForm.value = true;
      showRecipes.value = false;
    };

    const saveEditedRecipe = async () => {
      try {
        const recipeToSave = { ...editingRecipe.value };
        recipeToSave.ingredients = textToIngredientsArray(editingRecipe.value.ingredientsText);
        recipeToSave.steps = textToInstructionsArray(editingRecipe.value.instructionsText);
        await communicationManager.updateRecipe(editingRecipe.value.id, recipeToSave);
        const index = recipes.value.findIndex(r => r.id === editingRecipe.value.id);
        if (index !== -1) {
          recipes.value[index] = { ...editingRecipe.value };
        }
        popupMessage.value = "Receta actualizada correctamente";
        setTimeout(() => { popupMessage.value = ''; }, 3000);
        showEditRecipeForm.value = false;
        showRecipes.value = true;
        editingRecipe.value = null;
      } catch (error) {
        console.error('Error actualizando receta:', error);
        popupMessage.value = "Error al actualizar la receta";
        setTimeout(() => { popupMessage.value = ''; }, 3000);
      }
    };

    const cancelEditRecipe = () => {
      editingRecipe.value = null;
      showEditRecipeForm.value = false;
      showRecipes.value = true;
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

    // 1. En el setup, añade helpers para convertir arrays a texto y viceversa:

    function ingredientsArrayToText(ingredients) {
      if (!Array.isArray(ingredients)) return '';
      return ingredients.map(ing => {
        if (typeof ing === 'string') return ing;
        return `${ing.quantity || ''} ${ing.unit || ''} ${ing.name || ing}`.trim();
      }).join('\n');
    }
    function textToIngredientsArray(text) {
      return text.split('\n').map(line => {
        const parts = line.trim().split(' ');
        if (parts.length < 2) return { name: line.trim() };
        // Asume formato: cantidad unidad nombre
        const quantity = parts.shift();
        const unit = parts.shift();
        const name = parts.join(' ');
        return { quantity, unit, name };
      });
    }
    function instructionsArrayToText(steps) {
      if (!Array.isArray(steps)) return '';
      return steps.join('\n');
    }
    function textToInstructionsArray(text) {
      return text.split('\n').map(line => line.trim()).filter(Boolean);
    }

    // 1. En el setup, añade los states y la carga de categorías y cocinas:
    const categories = ref([]);
    const cuisines = ref([]);
    const fetchCategoriesAndCuisines = async () => {
      try {
        let cats = await communicationManager.fetchCategories();
        if (cats && cats.data) cats = cats.data; // por si la API devuelve {data: [...]}
        categories.value = Array.isArray(cats) ? cats : [];
        let cuis = await communicationManager.fetchCuisines();
        if (cuis && cuis.data) cuis = cuis.data; // por si la API devuelve {data: [...]}
        cuisines.value = Array.isArray(cuis) ? cuis : [];
      } catch (error) {
        categories.value = [];
        cuisines.value = [];
        console.error('Error cargando categorías o cocinas', error);
      }
    };

    return {
      user: null,
      recipes: [],
      showLivesSection: false,
      showLiveForm: false,
      showEditForm,
      editingLive,
      newLive: {
        recipe_id: '',
        dia: '',
        hora: ''
      },
      minDate: new Date().toISOString().split('T')[0],
      lives: [],
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
      saveEditedLive,
      showLivesSection,
      showScheduledLives,
      hideLivesSection,
      goToExplore,
      goToVerification,
      goToRecipe,
      handleCreateFolderClick,
      goToFormulario,
      editRecipe,
      saveEditedRecipe,
      cancelEditRecipe,
      editingRecipe,
      showEditRecipeForm,
      categories,
      cuisines,
      fetchCategoriesAndCuisines
    };
  }
};
</script>