<template>
  <div class="notifications-container">
    <h1>Notificaciones</h1>
    <div v-if="notifications.length === 0">
      <p>No tienes notificaciones.</p>
    </div>

    <router-link v-for="notification in notifications" :key="notification.id" :to="'/info/' + notification.recipe_id"
      class="notification-link">
      <div class="notification">
        <img v-if="notification.recipe_image" :src="notification.recipe_image" alt="Imagen de la receta"
          class="recipe-image" />
        <p :class="notification.read ? 'read-notification' : 'unread-notification'">
          {{ notification.message }}
        </p>
        <button v-if="!notification.read" @click.prevent="markAsRead(notification.id)" class="mark-read-button">
          Marcar como leída
        </button>
      </div>
    </router-link>
  </div>
</template>

<script>
import communicationManager from '@/services/communicationManager';

export default {
  data() {
    return {
      notifications: [],
      pollingInterval: null,
    };
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
    }
  }
};
</script>

<style scoped>
.notifications-container {
  padding: 20px;
}

.notification-link {
  text-decoration: none;
  color: inherit;
  display: block;
  margin-bottom: 15px;
}

.notification {
  border: 1px solid #ccc;
  padding: 10px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  transition: all 0.3s ease;
  cursor: pointer;
}

.notification:hover {
  background-color: #f5f5f5;
  transform: translateX(5px);
}

.recipe-image {
  width: 100%;
  max-height: 200px;
  object-fit: cover;
  border-radius: 5px;
}

.unread-notification {
  font-weight: bold;
  color: #333;
}

.read-notification {
  color: #666;
}

.mark-read-button {
  padding: 5px 10px;
  background-color: #4a6fa5;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  align-self: flex-start;
  margin-top: 5px;
}

.mark-read-button:hover {
  background-color: #3a5a80;
}
</style>