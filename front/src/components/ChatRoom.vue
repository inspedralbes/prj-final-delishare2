<template>
  <div class="live-chat-container">
    <!-- Página de espera para usuarios normales -->
    <div v-if="!isLiveStarted && !isChef" class="waiting-room">
      <div class="waiting-content">
        <h2>🕒 Esperando a que el chef inicie el live...</h2>
        <div class="waiting-info">
          <p>Usuarios esperando: {{ waitingUsers.length }}</p>
          <p>Chef: {{ chefName || 'Conectando...' }}</p>
        </div>
        <div class="loader"></div>
      </div>
    </div>

    <!-- Panel de control del chef -->
    <div v-if="isChef && !isLiveStarted" class="chef-control-panel">
      <h2>👨‍🍳 Panel del Chef</h2>
      <p>Estás a punto de iniciar el chat en vivo</p>
      <div class="user-count">
        <span>Usuarios esperando: {{ waitingUsers.length }}</span>
      </div>
      <button @click="startLiveChat" class="start-button">
        🚀 Iniciar Live Chat
      </button>
    </div>

    <!-- Chat activo (visible para todos cuando el chef inicia) -->
    <div v-if="isLiveStarted" class="active-chat">
      <div class="chat-header">
        <h3>Chat en vivo</h3>
        <div class="active-users">
          <span v-for="(user, index) in activeUsers" :key="index">
            {{ user }}{{ index < activeUsers.length - 1 ? ', ' : '' }} </span>
              <span v-if="activeUsers.length === 0">No hay otros usuarios conectados</span>
        </div>
      </div>

      <div class="chat-messages" ref="messagesContainer">
        <div v-for="(msg, index) in messages" :key="index"
          :class="['message', msg.username === username ? 'my-message' : 'other-message']">
          <div class="message-meta">
            <strong v-if="msg.username !== username">{{ msg.username }}</strong>
            <span class="message-time">{{ formatTime(msg.timestamp) }}</span>
          </div>
          <div class="message-content">{{ msg.message }}</div>
        </div>
      </div>

      <div class="chat-input">
        <input v-model="newMessage" @keyup.enter="sendMessage" placeholder="Escribe tu mensaje..." :disabled="!isConnected" />
        <button @click="sendMessage" :disabled="!isConnected || !newMessage.trim()">Enviar</button>
      </div>
    </div>

    <div v-if="!isConnected" class="connection-status">
      🔄 Conectando al chat...
    </div>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted, nextTick, computed } from 'vue';
import { useRoute } from 'vue-router';
import { io } from 'socket.io-client';
import { useAuthStore } from '@/stores/authStore';
import communicationManager from '@/services/communicationManager';

