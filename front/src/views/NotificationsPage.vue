<template>
  <div class="min-h-screen bg-lime-50 flex flex-col">
    <!-- Hero/Header visual -->
    <section class="relative overflow-hidden">
      <div class="bg-gradient-to-br from-lime-100 via-lime-200 to-green-200 py-16 relative">
        <div class="absolute inset-0 bg-white/30 backdrop-blur-sm"></div>
        <!-- Animated circles decoration -->
        <div class="absolute inset-0 overflow-hidden">
          <div class="absolute -left-10 -top-10 w-40 h-40 bg-yellow-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
          <div class="absolute -right-10 -top-10 w-40 h-40 bg-lime-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
          <div class="absolute -bottom-10 left-20 w-40 h-40 bg-green-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
        </div>
        <div class="max-w-7xl mx-auto px-6 relative z-10">
          <div class="text-center flex flex-col items-center justify-center gap-4">
            <h1 class="text-4xl tracking-tight font-extrabold text-lime-900 sm:text-5xl md:text-6xl">
              <span class="block bg-gradient-to-r from-lime-900 via-lime-700 to-green-800 bg-clip-text text-transparent">
                Notificaciones
              </span>
              <span class="block text-2xl mt-3 text-lime-700 font-medium">
                Consulta tus avisos y novedades
              </span>
            </h1>
            <span v-if="unreadCount > 0" class="inline-flex items-center justify-center bg-red-500 text-white rounded-full w-8 h-8 text-base font-bold">{{ unreadCount }}</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Search -->
    <div class="max-w-2xl w-full mx-auto px-4 mt-8">
      <input 
        type="text" 
        v-model="searchQuery" 
        placeholder="Buscar notificaciones..." 
        class="w-full p-3 border border-gray-200 rounded-xl text-lg focus:outline-none focus:ring-2 focus:ring-lime-300 shadow-sm"
      >
    </div>

    <!-- Tabs -->
    <div class="flex justify-center gap-4 mt-8 mb-6 px-6 md:gap-2 md:mt-4 md:mb-2 md:px-2">
      <button 
        @click="activeTab = 'all'" 
        :class="[
          'flex-1 py-2 px-2 text-sm rounded-full font-semibold transition-all duration-300',
          'md:py-4 md:px-4 md:text-base',
          activeTab === 'all' ? 'bg-gradient-to-r from-green-500 via-lime-400 to-lime-300 text-lime-900 shadow-lg hover:shadow-xl hover:brightness-110' : 'bg-gradient-to-r from-green-100 via-lime-50 to-lime-100 text-lime-700 hover:from-green-200 hover:via-lime-100 hover:to-lime-200 hover:shadow-md'
        ]"
      >
        Todas
      </button>
      <button 
        @click="activeTab = 'unread'" 
        :class="[
          'flex-1 py-2 px-2 text-sm rounded-full font-semibold transition-all duration-300',
          'md:py-4 md:px-4 md:text-base',
          activeTab === 'unread' ? 'bg-gradient-to-r from-green-500 via-lime-400 to-lime-300 text-lime-900 shadow-lg hover:shadow-xl hover:brightness-110' : 'bg-gradient-to-r from-green-100 via-lime-50 to-lime-100 text-lime-700 hover:from-green-200 hover:via-lime-100 hover:to-lime-200 hover:shadow-md'
        ]"
      >
        No leídas
      </button>
    </div>

    <!-- Lista de notificaciones -->
    <main class="flex-1 max-w-2xl w-full mx-auto px-4 pb-32 md:pb-24">
      <transition-group name="list" tag="div" class="space-y-4 mt-4">
        <div v-if="filteredNotifications.length === 0" key="empty" class="bg-white rounded-xl shadow-lg p-8 text-center mt-8 md:rounded-xl md:shadow-md md:p-6 md:mt-4">
          <svg class="mx-auto mb-4" viewBox="0 0 24 24" width="64" height="64">
            <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-1 14H5c-.55 0-1-.45-1-1V7c0-.55.45-1 1-1h14c.55 0 1 .45 1 1v10c0 .55-.45 1-1 1z" fill="#ccc"/>
            <path d="M12 11c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" fill="#ccc"/>
          </svg>
          <p class="text-gray-600 mb-4 text-xl md:text-base">No tienes notificaciones{{ activeTab === 'unread' ? ' sin leer' : '' }}.</p>
        </div>

        <router-link 
          v-for="notification in filteredNotifications" 
          :key="notification.id" 
          :to="'/info/' + notification.recipe_id"
          class="block"
        >
          <div class="flex items-start gap-4 bg-white rounded-xl shadow-md p-4 md:p-6 mb-2 transition-all hover:shadow-lg border-l-4" :class="notification.read ? 'border-transparent' : 'border-lime-400'">
            <div v-if="notification.recipe_image" class="w-16 h-16 rounded-lg overflow-hidden flex-shrink-0">
              <img :src="notification.recipe_image" alt="Imagen de la receta" class="w-full h-full object-cover" />
            </div>
            <div class="flex-1">
              <p :class="notification.read ? 'text-gray-500' : 'font-semibold text-lime-900'">
                {{ notification.message }}
              </p>
              <span class="block text-xs text-gray-400 mt-1">{{ formatTime(notification.createdAt) }}</span>
            </div>
            <div v-if="!notification.read" class="flex items-center">
              <span class="inline-block w-3 h-3 bg-lime-400 rounded-full"></span>
            </div>
            <div class="ml-2">
              <button 
                v-if="!notification.read" 
                @click.prevent="markAsRead(notification.id)" 
                class="inline-flex items-center px-2 py-1 bg-lime-50 text-lime-700 rounded-lg text-xs font-medium hover:bg-lime-100 transition-colors"
                aria-label="Marcar como leída"
              >
                <svg viewBox="0 0 24 24" width="18" height="18" class="mr-1">
                  <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z" fill="currentColor"/>
                </svg>
                Leída
              </button>
            </div>
          </div>
        </router-link>
      </transition-group>

      <!-- Paginación -->
      <div class="flex justify-center items-center mt-8" v-if="notifications.length > itemsPerPage">
        <button 
          class="px-4 py-2 bg-lime-100 text-lime-700 rounded-lg font-medium mr-2 disabled:opacity-50" 
          :disabled="currentPage === 1"
          @click="currentPage--"
        >
          Anterior
        </button>
        <span class="mx-2 text-gray-600">{{ currentPage }} / {{ totalPages }}</span>
        <button 
          class="px-4 py-2 bg-lime-100 text-lime-700 rounded-lg font-medium ml-2 disabled:opacity-50" 
          :disabled="currentPage === totalPages"
          @click="currentPage++"
        >
          Siguiente
        </button>
      </div>
    </main>
  </div>
