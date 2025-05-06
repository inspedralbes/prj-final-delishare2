<template>
  <div class="notifications-container">
    <div class="header">
      <h1>Notificaciones</h1>
      <span class="notification-count" v-if="unreadCount > 0">{{ unreadCount }}</span>
    </div>
    
    <div class="search-container">
      <input 
        type="text" 
        v-model="searchQuery" 
        placeholder="Buscar notificaciones..." 
        class="search-input"
      >
    </div>

    <div class="tabs">
      <button 
        @click="activeTab = 'all'" 
        :class="['tab-button', activeTab === 'all' ? 'active' : '']"
      >
        Todas
      </button>
      <button 
        @click="activeTab = 'unread'" 
        :class="['tab-button', activeTab === 'unread' ? 'active' : '']"
      >
        No leídas
      </button>
    </div>
    
    <transition-group name="list" tag="div" class="notifications-list">
      <div v-if="filteredNotifications.length === 0" key="empty" class="empty-state">
        <svg class="empty-icon" viewBox="0 0 24 24" width="64" height="64">
          <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-1 14H5c-.55 0-1-.45-1-1V7c0-.55.45-1 1-1h14c.55 0 1 .45 1 1v10c0 .55-.45 1-1 1z"/>
          <path d="M12 11c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
        </svg>
        <p>No tienes notificaciones{{ activeTab === 'unread' ? ' sin leer' : '' }}.</p>
      </div>

      <router-link 
        v-for="notification in filteredNotifications" 
        :key="notification.id" 
        :to="'/info/' + notification.recipe_id"
        class="notification-link"
      >
        <div class="notification" :class="{ 'unread': !notification.read }">
          <div class="notification-indicator" v-if="!notification.read"></div>
          <div class="notification-content">
            <div class="image-container" v-if="notification.recipe_image">
              <img :src="notification.recipe_image" alt="Imagen de la receta" class="recipe-image" />
            </div>
            <div class="notification-message">
              <p :class="notification.read ? 'read-notification' : 'unread-notification'">
                {{ notification.message }}
              </p>
              <span class="notification-time">{{ formatTime(notification.createdAt) }}</span>
            </div>
          </div>
          <div class="notification-actions">
            <button 
              v-if="!notification.read" 
              @click.prevent="markAsRead(notification.id)" 
              class="mark-read-button"
              aria-label="Marcar como leída"
            >
              <svg viewBox="0 0 24 24" width="18" height="18">
                <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
              </svg>
              <span>Leída</span>
            </button>
          </div>
        </div>
      </router-link>
    </transition-group>

    <div class="pagination" v-if="notifications.length > itemsPerPage">
      <button 
        class="pagination-button" 
        :disabled="currentPage === 1"
        @click="currentPage--"
      >
        Anterior
      </button>
      <span class="page-info">{{ currentPage }} / {{ totalPages }}</span>
      <button 
        class="pagination-button" 
        :disabled="currentPage === totalPages"
        @click="currentPage++"
      >
        Siguiente
      </button>
    </div>
  </div>
</template>

<script>
import communicationManager from '@/services/communicationManager';

