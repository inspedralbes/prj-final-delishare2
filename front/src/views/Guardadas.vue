<template>
  <!-- Popup de notificación personalizado -->
  <div v-if="popupMessage" class="popup-notification">
    {{ popupMessage }}
  </div>

  <div class="guardades-container">
    
    <!-- Botones de navegación -->
    <div class="tabs">
      <button :class="{ active: activeTab === 'guardades' }" @click="activeTab = 'guardades'">
        Guardades
      </button>
      <button :class="{ active: activeTab === 'carpetes' }" @click="activeTab = 'carpetes'">
        Les meves carpetes
      </button>
    </div>


    <!-- Sección de receptes guardades -->
    <div v-if="activeTab === 'guardades'" class="guardades">
      <h1 class="title">Receptes guardades</h1>
      <div v-if="loadingGuardades" class="loading">Carregant...</div>
      <div v-else>
        <ul class="recipe-list">
          <li v-for="recipe in savedRecipes" :key="recipe.id" class="recipe-item">
            <RecipeCard
              :recipeId="recipe.id"
              :title="recipe.title"
              :image="recipe.image"
              :description="recipe.description"
            />
            <!-- <button @click="removeSavedRecipe(recipe.id)" class="remove-btn">
              Elimina
            </button> -->
            <button @click="removeSavedRecipe(recipe.id)" class="delete-btn">
            <img :src="binIcon" alt="Eliminar" class="delete-icon" />
          </button>
          </li>
        </ul>
        <p v-if="savedRecipes.length === 0" class="no-recipes">
          No tens receptes guardades.
        </p>
      </div>
    </div>

    <!-- Sección de carpetes -->
    <div v-if="activeTab === 'carpetes'" class="carpetes">
      <h1 class="title">Les meves carpetes</h1>

      <!-- Vista de carpeta única -->
      <div v-if="selectedFolder">
        <button @click="goBackFromFolder" class="back-btn">← Tornar enrere</button>
        <h3 class="title">Receptes a la carpeta "{{ selectedFolder.name }}"</h3>
        <ul class="recipe-list" v-if="selectedFolderRecipes.length > 0">
          <li v-for="recipe in selectedFolderRecipes" :key="recipe.id" class="recipe-item">
            <RecipeCard
              :recipeId="recipe.id"
              :title="recipe.title"
              :image="recipe.image"
              :description="recipe.description"
            />
           
            <button @click="removeRecipeFromFolder(recipe.id, selectedFolder.id)" class="delete-btn">
            <img :src="binIcon" alt="Eliminar" class="delete-icon" />
          </button>
          </li>
        </ul>
        <p v-else class="no-recipes">
          No hi ha receptes en aquesta carpeta.
        </p>
      </div>

      <div v-else>
        <div>
  <button v-if="!showCreateFolderInput" @click="showCreateFolderInput = true" class="create-folder-btn">
    Crear carpeta
  </button>
  <div v-if="showCreateFolderInput" class="create-folder-input" style="max-width: 250px; margin: 0 auto;">
    <input type="text" v-model="newFolderName" placeholder="Nom de la carpeta" style="max-width: 150px;"/>
    <div class="button-group">
      <button @click="createFolder" class="create-folder-btn">Guardar</button>
      <button @click="showCreateFolderInput = false" class="cancel-folder-btn">Cancelar</button>
    </div>
  </div>
</div>



        <div v-if="folders.length > 0" class="folders">
          <div
            class="folder-card"
            v-for="folder in folders"
            :key="folder.id"
            @click="fetchFolderRecipes(folder.id)"
          >
            <div class="folder-image-container">
              <img src="@/assets/images/folder2.png" alt="Folder" class="folder-icon" />
              <div class="folder-overlay">
                <span class="folder-name">{{ folder.name }}</span>
                <button @click.stop="deleteFolder(folder.id)" class="delete-folder-btn">
                  <img src="@/assets/images/bin.svg" alt="Delete" />
                </button>
              </div>
            </div>
          </div>
        </div>
        <p v-if="folders.length === 0" class="no-folders">
          No tens cap carpeta.
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import communicationManager from '@/services/communicationManager';
import { ref, onMounted } from 'vue';
import RecipeCard from '@/components/RecipeCard.vue';
import binIcon from '@/assets/images/bin.svg';

