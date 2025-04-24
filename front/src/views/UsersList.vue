<template>
    <div class="users-container">
      <h1 class="text-2xl font-bold mb-4">Lista de Usuarios</h1>
      
      <div v-if="loading" class="loading-spinner">
        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-500"></div>
        <p class="mt-2">Cargando usuarios...</p>
      </div>
      
      <div v-else-if="error" class="error-message bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <p>{{ error }}</p>
        <button @click="fetchUsers" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded mt-2">
          Reintentar
        </button>
      </div>
      
      <div v-else>
        <div class="search-box mb-4">
          <input 
            type="text" 
            v-model="searchTerm" 
            placeholder="Buscar usuarios..." 
            class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>
        
        <div v-if="filteredUsers.length === 0" class="no-results p-4 bg-gray-100 rounded text-center">
          <p>No se encontraron usuarios con ese criterio de búsqueda.</p>
        </div>
        
        <div v-else class="users-list grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div 
            v-for="user in filteredUsers" 
            :key="user.id" 
            class="user-card bg-white p-4 rounded shadow hover:shadow-md transition-shadow"
          >
            <div class="user-avatar mb-3">
              <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center text-blue-500 mx-auto">
                <span class="text-xl font-bold">{{ getUserInitials(user.name) }}</span>
              </div>
            </div>
            <h2 class="text-lg font-semibold text-center">{{ user.name }}</h2>
            <p class="text-gray-600 text-center mb-2">{{ user.email }}</p>
            <div class="user-actions flex justify-center space-x-2 mt-3">
              <button 
                @click="deleteUser(user.id)" 
                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm"
              >
                Eliminar
              </button>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Modal de confirmación -->
      <div v-if="showConfirmModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
          <h3 class="text-lg font-semibold mb-4">Confirmar eliminación</h3>
          <p>¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.</p>
          <div class="flex justify-end space-x-3 mt-6">
            <button 
              @click="cancelDelete" 
              class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded"
            >
              Cancelar
            </button>
            <button 
              @click="confirmDelete" 
              class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded"
            >
              Eliminar
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import communicationManager from '@/services/communicationManager';
  
  export default {
    name: 'UsersList',
    data() {
      return {
        users: [],
        loading: true,
        error: null,
        searchTerm: '',
        showConfirmModal: false,
        userToDelete: null
      };
    },
    computed: {
      filteredUsers() {
        if (!this.searchTerm) {
          return this.users;
        }
        
        const searchLower = this.searchTerm.toLowerCase();
        return this.users.filter(user => 
          user.name.toLowerCase().includes(searchLower) || 
          user.email.toLowerCase().includes(searchLower)
        );
      }
    },
    mounted() {
      this.fetchUsers();
    },
    methods: {
      async fetchUsers() {
        this.loading = true;
        this.error = null;
        
        try {
          // Verificar si hay token de autenticación
          const token = localStorage.getItem('token');
          
          if (!token) {
            this.error = 'No se encontró el token de autenticación. Por favor, inicia sesión.';
            this.loading = false;
            return;
          }
          
          // Usar communicationManager para obtener los usuarios
          this.users = await communicationManager.fetchAllUsers();
        } catch (err) {
          console.error('Error fetching users:', err);
          
          if (err.response && err.response.status === 401) {
            this.error = 'Tu sesión ha expirado o no tienes autorización. Por favor, vuelve a iniciar sesión.';
          } else {
            this.error = 'Ocurrió un error al cargar los usuarios. Por favor, inténtalo de nuevo.';
          }
        } finally {
          this.loading = false;
        }
      },
      getUserInitials(name) {
        if (!name) return '?';
        
        return name
          .split(' ')
          .map(word => word[0])
          .join('')
          .toUpperCase()
          .substring(0, 2);
      },
      deleteUser(userId) {
        this.userToDelete = userId;
        this.showConfirmModal = true;
      },
      cancelDelete() {
        this.showConfirmModal = false;
        this.userToDelete = null;
      },
      async confirmDelete() {
  if (!this.userToDelete) return;
  
  try {
    // Usar la función del communicationManager para eliminar el usuario de la BD
    await communicationManager.deleteUser(this.userToDelete);
    
    // Mensaje de éxito en consola
    console.log(`Usuario con ID ${this.userToDelete} eliminado correctamente`);
    
    // Eliminar el usuario de la lista local después de eliminarlo de la BD
    this.users = this.users.filter(user => user.id !== this.userToDelete);
    
    // Opcional: Mostrar notificación de éxito si tienes algún sistema para ello
    // this.$notify({ type: 'success', text: 'Usuario eliminado correctamente' });
    
  } catch (error) {
    console.error('Error al eliminar el usuario:', error);
    
    // Opcional: Mostrar notificación de error si tienes algún sistema para ello
    // this.$notify({ type: 'error', text: 'No se pudo eliminar el usuario' });
    
    // También puedes manejar errores específicos
    if (error.response && error.response.status === 403) {
      console.error('No tienes permisos para eliminar este usuario');
      // this.$notify({ type: 'error', text: 'No tienes permisos para eliminar este usuario' });
    }
  } finally {
    this.showConfirmModal = false;
    this.userToDelete = null;
  }
}
    }
  };
  </script>
  
  <style scoped>
  .users-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
  }
  
  .loading-spinner {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 200px;
  }
  
  .user-card {
    transition: transform 0.2s;
  }
  
  .user-card:hover {
    transform: translateY(-5px);
  }
  </style>