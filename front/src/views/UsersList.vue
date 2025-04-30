<template>
    <div class="users-container">
      <h1 class="title">Lista de Usuarios</h1>
      <BotonesCrud/>

      <div v-if="loading" class="loading-spinner">
        <div class="spinner"></div>
        <p class="loading-text">Cargando usuarios...</p>
      </div>
      
      <div v-else-if="error" class="error-message">
        <p class="error-text">{{ error }}</p>
        <button @click="fetchUsers" class="retry-button">
          Reintentar
        </button>
      </div>
      
      <div v-else>
        <div class="search-box">
          <input 
            type="text" 
            v-model="searchTerm" 
            placeholder="Buscar usuarios..." 
            class="search-input"
          />
        </div>
        
        <div v-if="filteredUsers.length === 0" class="no-results">
          <p>No se encontraron usuarios con ese criterio de búsqueda.</p>
        </div>
        
        <div v-else class="table-container">
          <table class="users-table">
            <thead>
              <tr>
                <th>Usuario</th>
                <th>Correo electrónico</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in filteredUsers" :key="user.id" class="user-row">
                <td class="user-name-cell">
                  <div class="user-info">
                    <div class="avatar-small">
                      <span class="initials-small">{{ getUserInitials(user.name) }}</span>
                    </div>
                    <span>{{ user.name }}</span>
                  </div>
                </td>
                <td class="user-email-cell">{{ user.email }}</td>
                <td class="actions-cell">
                  <button 
                    @click="deleteUser(user.id)" 
                    class="delete-button"
                    title="Eliminar usuario"
                  >
                    <svg class="delete-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    <span>Eliminar</span>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
  
      <!-- Modal de confirmación -->
      <div v-if="showConfirmModal" class="modal-overlay">
        <div class="modal-container">
          <h3 class="modal-title">Confirmar eliminación</h3>
          <p class="modal-text">¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.</p>
          <div class="modal-buttons">
            <button 
              @click="cancelDelete" 
              class="cancel-button"
            >
              Cancelar
            </button>
            <button 
              @click="confirmDelete" 
              class="confirm-button"
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
  import BotonesCrud from '@/components/BotonesCrud.vue';
  export default {
    name: 'UsersList',
    components: {
    BotonesCrud
  },
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
          
        } catch (error) {
          console.error('Error al eliminar el usuario:', error);
          
          if (error.response && error.response.status === 403) {
            console.error('No tienes permisos para eliminar este usuario');
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
    padding: 1.5rem;
  }
  
  .title {
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 1.5rem;
    color: #1f2937;
  }
  
  .loading-spinner {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 200px;
  }
  
  .spinner {
    border-radius: 50%;
    width: 3rem;
    height: 3rem;
    border: 2px solid transparent;
    border-bottom-color: #2563eb;
    animation: spin 1s linear infinite;
  }
  
  .loading-text {
    margin-top: 0.75rem;
    color: #4b5563;
  }
  
  .error-message {
    background-color: #fef2f2;
    border-left: 4px solid #ef4444;
    color: #b91c1c;
    padding: 1rem 1.5rem;
    border-radius: 0.375rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
  }
  
  .error-text {
    font-weight: 500;
  }
  
  .retry-button {
    background-color: #dc2626;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    margin-top: 0.75rem;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    border: none;
    cursor: pointer;
    transition: background-color 0.2s;
  }
  
  .retry-button:hover {
    background-color: #b91c1c;
  }
  
  .search-box {
    margin-bottom: 1.5rem;
  }
  
  .search-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    transition: all 0.2s;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
  }
  
  .search-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
  }
  
  .no-results {
    padding: 1.5rem;
    background-color: #f9fafb;
    border-radius: 0.5rem;
    text-align: center;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    border: 1px solid #f3f4f6;
  }
  
  .table-container {
    overflow-x: auto;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }
  
  .users-table {
    width: 100%;
    border-collapse: collapse;
    background-color: white;
  }
  
  .users-table th,
  .users-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
  }
  
  .users-table th {
    background-color: #f9fafb;
    font-weight: 600;
    color: #4b5563;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }
  
  .user-row {
    transition: background-color 0.2s;
  }
  
  .user-row:hover {
    background-color: #f9fafb;
  }
  
  .user-name-cell {
    min-width: 250px;
  }
  
  .user-email-cell {
    min-width: 250px;
    color: #4b5563;
  }
  
  .actions-cell {
    text-align: center;
    min-width: 120px;
  }
  
  .user-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }
  
  .avatar-small {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    background: linear-gradient(to right, #3b82f6, #1e40af);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }
  
  .initials-small {
    font-size: 0.875rem;
    font-weight: bold;
  }
  
  .delete-button {
    background-color: #ef4444;
    color: white;
    padding: 0.5rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    transition: background-color 0.2s;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .delete-button:hover {
    background-color: #dc2626;
  }
  
  .delete-icon {
    width: 0.875rem;
    height: 0.875rem;
    margin-right: 0.25rem;
  }
  
  .modal-overlay {
    position: fixed;
    inset: 0;
    background-color: rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 50;
  }
  
  .modal-container {
    background-color: white;
    padding: 2rem;
    border-radius: 0.5rem;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    max-width: 28rem;
    width: 100%;
    animation: fadeIn 0.2s ease-out forwards;
  }
  
  .modal-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #1f2937;
  }
  
  .modal-text {
    color: #4b5563;
  }
  
  .modal-buttons {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 2rem;
  }
  
  .cancel-button {
    padding: 0.5rem 1.25rem;
    background-color: #e5e7eb;
    border-radius: 0.375rem;
    transition: background-color 0.2s;
    border: none;
    cursor: pointer;
  }
  
  .cancel-button:hover {
    background-color: #d1d5db;
  }
  
  .confirm-button {
    padding: 0.5rem 1.25rem;
    background-color: #dc2626;
    color: white;
    border-radius: 0.375rem;
    transition: background-color 0.2s;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    border: none;
    cursor: pointer;
  }
  
  .confirm-button:hover {
    background-color: #b91c1c;
  }
  
  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
  
  @keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
  }
  
  @media (max-width: 640px) {
    .users-table {
    font-size: 0.875rem;
  }
  
  .users-table th,
  .users-table td {
    padding: 0.5rem 0.25rem; /* Reducimos más el padding */
  }
  
  .user-name-cell,
  .user-email-cell {
    min-width: auto; /* Eliminamos el ancho mínimo fijo */
    max-width: 150px; /* Establecemos un máximo más pequeño */
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  
  .actions-cell {
    min-width: 40px; /* Reducimos el ancho mínimo */
  }
  
  .avatar-small {
    width: 1.75rem;
    height: 1.75rem;
    display: none; /* Ocultamos el avatar en móvil para ahorrar espacio */
  }
  
  .delete-button {
    padding: 0.25rem; /* Reducimos el padding del botón */
  }
  
  .delete-button span {
    display: none;
  }
  
  .delete-icon {
    margin-right: 0;
    width: 0.75rem;
    height: 0.75rem;
  }
  
  .user-info {
    gap: 0.25rem; /* Reducimos el espacio entre elementos */
  }
}
  </style>