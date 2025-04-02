import axios from 'axios';

// Configuración base de Axios
const apiClient = axios.create({
  baseURL: 'http://127.0.0.1:8000/api',
  headers: {
    'Content-Type': 'application/json',
  },
  withCredentials: true, 
});
apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);



const communicationManager = {
  fetchRecipes() {
    return apiClient.get('/recipes')
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching recipes:', error);
        throw error;
      });
  },

  fetchCategories() {
    return apiClient.get('/categories')
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching categories:', error);
        throw error;
      });
  },

  fetchCuisines() {
    return apiClient.get('/cuisines')
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching cuisines:', error);
        throw error;
      });
  },
  fetchRecipeDetails(recipeId) {
    // Asegúrate de que recipeId sea string si tu backend lo espera así
    return apiClient.get(`/recipes/${String(recipeId)}`)
      .then(response => response.data)
      .catch(error => {
        if (error.response?.status === 401) {
          throw new Error('Unauthorized - Please login');
        }
        console.error('Error fetching recipe details:', error);
        throw error;
      });
  },
  createRecipe(recipeData) {
    return apiClient.post('/recipes', {
      ...recipeData,
      user_id: localStorage.getItem('user_id') 
    })
      .then(response => response.data)
      .catch(error => {
        console.error('Error creating recipe:', error);
        throw error;
      });
  },

  register(userData) {
    return apiClient.post('/register', userData)
      .then(response => response.data)
      .catch(error => {
        console.error('Error registering user:', error);
        throw error;
      });
  },

  login(userData) {
    return apiClient.post('/login', userData)
      .then(response => response.data)
      .catch(error => {
        console.error('Error logging in:', error);
        throw error;
      });
  },

  // Obtener recetas guardadas
  getSavedRecipes() {
    return apiClient.get('/saved-recipes')
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching saved recipes:', error);
        throw error;
      });
  },

  // Guardar o quitar una receta
  toggleSaveRecipe(recipeId) {
    return apiClient.post(`/saved-recipes/toggle/${recipeId}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error toggling saved recipe:', error);
        throw error;
      });
  },

  //info de cada user
  getUser: async () => {
    try {
      const token = localStorage.getItem('token');
      if (!token) throw new Error("No token found");

      const response = await axios.get('http://127.0.0.1:8000/api/user', {
        headers: { Authorization: `Bearer ${token}` }
      });
      return response.data;
    } catch (error) {
      throw error;
    }
  },

  // Métodos nuevos para obtener categorías y cocinas
  getCategories() {
    return apiClient.get('/categories')
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching categories:', error);
        throw error;
      });
  },

  // Filtrar recetas por categoría
  fetchRecipesByCategory(categoryId) {
    return apiClient.get(`/filterByCategory/${categoryId}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching recipes by category:', error);
        throw error;
      });
  },

  fetchUsers() {
    return apiClient.get('/getAllUsers')
      .then(response => response.data.users)
      .catch(error => {
        console.error('Error fetching users:', error);
        throw error;
      });
  },


  // Obtener recetas filtradas por el ID del usuario
  fetchRecipesByUser(userId) {
    return apiClient.get(`/recipes/filterByUser/${userId}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching recipes by user:', error);
        throw error;
      });
  },

  // Obtener recetas filtradas por el ID de la cocina
  fetchRecipesByCuisine(cuisineId) {
    return apiClient.get(`/filterByCuisine/${cuisineId}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching recipes by cuisine:', error);
        throw error;
      });
  },
  // Obtener todos los tiempos disponibles
  getAllTimes() {
    return apiClient.get('/times')
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching times:', error);
        throw error;
      });
  },

  // Filtrar recetas por tiempo total (preparación + cocción)
  fetchRecipesByTime(time) {
    return apiClient.get(`/filterByTime/${time}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error filtering recipes by time:', error);
        throw error;
      });
  },
  updateProfile(userData) {
    return apiClient.post('/updatePerfile', userData)
      .then(response => response.data)
      .catch(error => {
        console.error('Error updating profile:', error);
        throw error;
      });
  },

  async updateProfilePicture(img) {
    return apiClient.put('/updateProfilePicture', img)
      .then(response => {
        console.log('se ha subido correctamente');
        return response.data;
      })
      .catch(error => {
        console.error('Error updating profile picture:', error);
        throw error;
      });
  },

  changePassword(passwordData) {

    return apiClient.post('/cambiarContra', passwordData)
      .then(response => response.data)
      .catch(error => {
        console.error('Error changing password:', error);
        throw error;
      });
  },
  getUserRecipes(id) {
    return apiClient.get(`/user/${id}/recipes`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching user recipes:', error);
        throw error;
      });

  },


  fetchComments(recipeId) {
    return apiClient.get(`/recipes/${recipeId}/comments`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching comments:', error);
        throw error;
      });
  },
  // Eliminar receta
  deleteRecipe(recipeId) {
    return apiClient.delete(`/recipes/${recipeId}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error eliminando receta:', error);
        throw error;
      });
  },


  // Agregar un comentario a una receta
  addComment(recipeId, commentText) {
    return apiClient.post(`/recipes/${recipeId}/comment`, { comment: commentText })  // <-- Enviar 'comment' en lugar de 'text'
      .then(response => response.data)
      .catch(error => {
        console.error('Error adding comment:', error);
        throw error;
      });
  },
  createFolder(folderName) {
    return apiClient.post('/folders', { name: folderName }) 
      .then(response => response.data)
      .catch(error => {
        console.error('Error creando carpeta:', error.response ? error.response.data : error.message); 
        throw error;
      });
  },
  // Obtener las recetas de una carpeta
  fetchFolderRecipes(folderId) {
    return apiClient.get(`/folders/${folderId}/recipes`)
      .then(response => response.data.recipes) 
      .catch(error => {
        console.error('Error fetching folder recipes:', error);
        throw error;
      });
  },


  // Guardar una receta en una carpeta
  saveRecipeToFolder(folderId, recipeId) {
    return apiClient.post(`/folders/${folderId}/recipes/${recipeId}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error saving recipe to folder:', error);
        throw error;
      });
  },
  removeRecipeFromFolder: async (recipeId, folderId) => {
    try {
      // Asegúrate de que la URL esté correctamente construida
      const response = await apiClient.delete(`/folders/${folderId}/recipes/${recipeId}`);
      return response.data;
    } catch (error) {
      console.error('Error eliminando receta de la carpeta', error);
      throw error;
    }
  },

  // Obtener las recetas guardadas por el usuario
  fetchUserSavedRecipes() {
    return apiClient.get('/user/saved-recipes')
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching user saved recipes:', error);
        throw error;
      });
  },
  // Obtener las carpetas del usuario
  fetchUserFolders() {
    return apiClient.get('/folders')
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching user folders:', error);
        throw error;
      });
  },
  // Método para eliminar una carpeta
  deleteFolder(folderId) {
    return apiClient.delete(`/folders/${folderId}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error eliminando carpeta:', error);
        throw error;
      });
  },

  // Eliminar receta
  deleteRecipe(recipeId) {
    return apiClient.delete(`/recipes/${recipeId}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error eliminando receta:', error);
        throw error;
      });
  },
  saveRecipeToFolder(folderId, recipeId) {
    return apiClient.post(`/folders/${folderId}/recipes/${recipeId}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error saving recipe to folder:', error.response ? error.response.data : error.message);
        throw error;
      });
  },
  // Guardar o quitar una receta de las recetas guardadas
  toggleSaveRecipe(recipeId) {
    return apiClient.post(`/saved-recipes/toggle/${recipeId}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error toggling saved recipe:', error);
        throw error;
      });
  },
  toggleLike(recipeId) {
    return apiClient.post(`/recipes/${recipeId}/like`)
        .then(response => response.data)
        .catch(error => {
            console.error('Error toggling like:', error);
            throw error;
        });
},

getLikes(recipeId) {
    return apiClient.get(`/recipes/${recipeId}/likes`)
        .then(response => response.data)
        .catch(error => {
            console.error('Error getting likes:', error);
            throw error;
        });
},
  logout() {
    const authStore = useAuthStore(); 
    try {
      return apiClient.post('/logout')
        .then(() => {
          authStore.clearAuth();
          localStorage.removeItem('token');
          window.location.href = '/login'; 
        })
        .catch(error => {
          console.error('Error al cerrar sesión:', error);
        });
    } catch (error) {
      console.error('Error al intentar cerrar sesión:', error);
    }
  }
}  

export default communicationManager;
