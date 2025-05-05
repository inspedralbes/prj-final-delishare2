<template>
  <div class="notifications-container">
    <h1>Notificaciones</h1>
    
    <div v-if="loading" class="loading-notifications">
      <p>Cargando notificaciones...</p>
    </div>
    
    <div v-else-if="notifications.length === 0" class="empty-notifications">
      <p>No tienes notificaciones.</p>
    </div>

    <div v-else>
      <div v-for="notification in notifications" :key="notification.id" class="notification-item">
        <router-link :to="'/info/' + notification.recipe_id" class="notification-link">
          <div class="notification-content">
            <img v-if="notification.user_image" :src="notification.user_image" 
                 alt="Imagen de usuario" class="user-image" />
            
            <div class="notification-text">
              <p :class="{'unread': !notification.read, 'read': notification.read}">
                {{ notification.message }}
              </p>
              <span class="notification-time">{{ formatTime(notification.created_at) }}</span>
            </div>
            
            <img v-if="notification.recipe_image" :src="notification.recipe_image" 
                 alt="Imagen de receta" class="recipe-image" />
          </div>
        </router-link>
        
        <button v-if="!notification.read" @click.stop="markAsRead(notification.id)" 
                class="mark-read-button">
          Marcar como leída
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import communicationManager from '@/services/communicationManager';
import { useAuthStore } from '@/stores/authStore';

export default {
  data() {
    return {
      notifications: [],
      loading: true,
      socket: null
    };
  },
  async created() {
    await this.loadNotifications();
    this.setupSocket();
  },
  beforeUnmount() {
    if (this.socket) {
      this.socket.off('newNotification');
      this.socket.off('notificationRead');
      this.socket.off('notificationError');
      communicationManager.disconnectSocket();
    }
  },
  methods: {
    async loadNotifications() {
      this.loading = true;
      try {
        this.notifications = await communicationManager.getUserNotifications();
      } catch (error) {
        console.error('Error cargando notificaciones:', error);
        this.$toast.error('Error al cargar notificaciones');
      } finally {
        this.loading = false;
      }
    },
    
    async markAsRead(id) {
      try {
        await communicationManager.markNotificationAsRead(id);
        
        // Actualizar estado local
        const index = this.notifications.findIndex(n => n.id === id);
        if (index !== -1) {
          this.notifications[index].read = true;
        }
        
        this.$toast.success('Notificación marcada como leída');
      } catch (error) {
        console.error('Error marcando notificación como leída:', error);
        this.$toast.error('Error al marcar como leída');
      }
    },
    
    setupSocket() {
      const authStore = useAuthStore();
      
      if (authStore.userId) {
        this.socket = communicationManager.initSocket(authStore.userId);
        
        // Escuchar nuevas notificaciones
        this.socket.on('newNotification', (notification) => {
          // Verificar si la notificación ya existe
          const exists = this.notifications.some(n => n.id === notification.id);
          if (!exists) {
            this.notifications.unshift(notification);
            this.showNewNotificationAlert(notification);
          }
        });
        
        // Escuchar confirmación de notificación leída
        this.socket.on('notificationRead', ({ notificationId }) => {
          const index = this.notifications.findIndex(n => n.id === notificationId);
          if (index !== -1) {
            this.notifications[index].read = true;
          }
        });
        
        // Manejar errores
        this.socket.on('notificationError', ({ error }) => {
          console.error('Error en notificación:', error);
          this.$toast.error(`Error: ${error}`);
        });
      }
    },
    
    showNewNotificationAlert(notification) {
      this.$toast.info(notification.message, {
        onClick: () => {
          this.$router.push(`/info/${notification.recipe_id}`);
        }
      });
    },
    
    formatTime(dateString) {
      const date = new Date(dateString);
      return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }
  }
};
</script>

<style scoped>
.notifications-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.notification-item {
  border-bottom: 1px solid #eee;
  padding: 15px 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.notification-content {
  display: flex;
  align-items: center;
  flex-grow: 1;
}

.notification-link {
  text-decoration: none;
  color: inherit;
  flex-grow: 1;
}

.user-image, .recipe-image {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
  margin-right: 15px;
}

.recipe-image {
  margin-left: auto;
  margin-right: 0;
}

.notification-text {
  flex-grow: 1;
}

.unread {
  font-weight: bold;
  color: #333;
}

.read {
  color: #666;
}

.notification-time {
  font-size: 0.8em;
  color: #999;
  display: block;
  margin-top: 5px;
}

.mark-read-button {
  background: #f0f0f0;
  border: none;
  padding: 5px 10px;
  border-radius: 4px;
  cursor: pointer;
  margin-left: 15px;
}

.mark-read-button:hover {
  background: #e0e0e0;
}

.loading-notifications, .empty-notifications {
  text-align: center;
  padding: 40px;
  color: #666;
}
</style>