export default {
  data() {
    return {
      notifications: [],
      pollingInterval: null,
      searchQuery: '',
      activeTab: 'all',
      currentPage: 1,
      itemsPerPage: 5
    };
  },
  computed: {
    unreadCount() {
      return this.notifications.filter(notification => !notification.read).length;
    },
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
    activeTab() {
      this.currentPage = 1;
    },
    searchQuery() {
      this.currentPage = 1;
    }
  },
  async mounted() {
    await this.loadNotifications();
    this.pollingInterval = setInterval(() => {
      this.loadNotifications();
    }, 10000);
  },
  beforeDestroy() {
    clearInterval(this.pollingInterval);
  },
  methods: {
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

<style scoped>
.notifications-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  color: #333;
}

.header {
  display: flex;
  align-items: center;
  margin-bottom: 24px;
}

h1 {
  font-size: 28px;
  font-weight: 600;
  color: #2c3e50;
  margin: 0;
}

.notification-count {
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #e74c3c;
  color: white;
  border-radius: 50%;
  width: 24px;
  height: 24px;
  font-size: 14px;
  margin-left: 12px;
  font-weight: bold;
}

.search-container {
  margin-bottom: 20px;
}

.search-input {
  width: 100%;
  padding: 12px 16px;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 16px;
  transition: all 0.3s ease;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.search-input:focus {
  outline: none;
  border-color: #3498db;
  box-shadow: 0 2px 10px rgba(52, 152, 219, 0.2);
}

.tabs {
  display: flex;
  margin-bottom: 20px;
  border-bottom: 1px solid #eee;
}

.tab-button {
  padding: 12px 20px;
  background: none;
  border: none;
  font-size: 16px;
  font-weight: 500;
  color: #666;
  cursor: pointer;
  position: relative;
  transition: all 0.3s ease;
}

.tab-button:hover {
  color: #3498db;
}

.tab-button.active {
  color: #3498db;
  font-weight: 600;
}

.tab-button.active::after {
  content: '';
  position: absolute;
  bottom: -1px;
  left: 0;
  width: 100%;
  height: 3px;
  background-color: #3498db;
  border-radius: 3px 3px 0 0;
}

.notifications-list {
  position: relative;
  min-height: 100px;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px 0;
  color: #95a5a6;
  text-align: center;
}

.empty-icon {
  fill: #ccc;
  margin-bottom: 16px;
}

.notification-link {
  text-decoration: none;
  color: inherit;
  display: block;
  margin-bottom: 16px;
}

.notification {
  border-radius: 8px;
  background-color: #fff;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  overflow: hidden;
  transition: all 0.3s ease;
  display: flex;
  align-items: stretch;
  position: relative;
  border-left: 4px solid transparent;
}

.notification.unread {
  border-left-color: #3498db;
  background-color: #f8fafd;
}

.notification:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.notification-indicator {
  position: absolute;
  top: 16px;
  right: 16px;
  width: 10px;
  height: 10px;
  background-color: #3498db;
  border-radius: 50%;
}

.notification-content {
  flex: 1;
  display: flex;
  padding: 16px;
}

.image-container {
  width: 80px;
  flex-shrink: 0;
  margin-right: 16px;
  overflow: hidden;
  border-radius: 6px;
}

.recipe-image {
  width: 100%;
  height: 80px;
  object-fit: cover;
  border-radius: 6px;
  transition: transform 0.3s ease;
}

.notification:hover .recipe-image {
  transform: scale(1.05);
}

.notification-message {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.unread-notification {
  font-weight: 600;
  color: #2c3e50;
  margin: 0 0 8px 0;
  line-height: 1.5;
}

.read-notification {
  color: #57606f;
  margin: 0 0 8px 0;
  line-height: 1.5;
}

.notification-time {
  font-size: 12px;
  color: #95a5a6;
}

.notification-actions {
  padding: 16px 16px 16px 0;
  display: flex;
  align-items: center;
}

.mark-read-button {
  display: flex;
  align-items: center;
  padding: 8px 12px;
  background-color: #eaf2fd;
  color: #3498db;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s ease;
}

.mark-read-button svg {
  margin-right: 4px;
  fill: currentColor;
}

.mark-read-button:hover {
  background-color: #d4e6fc;
}

/* Animaciones */
.list-enter-active,
.list-leave-active {
  transition: all 0.5s ease;
}
.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateX(-30px);
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 24px;
}

.pagination-button {
  padding: 8px 16px;
  background-color: #f1f2f6;
  color: #57606f;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s ease;
}

.pagination-button:not(:disabled):hover {
  background-color: #e2e5ea;
}

.pagination-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-info {
  margin: 0 16px;
  font-size: 14px;
  color: #57606f;
}

/* Responsive design */
@media (max-width: 600px) {
  .notification-content {
    flex-direction: column;
  }
  
  .image-container {
    width: 100%;
    margin-right: 0;
    margin-bottom: 16px;
  }
  
  .recipe-image {
    height: 120px;
  }
  
  .notification-actions {
    padding: 0 16px 16px 16px;
    justify-content: flex-end;
  }
}
</style>