export default {
  components: {
    RecipeCard
  },
  setup() {
    const activeTab = ref('guardades');
    const loadingGuardades = ref(true);
    const savedRecipes = ref([]);
    const folders = ref([]);
    const selectedFolder = ref(null);
    const selectedFolderRecipes = ref([]);
    const showCreateFolderInput = ref(false);
    const newFolderName = ref('');
    const popupMessage = ref('');

    const showPopup = (message) => {
      popupMessage.value = message;
      setTimeout(() => {
        popupMessage.value = '';
      }, 3000);
    };

    const fetchSavedRecipes = async () => {
      loadingGuardades.value = true;
      try {
        const response = await communicationManager.getSavedRecipes();
        savedRecipes.value = response;
      } catch (error) {
        console.error('Error al obtenir receptes guardades:', error);
      } finally {
        loadingGuardades.value = false;
      }
    };

    const removeSavedRecipe = async (recipeId) => {
      try {
        await communicationManager.toggleSaveRecipe(recipeId);
        savedRecipes.value = savedRecipes.value.filter(r => r.id !== recipeId);
      } catch (error) {
        console.error('Error en eliminar la recepta:', error);
      }
    };

    const fetchUserFolders = async () => {
      try {
        const userFolders = await communicationManager.fetchUserFolders();
        folders.value = userFolders;
      } catch (error) {
        console.error('Error carregant carpetes', error);
      }
    };

    const fetchFolderRecipes = async (folderId) => {
      try {
        const recipesFromFolder = await communicationManager.fetchFolderRecipes(folderId);
        selectedFolderRecipes.value = recipesFromFolder;
        selectedFolder.value = folders.value.find(folder => folder.id === folderId);
      } catch (error) {
        console.error('Error obtenint receptes de la carpeta', error);
      }
    };

    const createFolder = async () => {
      if (newFolderName.value.trim() === '') return;
      try {
        await communicationManager.createFolder(newFolderName.value);
        showPopup('La carpeta s\'ha creat correctament');
        showCreateFolderInput.value = false;
        newFolderName.value = '';
        await fetchUserFolders();
      } catch (error) {
        console.error('Error creant la carpeta', error);
        showPopup('Error creant la carpeta');
      }
    };

    const deleteFolder = async (folderId) => {
      try {
        await communicationManager.deleteFolder(folderId);
        showPopup('La carpeta s\'ha eliminat correctament');
        await fetchUserFolders();
        if (selectedFolder.value && selectedFolder.value.id === folderId) {
          selectedFolder.value = null;
          selectedFolderRecipes.value = [];
        }
      } catch (error) {
        console.error('Error eliminant la carpeta', error);
        showPopup('Error en eliminar la carpeta');
      }
    };

    const removeRecipeFromFolder = async (recipeId, folderId) => {
      try {
        await communicationManager.removeRecipeFromFolder(recipeId, folderId);
        selectedFolderRecipes.value = selectedFolderRecipes.value.filter(
          recipe => recipe.id !== recipeId
        );
      } catch (error) {
        console.error('Error eliminant la recepta de la carpeta', error);
      }
    };

    const goBackFromFolder = () => {
      selectedFolder.value = null;
      selectedFolderRecipes.value = [];
    };

    onMounted(() => {
      fetchSavedRecipes();
      fetchUserFolders();
    });

    return {
      activeTab,
      loadingGuardades,
      savedRecipes,
      removeSavedRecipe,
      folders,
      selectedFolder,
      selectedFolderRecipes,
      showCreateFolderInput,
      newFolderName,
      popupMessage,
      fetchFolderRecipes,
      createFolder,
      deleteFolder,      binIcon,

      removeRecipeFromFolder,
      goBackFromFolder
    };
  }
};
</script>

