import axios from 'axios';
import io from 'socket.io-client';

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

let socket = null;
let isSocketConnected = false;

const connectSocket = (userId) => {
  if (!socket) {
    socket = io(process.env.VUE_APP_SOCKET_URL || 'https://delishare.cat/socket.io/', {
      transports: ['websocket', 'polling'],
      auth: {
        token: localStorage.getItem('token')
      }
    });

    socket.on('connect', () => {
      isSocketConnected = true;
      console.log('🔌 Conectado al servidor de sockets');
      if (userId) {
        socket.emit('authenticate', userId);
      }
    });

    socket.on('disconnect', () => {
      isSocketConnected = false;
      console.log('🔌 Desconectado del servidor de sockets');
    });

    socket.on('connect_error', (err) => {
      console.error('Error de conexión con Socket.IO:', err);
    });
  }
};


const communicationManager = {
  downloadPDF(recipeId) {
    return apiClient.get(`/recipes/${recipeId}/download`, {
      responseType: 'blob',
    })
      .then(response => {
        const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `receta_${recipeId}.pdf`);
        document.body.appendChild(link);
        link.click();
        link.remove();
      })
      .catch(error => {
        console.error('Error al descargar el PDF:', error);
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
  deleteCuisine(id) {
    return apiClient.delete(`/cuisines/${id}`)
      .then(response => response.data)
      .catch(error => {
        console.error(`Error deleting cuisine with ID ${id}:`, error);
        throw error;
      });
  },
  createCuisine(cuisineData) {
    return apiClient.post('/cuisines', cuisineData)
      .then(response => response.data)
      .catch(error => {
        console.error('Error creando cocina:', error.response?.data || error.message);
        throw error;
      });
  },
  // En tu archivo communicationManager.js
getRecipeSteps(recipeId) {
  return apiClient.get(`/recipes/${recipeId}/steps`)
    .then(response => response.data)
    .catch(error => {
      console.error('Error fetching recipe steps:', error);
      throw error;
    });
},
  sendVerificationRequest(message) {
    return apiClient.post('/send-verification', { message })
      .then(response => response.data)
      .catch(error => {
        console.error('Error al enviar la solicitud de verificación:', error.response?.data || error.message);
        throw error;
      });
  },
  
  deleteRecipe(id) {
    return apiClient.delete(`/recipes/${id}`)
      .then(response => response.data)
      .catch(error => {
        console.error(`Error al eliminar la receta con ID ${id}:`, error);
        throw error;
      });
  }
  ,
  // Función para obtener todas las recetas
  fetchRecipes() {
    return apiClient.get('/recipes')
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching recipes:', error);
        throw error;
      });
  },

  // Función para eliminar una receta por su ID
  deleteRecipe(id) {
    return apiClient.delete(`/recipes/${id}`)
      .then(response => response.data)
      .catch(error => {
        console.error(`Error al eliminar la receta con ID ${id}:`, error);
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
  updateUserRole(userId, role) {
    return apiClient.put(`/usuarios/${userId}/rol`, { role })
      .then(response => response.data)
      .catch(error => {
        console.error(`Error al actualizar el rol del usuario ${userId}:`, error.response?.data || error.message);
        throw error;
      });
  },
  
  fetchAllUsers() {
    return apiClient.get('/users')
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching all users:', error);
        throw error;
      });
  },
  deleteUser(userId) {
    return apiClient.delete(`/users/${userId}`)
      .then(response => {
        console.log(`Usuario con ID ${userId} eliminado correctamente`);
        return response.data;
      })
      .catch(error => {
        console.error(`Error al eliminar el usuario con ID ${userId}:`, error);
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
        if (error.response && error.response.status === 422) {
          const validationErrors = error.response.data.errors;
          alert(Object.values(validationErrors).flat().join('\n'));
          // Ya está manejado, no lo volvemos a lanzar
          return null;
        } else {
          // Para errores inesperados sí mostramos algo
          alert('Error inesperado en el registro. Intenta de nuevo.');
          console.error('Error inesperado en el registro:', error);
          return null;
        }
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

      const response = await axios.get('https://delishare.cat/api/user', {
        headers: { Authorization: `Bearer ${token}` }
      });
      return response.data;
    } catch (error) {
      throw error;
    }
  },
  async fetchNotifications() {
    try {
      const response = await apiClient.get('/notifications');
      return response.data;
    } catch (error) {
      console.error('Error al obtener notificaciones:', error);
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
  deleteCategory(id) {
    return apiClient.delete(`/categories/${id}`)
      .then(response => response.data)
      .catch(error => {
        console.error(`Error deleting category with ID ${id}:`, error);
        throw error;
      });
  },

  // Método para crear una categoría (por si lo necesitas)
  createCategory(categoryData) {
    return apiClient.post('/categories', categoryData)
      .then(response => response.data)
      .catch(error => {
        console.error('Error creating category:', error);
        throw error;
      });
  },
  createCategory(categoryData) {
    return apiClient.post('/categories', categoryData)
      .then(response => response.data)
      .catch(error => {
        console.error('Error creating category:', error.response?.data || error.message);
        throw error;
      });
  },

  // Notificaciones
  async getUserNotifications() {
    try {
      const response = await apiClient.get('/notifications');
      return response.data;
    } catch (error) {
      console.error('Error obteniendo notificaciones:', error);
      throw error;
    }
  },

  async markNotificationAsRead(id) {
    try {
      const response = await apiClient.put(`/notifications/${id}/read`);

      // Sincronizar con el servidor de sockets si está conectado
      if (socket && isSocketConnected) {
        socket.emit('markNotificationAsRead', id);
      }

      return response.data;
    } catch (error) {
      console.error('Error marcando notificación como leída:', error);
      throw error;
    }
  },

  async createNotification(data) {
    try {
      // Primero crear en el backend PHP
      const response = await apiClient.post('/notifications', data);

      // Luego enviar por sockets si está conectado
      if (socket && isSocketConnected) {
        socket.emit('createNotification', {
          recipientId: data.user_id,
          message: response.data.message,
          recipeId: data.recipe_id,
          type: data.type,
          triggeredBy: data.triggered_by // Asumiendo que esto viene del store de autenticación
        });
      }

      return response.data;
    } catch (error) {
      console.error('Error creando notificación:', error);
      throw error;
    }
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

  fetchIngredients() {
    return apiClient.get('/ingredients')
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching ingredients:', error);
        throw error;
      });
  },
  fetchRecipesByIngredients(ingredients) {
    return apiClient.post('/recipes/filter-by-ingredients', { ingredients })
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching recipes by ingredients:', error);
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
  getAllComments() {
    return apiClient.get('/comments')
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching all comments:', error);
        throw error;
      });
  },
  deleteComment(recipeId, commentText) {
    return apiClient.delete(`/recipes/${recipeId}/comments`, {
      data: { comment: commentText }
    })
      .then(response => response.data)
      .catch(error => {
        console.error('Error eliminando comentario:', error);
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
  // Añade esto en tu objeto communicationManager
  getUserLikedRecipes: async () => {
    try {
      const response = await apiClient.get('/user/liked-recipes');
      return response.data;
    } catch (error) {
      console.error('Error fetching liked recipes:', error.response?.data?.message || error.message);

      // Manejo específico para errores de autenticación
      if (error.response?.status === 401) {
        throw new Error('Debes iniciar sesión para ver tus recetas likeadas');
      }

      throw error;
    }
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
  },
  fetchCuisinesAndCategories() {
    return apiClient.get('/recommendations/options')
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching options:', error);
        throw error;
      });
  },
  fetchRecipesByIngredients(ingredients) {
    // Validación antes de enviar la solicitud
    if (!Array.isArray(ingredients) || ingredients.length === 0) {
      return Promise.reject(new Error('Debe proporcionar al menos un ingrediente'));
    }
  
    return apiClient.post('/recipes/filter-by-ingredients', { ingredients })
      .then(response => {
        // Aquí podrías añadir más lógica si es necesario (como mostrar un mensaje si no se encuentran recetas)
        return response.data;
      })
      .catch(error => {
        // Manejo de errores mejorado
        console.error('Error fetching recipes by ingredients:', error);
        // Puedes lanzar un error más controlado o manejarlo de otra manera:
        throw new Error('No se pudieron obtener las recetas. Por favor, intente nuevamente.');
      });
  }
  ,
  fetchUserPreferences() {
    return apiClient.get('/recommendations/preferences')
      .then(response => response.data)
      .catch(error => {
        if (error.response && error.response.status === 404) {
          return { data: null }; // No hay preferencias guardadas
        }
        console.error('Error fetching preferences:', error);
        throw error;
      });
  },

  savePreferences(data) {
    return apiClient.post('/recommendations/preferences', data)
      .then(response => response.data)
      .catch(error => {
        console.error('Error saving preferences:', error);
        throw error;
      });
  },

  getRecommendedRecipes() {
    return apiClient.get('/recipes/recommended')
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching recommended recipes:', error);
        throw error;
      });
  },

  // Obtener todos los lives disponibles
  getLives() {
    return apiClient.get('/lives')
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching lives:', error);
        const status = error.response?.status;
        if (status === 404) throw new Error('Endpoint no encontrado');
        if (status === 500) throw new Error('Error del servidor al obtener lives');
        throw new Error(error.response?.data?.message || error.message);
      });
  },

  // Crear un nuevo live (solo chefs)
  createLive(liveData) {
    return apiClient.post('/lives', liveData)
      .then(response => response.data)
      .catch(error => {
        if (error.response?.status === 403) {
          throw new Error('Solo los chefs pueden crear lives');
        }
        console.error('Error creando live:', error);
        throw error;
      });
  },

  // Obtener detalles de un live específico
  getLiveDetails(liveId) {
    return apiClient.get(`/lives/${liveId}`)
      .then(response => response.data)
      .catch(error => {
        if (error.response?.status === 404) {
          throw new Error('Live no encontrado');
        }
        console.error('Error obteniendo detalles del live:', error);
        throw error;
      });
  },

  // Actualizar un live (solo chef dueño)
  updateLive(liveId, updateData) {
    return apiClient.put(`/lives/${liveId}`, updateData)
      .then(response => response.data)
      .catch(error => {
        if (error.response?.status === 403) {
          throw new Error('No tienes permiso para actualizar este live');
        }
        if (error.response?.status === 404) {
          throw new Error('Live no encontrado');
        }
        console.error('Error actualizando live:', error);
        throw error;
      });
  },

  // Obtener los lives del chef autenticado
  async getChefLives() {
    try {
      const response = await apiClient.get('/mis-lives');
      return response.data;
    } catch (error) {
      console.error('Error fetching chef lives:', error);
      throw error;
    }
  },

  // Obtener los lives de un chef específico por su ID
  getChefLivesByUserId: async (userId) => {
    try {
      const response = await apiClient.get(`/chef/${userId}/lives`);
      return response.data?.data || response.data || [];
    } catch (error) {
      console.error('Error fetching chef lives:', error);
      if (error.response?.status === 404) {
        return []; // Retorna array vacío si no hay lives
      }
      throw error;
    }
  },

  // Obtener información de un usuario específico
  getUserInfo: async (userId) => {
    try {
      const token = localStorage.getItem('token');
      if (!token) throw new Error("No token found");

      const response = await axios.get(`http://127.0.0.1:8000/api/userInfo/${userId}`, {
        headers: { Authorization: `Bearer ${token}` }
      });
      return response.data;
    } catch (error) {
      throw error;
    }
  },

  // Obtener los lives de un usuario específico
  async getUserLives(userId) {
    try {
      const response = await apiClient.get(`/users/${userId}/lives`);
      return response.data;
    } catch (error) {
      console.error('Error fetching user getUserLives:', error);
      throw error;
    }
  },
  // Eliminar un live (solo chef dueño)
  async deleteLive(liveId) {
    try {
      const response = await apiClient.delete(`/lives/${liveId}`);
      return response.data;
    } catch (error) {
      console.error('Error eliminando live:', error);
      throw new Error(error.response?.data?.message || 'Error al eliminar el live');
    }
  },

  // Notificaciones
  async getUserNotifications() {
    try {
      const response = await apiClient.get('/notifications');
      return response.data;
    } catch (error) {
      console.error('Error obteniendo notificaciones:', error);
      throw error;
    }
  },

  async markNotificationAsRead(id) {
    try {
      const response = await apiClient.put(`/notifications/${id}/read`);
      
      // Sincronizar con el servidor de sockets si está conectado
      if (socket && isSocketConnected) {
        socket.emit('markNotificationAsRead', id);
      }
      
      return response.data;
    } catch (error) {
      console.error('Error marcando notificación como leída:', error);
      throw error;
    }
  },

  async createNotification(data) {
    try {
      const response = await apiClient.post('/notifications', data);
      
      // Enviar por sockets si está conectado
      if (socket && isSocketConnected) {
        socket.emit('createNotification', {
          recipientId: data.user_id,
          message: response.data.message,
          recipeId: data.recipe_id,
          type: data.type,
          triggeredBy: data.triggered_by
        });
      }
      
      return response.data;
    } catch (error) {
      console.error('Error creando notificación:', error);
      throw error;
    }
  },

  // Socket.IO
  initSocket(userId) {
    connectSocket(userId);
    return socket;
  },

  disconnectSocket() {
    if (socket) {
      socket.disconnect();
      socket = null;
      isSocketConnected = false;
    }
  },

  getSocket() {
    return socket;
  }
};
export default communicationManager;