</template>

<script>
import communicationManager from '@/services/communicationManager';

export default {
  data() {
    return {
      // Lista de notificaciones del usuario
      notifications: [],
      // Intervalo para polling de notificaciones
      pollingInterval: null,
      // Término de búsqueda para filtrar notificaciones
      searchQuery: '',
      // Pestaña activa (todas/no leídas)
      activeTab: 'all',
      // Página actual de la paginación
      currentPage: 1,
      // Número de items por página
      itemsPerPage: 5
    };
  },
  computed: {
    /**
     * Calcula el número de notificaciones no leídas
     * @returns {number} Número de notificaciones sin leer
     */
    unreadCount() {
      return this.notifications.filter(notification => !notification.read).length;
    },

    /**
     * Filtra y pagina las notificaciones según:
     * - Término de búsqueda
     * - Estado de lectura
     * - Paginación actual
     * @returns {Array} Lista de notificaciones filtradas y paginadas
     */
    filteredNotifications() {
      let result = [...this.notifications];
      
      // Filtrar por búsqueda
      if (this.searchQuery.trim() !== '') {
        const query = this.searchQuery.toLowerCase();
        result = result.filter(notification => 
          notification.message.toLowerCase().includes(query)
        );
      }
      
      // Filtrar por pestaña
      if (this.activeTab === 'unread') {
        result = result.filter(notification => !notification.read);
      }
      
      // Paginación
      const startIndex = (this.currentPage - 1) * this.itemsPerPage;
      const endIndex = startIndex + this.itemsPerPage;
      
      return result.slice(startIndex, endIndex);
    },

    /**
     * Calcula el número total de páginas según los filtros aplicados
     * @returns {number} Número total de páginas
     */
    totalPages() {
      let filteredItems = [...this.notifications];
      
      if (this.searchQuery.trim() !== '') {
        const query = this.searchQuery.toLowerCase();
        filteredItems = filteredItems.filter(notification => 
          notification.message.toLowerCase().includes(query)
        );
      }
      
      if (this.activeTab === 'unread') {
        filteredItems = filteredItems.filter(notification => !notification.read);
      }
      
      return Math.ceil(filteredItems.length / this.itemsPerPage);
    }
  },
  watch: {
    /**
     * Resetea la página actual al cambiar de pestaña
     */
    activeTab() {
      this.currentPage = 1;
    },
    /**
     * Resetea la página actual al realizar una búsqueda
     */
    searchQuery() {
      this.currentPage = 1;
    }
  },
  async mounted() {
    await this.loadNotifications();
    // Configurar polling cada 10 segundos
    this.pollingInterval = setInterval(() => {
      this.loadNotifications();
    }, 10000);
  },
  beforeDestroy() {
    // Limpiar intervalo al destruir el componente
    clearInterval(this.pollingInterval);
  },
  methods: {
    /**
     * Carga las notificaciones del usuario desde el servidor
     * Añade timestamps si no existen
     */
    async loadNotifications() {
      try {
        this.notifications = await communicationManager.getUserNotifications();
        // Asumimos que las notificaciones vienen con una fecha de creación
        // Si no es así, podemos agregar una fecha ficticia para demostración
        this.notifications.forEach(notification => {
          if (!notification.createdAt) {
            notification.createdAt = new Date();
          }
        });
      } catch (error) {
        console.error('Error fetching notifications:', error);
      }
    },

    /**
     * Marca una notificación como leída
     * @param {number} id - ID de la notificación a marcar como leída
     */
    async markAsRead(id) {
      try {
        await communicationManager.markNotificationAsRead(id);
        const notification = this.notifications.find(n => n.id === id);
        if (notification) {
          notification.read = true;
        }
      } catch (error) {
        console.error('Error marking notification as read:', error);
      }
    },

    /**
     * Formatea la fecha de una notificación en un formato legible
     * Muestra "Hoy", "Ayer", "Hace X días" o la fecha completa
     * @param {Date|string} date - Fecha a formatear
     * @returns {string} Fecha formateada
     */
    formatTime(date) {
      if (!date) return '';
      
      const now = new Date();
      const notificationDate = new Date(date);
      const diffTime = Math.abs(now - notificationDate);
      const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
      
      if (diffDays === 0) {
        const hours = notificationDate.getHours().toString().padStart(2, '0');
        const minutes = notificationDate.getMinutes().toString().padStart(2, '0');
        return `Hoy a las ${hours}:${minutes}`;
      } else if (diffDays === 1) {
        return 'Ayer';
      } else if (diffDays < 7) {
        return `Hace ${diffDays} días`;
      } else {
        const day = notificationDate.getDate().toString().padStart(2, '0');
        const month = (notificationDate.getMonth() + 1).toString().padStart(2, '0');
        const year = notificationDate.getFullYear();
        return `${day}/${month}/${year}`;
      }
    }
  }
};
</script>