<style scoped>
.guardades-container {
  padding: 20px;
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

.tabs {
  display: flex;
  gap: 20px;
  margin-bottom: 20px;
  justify-content: center;
  margin-top: 20px;
}

.tabs button {
  padding: 10px 20px;
  border: none;
  background-color: #e0e0e0;
  cursor: pointer;
  border-radius: 5px;
  transition: background-color 0.3s ease;
}

.tabs button.active {
  background-color: #0c0636;
  color: white;
}

.title {
  text-align: center;
  font-size: 32px;
  margin-bottom: 20px;
  color: #343330;
}

.loading {
  font-size: 18px;
  color: #444;
  text-align: center;
  padding: 20px;
  font-weight: bold;
}

.recipe-list {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
  justify-items: center;
  padding: 20px 10px;
  list-style: none;
}

.recipe-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  width: 100%;
  max-width: 250px;
  margin-bottom: 20px;
}

.remove-btn {
  background-color: #0c0636;
  color: white;
  border: none;
  padding: 8px 12px;
  border-radius: 20px;
  font-size: 14px;
  cursor: pointer;
  margin-top: 10px;
  transition: background-color 0.3s ease;
}

.remove-btn:hover {
  background-color: #322b5f;
}

/* Mensajes de "no hay..." */
.no-recipes,
.no-folders {
  font-size: 18px;
  color: #888;
  margin-top: 20px;
  text-align: center;
}

/* Botón para crear carpeta */
.create-folder-btn {
  background-color: #0c0636;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 20px;
  font-size: 14px;
  cursor: pointer;
  margin-bottom: 20px;
  display: block;
  margin-left: auto;
  margin-right: auto;
  transition: background-color 0.3s ease;
}

.create-folder-btn:hover {
  background-color: #322b5f;
}

.create-folder-input {
  display: flex;
  gap: 10px;
  justify-content: center;
  
}

.create-folder-input input {
  border: 1px solid #ccc;
  border-radius: 4px;

}

.create-folder-input button {
  
  background-color: #0c0636;
  color: white;
  border: none;
  padding: 6px 10px;
  border-radius: 20px;
  font-size: 11px;
  cursor: pointer;
  transition: background-color 0.3s ease;

}

.create-folder-input button:hover {
  background-color: #322b5f;
}

/* Estilos para las carpetes */
.folders {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
  padding: 20px 10px;
}

.folder-card {
  cursor: pointer;
  max-width: 250px;
  margin: 0 auto;
}

/* Contenedor de la imagen y overlay */
.folder-image-container {
  position: relative;
  width: 100%;
}

.folder-icon {
  width: 100%;
  display: block;
  border-radius: 8px;
}

/* Overlay con nombre y botón (absoluto sobre la imagen) */
.folder-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  color: rgb(0, 0, 0);
  padding: 30px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}


.folder-name {
  font-size: 18px;
  font-weight: bold;
}

.delete-folder-btn {
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 0;
}

.delete-folder-btn img {
  width: 24px;
  height: 24px;
}

/* Contenedor de receptes dins d'una carpeta */
.folder-recipes {
  padding: 20px 10px;
}

/* Botón para volver a la vista de carpetes */
.back-btn {
  background-color: #0c0636;
  color: white;
  border: none;
  padding: 8px 12px;
  border-radius: 20px;
  font-size: 14px;
  cursor: pointer;
  margin-bottom: 20px;
  transition: background-color 0.3s ease;
}

.back-btn:hover {
  background-color: #322b5f;
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


/* Media Queries */
@media (min-width: 600px) {
  .recipe-item {
    max-width: 280px;
  }
  
  .remove-btn {
    font-size: 16px;
    padding: 10px 14px;
  }
}

@media (min-width: 1024px) {
  .recipe-item {
    max-width: 300px;
  }
  
  .remove-btn {
    font-size: 18px;
    padding: 12px 16px;
  }
}
</style>
