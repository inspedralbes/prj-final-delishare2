<template>
  <div class="min-h-screen bg-lime-50 flex flex-col">
    <!-- Hero Section with animated background -->
    <section class="relative overflow-hidden">
      <div class="bg-gradient-to-br from-lime-100 via-lime-200 to-green-200 py-16 relative">
        <div class="absolute inset-0 bg-white/30 backdrop-blur-sm"></div>
        <!-- Animated circles decoration -->
        <div class="absolute inset-0 overflow-hidden">
          <div class="absolute -left-10 -top-10 w-40 h-40 bg-yellow-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 motion-safe:animate-[blob_7s_infinite]"></div>
          <div class="absolute -right-10 -top-10 w-40 h-40 bg-lime-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 motion-safe:animate-[blob_7s_infinite_2s]"></div>
          <div class="absolute -bottom-10 left-20 w-40 h-40 bg-green-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 motion-safe:animate-[blob_7s_infinite_4s]"></div>
        </div>

        <div class="max-w-7xl mx-auto px-6 relative z-10">
          <div class="text-center">
            <h1 class="text-4xl tracking-tight font-extrabold text-lime-900 sm:text-5xl md:text-6xl">
              <span class="block bg-gradient-to-r from-lime-900 via-lime-700 to-green-800 bg-clip-text text-transparent">
                Gestió d'Usuaris
              </span>
              <span class="block text-2xl mt-3 text-lime-700 font-medium">
                Administra els usuaris de la plataforma
              </span>
            </h1>
          </div>
        </div>
      </div>
    </section>

    <!-- Search Section -->
    <div class="w-full px-6 -mt-8 relative z-20 flex justify-center">
      <div class="w-full sm:w-5/6 md:w-4/5 lg:w-3/4 xl:w-2/3 2xl:w-1/2 transform hover:scale-105 transition-transform duration-300">
        <div class="relative">
          <input 
            type="text" 
            v-model="searchTerm" 
            placeholder="Cerca usuaris..." 
            class="w-full pl-12 pr-8 py-5 text-lg text-lime-900 border-2 border-lime-300 rounded-full focus:outline-none focus:ring-4 focus:ring-lime-300/50 focus:border-lime-400 bg-white/80 backdrop-blur-sm shadow-lg" 
          />
          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <svg class="w-6 h-6 text-lime-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-6 py-12">
      <BotonesCrud />

      <!-- Loading State -->
      <div v-if="loading" class="flex flex-col items-center justify-center py-12">
        <div class="relative">
          <div class="w-16 h-16 border-4 border-lime-300 border-dashed rounded-full animate-spin"></div>
          <span class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-3xl">👥</span>
        </div>
        <p class="mt-4 text-lime-700 font-medium animate-pulse">Carregant usuaris...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="bg-red-50 rounded-xl p-8 text-center border border-red-200 shadow-lg hover:shadow-xl transition-all duration-300 motion-safe:animate-[shake_0.5s_ease-in-out]">
        <div class="text-4xl mb-4">😕</div>
        <p class="text-red-600 mb-4 font-medium">{{ error }}</p>
        <button @click="fetchUsers" class="bg-gradient-to-r from-green-500 via-lime-400 to-lime-300 text-lime-900 px-8 py-3 rounded-full hover:from-green-600 hover:via-lime-500 hover:to-lime-400 transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 font-medium">
          Tornar a intentar
        </button>
      </div>

      <!-- No Results State -->
      <div v-else-if="filteredUsers.length === 0" class="text-center py-12">
        <div class="text-6xl mb-4">🔍</div>
        <p class="text-lime-700 text-xl">No s'han trobat usuaris amb aquest criteri de cerca.</p>
      </div>

      <!-- Users Table -->
      <div v-else class="bg-white rounded-2xl shadow-lg overflow-hidden max-w-3xl mx-auto">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-gradient-to-r from-lime-50 to-green-50">
                <th class="px-3 py-2 text-left text-xs font-semibold text-lime-900">Usuari</th>
                <th class="px-2 py-2 text-left text-xs font-semibold text-lime-900">Email</th>
                <th class="px-2 py-2 text-left text-xs font-semibold text-lime-900">Rol</th>
                <th class="px-2 py-2 text-center text-xs font-semibold text-lime-900">.</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-lime-100">
              <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-lime-50/50 transition-colors duration-200">
                <td class="px-3 py-2">
                  <div class="flex items-center space-x-2">
                    <div class="flex-shrink-0">
                      <div class="w-7 h-7 rounded-full bg-gradient-to-r from-lime-400 to-green-500 flex items-center justify-center text-white text-xs font-semibold">
                        {{ getUserInitials(user.name) }}
                      </div>
                    </div>
                    <div class="text-xs font-medium text-lime-900">{{ user.name }}</div>
                  </div>
                </td>
                <td class="px-2 py-2 text-xs text-lime-700 whitespace-nowrap">{{ user.email }}</td>
                <td class="px-2 py-2">
                  <select 
                    v-model="user.role" 
                    @change="updateUserRole(user)" 
                    class="text-xs rounded border-lime-300 focus:border-lime-400 focus:ring focus:ring-lime-200 focus:ring-opacity-50 min-w-[70px]"
                  >
                    <option value="user">Nom</option>
                    <option value="chef">Xef</option>
                    <option value="admin">Admin</option>
                  </select>
                </td>
                <td class="px-2 py-2 text-center">
                  <button 
                    @click="deleteUser(user.id)" 
                    class="inline-flex items-center px-1.5 py-1 text-xs font-medium text-red-600 hover:text-red-800 transition-colors duration-200"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Confirmation Modal -->
    <div v-if="showConfirmModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 transform transition-all">
        <h3 class="text-xl font-bold text-lime-900 mb-4">Confirmar eliminació</h3>
        <p class="text-lime-700 mb-6">Estàs segur que vols eliminar aquest usuari? Aquesta acció no es pot desfer.</p>
        <div class="flex justify-end space-x-4">
          <button 
            @click="cancelDelete" 
            class="px-4 py-2 text-sm font-medium text-lime-700 hover:text-lime-900 transition-colors duration-200"
          >
            Cancel·lar
          </button>
          <button 
            @click="confirmDelete" 
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors duration-200"
          >
            Eliminar
          </button>
        </div>
      </div>
    </div>

    <!-- Success Modal -->
    <div v-if="showSuccessModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 transform transition-all">
        <div class="text-center">
          <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <h3 class="text-xl font-bold text-lime-900 mb-2">¡Èxit!</h3>
          <p class="text-lime-700">{{ successMessage }}</p>
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
      userToDelete: null,
      successMessage: '',
      showSuccessModal: false,
    };
  },
  computed: {
    filteredUsers() {
      if (!this.searchTerm) return this.users;
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
        const token = localStorage.getItem('token');
        if (!token) {
          this.error = 'No s\'ha trobat el token d\'autenticació. Si us plau, inicia sessió.';
          this.loading = false;
          return;
        }
        this.users = await communicationManager.fetchAllUsers();
      } catch (err) {
        console.error('Error fetching users:', err);
        if (err.response && err.response.status === 401) {
          this.error = 'La teva sessió ha expirat o no tens autorització. Si us plau, torna a iniciar sessió.';
        } else {
          this.error = 'Ha ocorregut un error en carregar els usuaris. Si us plau, torna-ho a intentar.';
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
        await communicationManager.deleteUser(this.userToDelete);
        this.users = this.users.filter(user => user.id !== this.userToDelete);
        this.showSuccess('Usuari eliminat correctament');
      } catch (error) {
        console.error('Error al eliminar el usuari:', error);
        if (error.response && error.response.status === 403) {
          console.error('No tens permisos per eliminar aquest usuari');
        }
      } finally {
        this.showConfirmModal = false;
        this.userToDelete = null;
      }
    },
    async updateUserRole(user) {
      try {
        await communicationManager.updateUserRole(user.id, user.role);
        this.showSuccess(`Rol actualitzat a "${user.role}" per l'usuari ${user.name}`);
      } catch (error) {
        console.error('Error al actualitzar el rol:', error);
        alert('Ha ocorregut un error en actualitzar el rol. Torna-ho a intentar.');
      }
    },
    showSuccess(message) {
      this.successMessage = message;
      this.showSuccessModal = true;
      setTimeout(() => {
        this.showSuccessModal = false;
        this.successMessage = '';
      }, 1000);
    }
  }
};
</script>