export default {
  setup() {
    const route = useRoute();
    const authStore = useAuthStore();
    const liveId = ref(route.params.liveId);
    const socket = ref(null);
    const username = ref('Usuario');
    const activeUsers = ref([]);
    const waitingUsers = ref([]);
    const messages = ref([]);
    const newMessage = ref('');
    const isConnected = ref(false);
    const isLiveStarted = ref(false);
    const isChef = ref(false); // Inicialmente asumimos que no es chef
    const chefName = ref('');
    const messagesContainer = ref(null);
    const loadingUser = ref(true);
    const userError = ref(null);

    // Función para obtener los datos del usuario
    const fetchUserData = async () => {
      try {
        loadingUser.value = true;
        userError.value = null;
        
        // Verificar si hay token
        const token = localStorage.getItem('token');
        if (!token) {
          throw new Error('No estás autenticado');
        }

        // Obtener datos del usuario
        const userData = await communicationManager.getUser();
        
        // Validar datos del usuario
        if (!userData || !userData.id) {
          throw new Error('Datos de usuario incompletos');
        }

        // Asignar valores
        username.value = userData.name || 'Usuario';
        isChef.value = userData.role === 'chef'; // Verificar el role según tu tabla
        
        console.log(`Usuario identificado: ${username.value}, Role: ${userData.role}`);
      } catch (error) {
        console.error('Error al obtener datos del usuario:', error);
        userError.value = error.message || 'Error al cargar datos de usuario';
      } finally {
        loadingUser.value = false;
      }
    };

    // Computed para verificar si podemos enviar mensajes
    const canSendMessages = computed(() => {
      return isConnected.value && (isLiveStarted.value || isChef.value);
    });

    const formatTime = (timestamp) => {
      if (!timestamp) return '';
      const date = timestamp instanceof Date ? timestamp : new Date(timestamp);
      return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    };

    const scrollToBottom = () => {
      nextTick(() => {
        if (messagesContainer.value) {
          messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
      });
    };

    const startLiveChat = () => {
      if (!isChef.value || !isConnected.value) return;

      console.log('Intentando iniciar el live chat...');
      socket.value.emit('chefStartLive', { liveId: liveId.value }, (response) => {
        if (response?.success) {
          isLiveStarted.value = true;
          console.log('Live iniciado con éxito!');
        } else {
          console.error('Error al iniciar el live:', response?.message);
        }
      });
    };

    const sendMessage = () => {
      if (!newMessage.value.trim() || !canSendMessages.value) return;
      
      const messageData = { 
        liveId: liveId.value, 
        username: username.value, 
        message: newMessage.value.trim(),
        isChef: isChef.value
      };
      
      socket.value.emit('sendChatMessage', messageData);
      
      // Agregamos nuestro propio mensaje a la lista para verlo inmediatamente
      messages.value.push({
        username: username.value,
        message: newMessage.value.trim(),
        timestamp: new Date(),
        isChef: isChef.value
      });
      
      newMessage.value = '';
      scrollToBottom();
    };

    const initializeChat = async () => {
      // Primero obtenemos los datos del usuario
      await fetchUserData();
      
      // Solo inicializamos el chat si tenemos datos del usuario
      if (!userError.value) {
        console.log(`Inicializando chat como ${isChef.value ? 'CHEF' : 'USUARIO'} para sala ${liveId.value}`);
        
        socket.value = io('http://localhost:4000', {
          transports: ['websocket', 'polling'],
          query: {
            liveId: liveId.value,
            username: username.value,
            isChef: isChef.value
          }
        });

        socket.value.on('connect', () => {
          console.log('Conectado al servidor:', socket.value.id);
          isConnected.value = true;
          
          socket.value.emit('joinLiveRoom', { 
            liveId: liveId.value, 
            username: username.value, 
            isChef: isChef.value 
          }, (response) => {
            if (response?.success) {
              console.log(`Unido al room correctamente como ${isChef.value ? 'CHEF' : 'USUARIO'}`);
            } else {
              console.error('Error al unirse al room:', response?.error);
            }
          });
        });

        socket.value.on('liveStarted', (data) => {
          console.log('Live iniciado:', data);
          isLiveStarted.value = true;
          activeUsers.value = data.activeUsers || [];
          messages.value.push({ 
            username: 'Sistema', 
            message: '¡El chat en vivo ha comenzado!', 
            timestamp: new Date(),
            isSystem: true
          });
          scrollToBottom();
        });

        socket.value.on('waitingRoomUpdate', (data) => {
          console.log('Actualización sala de espera:', data);
          waitingUsers.value = data.waitingUsers || [];
          activeUsers.value = data.activeUsers || [];
          chefName.value = data.chefName || 'Esperando chef...';
        });

        socket.value.on('newChatMessage', (msg) => {
          // Solo agregamos mensajes que no sean nuestros
          if (msg.username !== username.value) {
            messages.value.push(msg);
            scrollToBottom();
          }
        });

        socket.value.on('userJoined', (data) => {
          messages.value.push({
            username: 'Sistema',
            message: `${data.username} se ha unido al chat`,
            timestamp: new Date(),
            isSystem: true
          });
          activeUsers.value = data.users || [];
          scrollToBottom();
        });

        socket.value.on('userLeft', (data) => {
          messages.value.push({ 
            username: 'Sistema', 
            message: `${data.username} ha abandonado el chat`, 
            timestamp: new Date(),
            isSystem: true
          });
          activeUsers.value = data.users || [];
          scrollToBottom();
        });

        socket.value.on('error', (error) => {
          console.error('Error del socket:', error);
          messages.value.push({ 
            username: 'Sistema', 
            message: `Error: ${error.message}`, 
            timestamp: new Date(),
            isSystem: true
          });
        });

        socket.value.on('disconnect', () => {
          isConnected.value = false;
          console.log('Desconectado del servidor');
        });
      }
    };

    onMounted(() => {
      initializeChat();
    });

    onUnmounted(() => {
      if (socket.value) {
        socket.value.emit('leaveRoom', { 
          liveId: liveId.value, 
          username: username.value 
        });
        socket.value.disconnect();
      }
    });

    return {
      username, activeUsers, waitingUsers, messages, newMessage, 
      isConnected, isLiveStarted, isChef, chefName, messagesContainer,
      canSendMessages, startLiveChat, sendMessage, formatTime,
      loadingUser, userError
    };
  }
};
</script>

<style scoped>
.live-chat-container {
  display: flex;
  flex-direction: column;
  height: 100%;
  max-width: 800px;
  margin: 0 auto;
  border: 1px solid #ddd;
  border-radius: 8px;
  overflow: hidden;
}

.waiting-room, .chef-control-panel {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 20px;
  height: 100%;
  text-align: center;
}

.waiting-content {
  max-width: 400px;
}

.loader {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #3498db;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 2s linear infinite;
  margin: 20px auto;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.chef-control-panel {
  background-color: #f8f9fa;
}

.start-button {
  background-color: #28a745;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
  margin-top: 20px;
}

.start-button:hover {
  background-color: #218838;
}

.active-chat {
  display: flex;
  flex-direction: column;
  height: 100%;
}

.chat-header {
  padding: 10px;
  background-color: #f8f9fa;
  border-bottom: 1px solid #ddd;
}

.active-users {
  font-size: 0.8em;
  color: #6c757d;
}

.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 10px;
  display: flex;
  flex-direction: column;
}

.message {
  margin-bottom: 10px;
  padding: 8px 12px;
  border-radius: 8px;
  max-width: 80%;
}

.my-message {
  align-self: flex-end;
  background-color: #dcf8c6;
}

.other-message {
  align-self: flex-start;
  background-color: #f1f0f0;
}

.message-meta {
  font-size: 0.8em;
  margin-bottom: 3px;
  display: flex;
  justify-content: space-between;
}

.message-time {
  color: #6c757d;
}

.chat-input {
  display: flex;
  padding: 10px;
  border-top: 1px solid #ddd;
}

.chat-input input {
  flex: 1;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
  margin-right: 10px;
}

.chat-input button {
  background-color: #007bff;
  color: white;
  border: none;
  padding: 8px 15px;
  border-radius: 4px;
  cursor: pointer;
}

.chat-input button:disabled {
  background-color: #6c757d;
  cursor: not-allowed;
}

.connection-status {
  text-align: center;
  padding: 10px;
  background-color: #f8d7da;
  color: #721c24;
}
</style>