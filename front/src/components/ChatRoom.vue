<template>
    <div class="live-chat-container">
      <!-- Cabecera del chat -->
      <div class="chat-header">
        <h3>Chat en vivo</h3>
        <div class="active-users">
          <span v-for="(user, index) in activeUsers" :key="index">
            {{ user }}{{ index < activeUsers.length - 1 ? ', ' : '' }}
          </span>
          <span v-if="activeUsers.length === 0">No hay otros usuarios conectados</span>
        </div>
      </div>
  
      <!-- Mensajes del chat -->
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
  
      <!-- Entrada de mensajes -->
      <div class="chat-input">
        <input
          v-model="newMessage"
          @keyup.enter="sendMessage"
          placeholder="Escribe tu mensaje..."
          :disabled="!isConnected"
        />
        <button @click="sendMessage" :disabled="!isConnected || !newMessage.trim()">
          Enviar
        </button>
      </div>
  
      <div v-if="!isConnected" class="connection-status">
        🔄 Conectando al chat...
      </div>
    </div>
  </template>
  
  <script>
  import { ref, onMounted, onUnmounted, nextTick } from 'vue';
  import { io } from 'socket.io-client';
  import communicationManager from '@/services/communicationManager';
  
  export default {
    props: {
      liveId: {
        type: String,
        required: true
      }
    },
    
    setup(props) {
      const socket = ref(null);
      const username = ref('');
      const activeUsers = ref([]);
      const messages = ref([]);
      const newMessage = ref('');
      const isConnected = ref(false);
      const messagesContainer = ref(null);
  
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
  
      const sendMessage = () => {
        if (!newMessage.value.trim() || !isConnected.value) return;
  
        socket.value.emit('sendChatMessage', {
          liveId: props.liveId,
          username: username.value,
          message: newMessage.value
        });
  
        newMessage.value = '';
      };
  
      const initializeChat = async () => {
        try {
          // Obtener el nombre de usuario automáticamente
          const userData = await communicationManager.getUser();
          username.value = userData?.name || 'Anónimo';
  
          // Conectar al servidor de chat
          socket.value = io('http://localhost:4000', {
            reconnection: true,
            reconnectionAttempts: Infinity,
            reconnectionDelay: 1000,
          });
  
          // Configurar eventos del socket
          socket.value.on('connect', () => {
            isConnected.value = true;
            // Unirse a la sala del live
            socket.value.emit('joinLiveRoom', {
              liveId: props.liveId,
              username: username.value
            });
          });
  
          socket.value.on('disconnect', () => {
            isConnected.value = false;
          });
  
          socket.value.on('connect_error', (err) => {
            console.error('Error de conexión:', err);
            isConnected.value = false;
          });
  
          socket.value.on('newChatMessage', (msg) => {
            messages.value.push(msg);
            scrollToBottom();
          });
  
          socket.value.on('userJoined', (data) => {
            activeUsers.value = data.users;
            messages.value.push({
              username: 'Sistema',
              message: data.message,
              timestamp: new Date()
            });
            scrollToBottom();
          });
  
          socket.value.on('userLeft', (data) => {
            activeUsers.value = data.users;
            messages.value.push({
              username: 'Sistema',
              message: data.message,
              timestamp: new Date()
            });
            scrollToBottom();
          });
  
        } catch (error) {
          console.error('Error inicializando chat:', error);
        }
      };
  
      onMounted(() => {
        initializeChat();
      });
  
      onUnmounted(() => {
        if (socket.value) {
          socket.value.disconnect();
        }
      });
  
      return {
        username,
        activeUsers,
        messages,
        newMessage,
        isConnected,
        messagesContainer,
        formatTime,
        sendMessage
      };
    }
  };
  </script>
  
  <style scoped>
  .live-chat-container {
    display: flex;
    flex-direction: column;
    height: 100%;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
  
  .chat-header {
    padding: 12px 16px;
    background-color: #f8f9fa;
    border-bottom: 1px solid #e0e0e0;
  }
  
  .chat-header h3 {
    margin: 0 0 4px 0;
    font-size: 1.1rem;
    color: #333;
  }
  
  .active-users {
    font-size: 0.85rem;
    color: #666;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  
  .chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 12px 16px;
    background-color: #fff;
  }
  
  .message {
    margin-bottom: 12px;
    padding: 8px 12px;
    border-radius: 8px;
    max-width: 80%;
    word-wrap: break-word;
  }
  
  .my-message {
    background-color: #e3f2fd;
    margin-left: auto;
    border-bottom-right-radius: 0;
  }
  
  .other-message {
    background-color: #f1f1f1;
    margin-right: auto;
    border-bottom-left-radius: 0;
  }
  
  .message-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 4px;
  }
  
  .message-meta strong {
    font-size: 0.85rem;
    color: #555;
  }
  
  .message-time {
    font-size: 0.75rem;
    color: #777;
  }
  
  .message-content {
    font-size: 0.95rem;
    line-height: 1.4;
  }
  
  .chat-input {
    display: flex;
    padding: 12px;
    border-top: 1px solid #e0e0e0;
    background-color: #f8f9fa;
  }
  
  .chat-input input {
    flex: 1;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 20px;
    outline: none;
    font-size: 0.95rem;
  }
  
  .chat-input input:focus {
    border-color: #4dabf7;
  }
  
  .chat-input button {
    margin-left: 8px;
    padding: 8px 16px;
    background-color: #4dabf7;
    color: white;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    font-size: 0.95rem;
    transition: background-color 0.2s;
  }
  
  .chat-input button:hover {
    background-color: #339af0;
  }
  
  .chat-input button:disabled {
    background-color: #adb5bd;
    cursor: not-allowed;
  }
  
  .connection-status {
    padding: 8px;
    text-align: center;
    font-size: 0.85rem;
    color: #666;
    background-color: #fff3bf;
  }
  </style>