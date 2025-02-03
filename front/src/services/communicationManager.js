import axios from 'axios';

// Configuración base de Axios
const apiClient = axios.create({
  baseURL: 'http://127.0.0.1:8000/api',
  headers: {
    'Content-Type': 'application/json',
  },
  withCredentials: true, // Habilita el envío de cookies
});

// Interceptor para agregar el token a cada solicitud
apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token'); // Obtiene el token
    if (token) {
      config.headers.Authorization = `Bearer ${token}`; // Agrega el token en los headers
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
    return apiClient.get(`/recipes/${recipeId}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching recipe details:', error);
        throw error;
      });
  },

  createRecipe(recipeData) {
    return apiClient.post('/recipes', {
      ...recipeData,
      user_id: localStorage.getItem('user_id') // Obtiene el ID del usuario desde el almacenamiento
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

  // Método para dar like
  likeRecipe(recipeId) {
    return apiClient.post(`/recipes/${recipeId}/like`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error liking recipe:', error);
        throw error;
      });
  },

  // Método para quitar like
  unlikeRecipe(recipeId) {
    return apiClient.post(`/recipes/${recipeId}/unlike`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error unliking recipe:', error);
        throw error;
      });
  },

  getUser: async () => {
    try {
      const token = localStorage.getItem('token');
      if (!token) throw new Error("No token found");

      const response = await axios.get('http://127.0.0.1:8000/api/user', {
          headers: { Authorization: `Bearer ${token}` }
      });
      return response.data;
    } catch (error) {
      console.error("Error fetching user:", error);
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
  fetchCuisines() {
    return apiClient.get('/cuisines')
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching cuisines:', error);
        throw error;
      });
  },
  
  // Actualiza la función `fetchRecipesByCuisine` en el manager para que también funcione:
  
  fetchRecipesByCuisine(cuisineId) {
    return apiClient.get(`/filterByCuisine/${cuisineId}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching recipes by cuisine:', error);
        throw error;
      });
  },
  

  
  
  
};

export default communicationManager;
