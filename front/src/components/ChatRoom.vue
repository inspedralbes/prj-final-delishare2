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
      <div class="video-controls">
        <button @click="toggleCamera" class="camera-button">
          {{ isCameraOn ? '📷 Apagar Cámara' : '📷 Encender Cámara' }}
        </button>
        <video ref="chefPreviewVideo" autoplay muted playsinline class="preview-video"
          :class="{ 'video-active': isCameraOn, 'video-inactive': !isCameraOn }"></video>
      </div>
      <button @click="startLiveChat" class="start-button" :disabled="!isConnected">
        🚀 Iniciar Live Chat
      </button>
    </div>

    <!-- Chat activo -->
    <div v-if="isLiveStarted" class="active-chat">
      <div class="video-container" v-if="showVideo || isChef">
        <video :ref="isChef ? 'chefLiveVideo' : 'userVideo'" autoplay :muted="isChef" playsinline class="live-video"
          :class="{ 'video-active': isStreamActive, 'video-inactive': !isStreamActive }">
        </video>
        <div class="video-overlay" v-if="!isStreamActive && !isChef">
          <p>El chef ha apagado la cámara</p>
        </div>
        <div class="video-controls" v-if="isChef">
          <button @click="toggleCamera" class="camera-button">
            {{ isCameraOn ? '📷 Apagar Cámara' : '📷 Encender Cámara' }}
          </button>
        </div>
      </div>

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
        <input v-model="newMessage" @keyup.enter="sendMessage" placeholder="Escribe tu mensaje..."
          :disabled="!isConnected" />
        <button @click="sendMessage" :disabled="!isConnected || !newMessage.trim()">Enviar</button>
      </div>
    </div>

    <div v-if="!isConnected" class="connection-status">
      🔄 Conectando al chat...
    </div>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted, nextTick, computed, watch } from 'vue';
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
    const isChef = ref(false);
    const chefName = ref('');
    const messagesContainer = ref(null);
    const loadingUser = ref(true);
    const userError = ref(null);
    const chefPreviewVideo = ref(null);
    const chefLiveVideo = ref(null);
    const userVideo = ref(null);
    const isCameraOn = ref(false);
    const isStreamActive = ref(false);
    const showVideo = ref(true);
    const peerConnections = ref({});
    const localStream = ref(null);
    const videoInitialized = ref(false);

    const configuration = {
      iceServers: [
        { urls: 'stun:stun.l.google.com:19302' },
        { urls: 'stun:stun1.l.google.com:19302' },
        { urls: 'stun:stun2.l.google.com:19302' },
        { urls: 'stun:stun3.l.google.com:19302' },
        { urls: 'stun:stun4.l.google.com:19302' }
      ]
    };

    const toggleCamera = async () => {
      try {
        if (isCameraOn.value) {
          if (localStream.value) {
            localStream.value.getTracks().forEach(track => track.stop());
            localStream.value = null;
          }
          isCameraOn.value = false;
          isStreamActive.value = false;

          if (isLiveStarted.value && socket.value) {
            socket.value.emit('chefCameraStatus', {
              liveId: liveId.value,
              status: false
            });
          }

          if (chefPreviewVideo.value) {
            chefPreviewVideo.value.srcObject = null;
          }
          if (chefLiveVideo.value) {
            chefLiveVideo.value.srcObject = null;
          }

          closeAllPeerConnections();
        } else {
          const constraints = {
            video: {
              width: { ideal: 1280 },
              height: { ideal: 720 },
              facingMode: 'user'
            },
            audio: true
          };

          const stream = await navigator.mediaDevices.getUserMedia(constraints);
          localStream.value = stream;

          if (!isLiveStarted.value && chefPreviewVideo.value) {
            chefPreviewVideo.value.srcObject = stream;
          } else if (isLiveStarted.value && chefLiveVideo.value) {
            chefLiveVideo.value.srcObject = stream;
          }

          isCameraOn.value = true;
          isStreamActive.value = true;

          if (isLiveStarted.value && socket.value) {
            socket.value.emit('chefCameraStatus', {
              liveId: liveId.value,
              status: true
            });
            initializeConnectionsWithViewers();
          }
        }
      } catch (error) {
        console.error('Error al cambiar estado de la cámara:', error);
        alert('Error al acceder a la cámara: ' + error.message);
      }
    };

    const closeAllPeerConnections = () => {
      Object.keys(peerConnections.value).forEach(socketId => {
        if (peerConnections.value[socketId]) {
          peerConnections.value[socketId].close();
          delete peerConnections.value[socketId];
        }
      });
    };

    const initializeConnectionsWithViewers = () => {
      if (!isChef.value || !localStream.value || !socket.value) return;

      socket.value.emit('requestActiveUsers', { liveId: liveId.value }, (response) => {
        if (response?.success && response.activeUsers) {
          response.activeUsers.forEach(user => {
            if (user.socketId && user.socketId !== socket.value.id) {
              console.log(`Iniciando conexión con: ${user.username} (${user.socketId})`);
              startCall(user.socketId);
            }
          });
        }
      });
    };

    const createPeerConnection = (socketId) => {
      if (peerConnections.value[socketId]) {
        peerConnections.value[socketId].close();
      }

      console.log(`Creando nueva conexión peer con: ${socketId}`);
      const pc = new RTCPeerConnection(configuration);
      peerConnections.value[socketId] = pc;

      pc.onicecandidate = (event) => {
        if (event.candidate) {
          socket.value.emit('iceCandidate', {
            liveId: liveId.value,
            target: socketId,
            candidate: event.candidate
          });
        }
      };

      if (isChef.value && localStream.value) {
        console.log(`Chef: agregando ${localStream.value.getTracks().length} tracks a la conexión con ${socketId}`);
        localStream.value.getTracks().forEach(track => {
          pc.addTrack(track, localStream.value);
        });
      }

      if (!isChef.value) {
        pc.ontrack = (event) => {
          console.log(`Usuario: track recibido de tipo: ${event.track.kind}`);
          
          if (userVideo.value && event.streams && event.streams[0]) {
            console.log('Asignando stream al elemento video');
            const videoElement = userVideo.value;
            videoElement.srcObject = event.streams[0];
            
            // Solución para el error de reproducción
            const playPromise = videoElement.play();
            
            if (playPromise !== undefined) {
              playPromise.then(_ => {
                console.log('Video reproducido con éxito');
                isStreamActive.value = true;
              })
              .catch(error => {
                console.warn("Error al reproducir video:", error);
                // Intentar nuevamente con muted
                videoElement.muted = true;
                videoElement.play().catch(e => console.warn("Segundo intento fallido:", e));
              });
            }
          }
        };
      }

      return pc;
    };

    const startCall = async (socketId) => {
      if (!isChef.value || !localStream.value || !socket.value) {
        console.log('No se puede iniciar llamada: condiciones no cumplidas');
        return;
      }

      console.log(`Chef iniciando llamada con: ${socketId}`);
      const pc = createPeerConnection(socketId);

      const senders = pc.getSenders();
      if (senders.length === 0 && localStream.value) {
        console.log('No hay senders, agregando tracks manualmente');
        localStream.value.getTracks().forEach(track => {
          pc.addTrack(track, localStream.value);
        });
      }

      try {
        const offer = await pc.createOffer({
          offerToReceiveAudio: true,
          offerToReceiveVideo: true
        });

        await pc.setLocalDescription(offer);
        console.log('Oferta creada y enviada:', offer.type);

        socket.value.emit('offer', {
          liveId: liveId.value,
          target: socketId,
          offer: pc.localDescription
        });
      } catch (error) {
        console.error('Error al crear oferta:', error);
      }
    };

    const handleOffer = async (data) => {
      if (!socket.value) return;

      console.log(`Recibida oferta de: ${data.from}`);
      const pc = createPeerConnection(data.from);

      try {
        await pc.setRemoteDescription(new RTCSessionDescription(data.offer));
        console.log('Descripción remota configurada');

        const answer = await pc.createAnswer();
        await pc.setLocalDescription(answer);
        console.log('Respuesta creada y enviada:', answer.type);

        socket.value.emit('answer', {
          liveId: liveId.value,
          target: data.from,
          answer: pc.localDescription
        });
      } catch (error) {
        console.error('Error al manejar oferta:', error);
      }
    };

    const handleAnswer = async (data) => {
      const pc = peerConnections.value[data.from];
      if (!pc) {
        console.log(`No hay conexión para respuesta de ${data.from}`);
        return;
      }

      try {
        console.log(`Recibida respuesta de: ${data.from}`);
        await pc.setRemoteDescription(new RTCSessionDescription(data.answer));
        console.log('Descripción remota configurada con respuesta');
      } catch (error) {
        console.error('Error al manejar respuesta:', error);
      }
    };

    const handleIceCandidate = async (data) => {
      const pc = peerConnections.value[data.from];
      if (!pc || !data.candidate) {
        return;
      }

      try {
        console.log(`Recibido candidato ICE de: ${data.from}`);
        await pc.addIceCandidate(new RTCIceCandidate(data.candidate));
      } catch (error) {
        console.error('Error al agregar ICE candidate:', error);
      }
    };

    const fetchUserData = async () => {
      try {
        loadingUser.value = true;
        userError.value = null;

        const token = localStorage.getItem('token');
        if (!token) {
          throw new Error('No estás autenticado');
        }

        const userData = await communicationManager.getUser();

        if (!userData || !userData.id) {
          throw new Error('Datos de usuario incompletos');
        }

        username.value = userData.name || 'Usuario';
        isChef.value = userData.role === 'chef';

        console.log(`Usuario identificado: ${username.value}, Role: ${userData.role}`);
      } catch (error) {
        console.error('Error al obtener datos del usuario:', error);
        userError.value = error.message || 'Error al cargar datos de usuario';
      } finally {
        loadingUser.value = false;
      }
    };

    const startLiveChat = () => {
      if (!isChef.value || !isConnected.value || !socket.value) return;

      console.log('Iniciando live chat...');

      const hasVideo = isCameraOn.value && localStream.value !== null;

      if (hasVideo && !isLiveStarted.value) {
        nextTick(() => {
          if (chefLiveVideo.value && localStream.value) {
            chefLiveVideo.value.srcObject = localStream.value;
          }
        });
      }

      socket.value.emit('chefStartLive', {
        liveId: liveId.value,
        hasVideo: hasVideo
      }, (response) => {
        if (response?.success) {
          isLiveStarted.value = true;
          showVideo.value = hasVideo;
          isStreamActive.value = hasVideo;

          console.log('Live iniciado con éxito!');

          if (hasVideo) {
            nextTick(() => {
              initializeConnectionsWithViewers();
            });
          }
        } else {
          console.error('Error al iniciar el live:', response?.message);
        }
      });
    };

    watch(isLiveStarted, (newValue) => {
      if (newValue && isChef.value && isCameraOn.value && localStream.value) {
        nextTick(() => {
          if (chefLiveVideo.value) {
            console.log('Transfiriendo stream al video en vivo');
            chefLiveVideo.value.srcObject = localStream.value;
          }
        });
      }
    });

    const sendMessage = () => {
      if (!newMessage.value.trim() || !isConnected.value || !socket.value) return;

      const messageData = {
        liveId: liveId.value,
        username: username.value,
        message: newMessage.value.trim(),
        isChef: isChef.value
      };

      socket.value.emit('sendChatMessage', messageData);

      messages.value.push({
        username: username.value,
        message: newMessage.value.trim(),
        timestamp: new Date(),
        isChef: isChef.value
      });

      newMessage.value = '';
      scrollToBottom();
    };

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

    const requestChefVideo = () => {
      if (!isChef.value && socket.value && isLiveStarted.value && showVideo.value && !videoInitialized.value) {
        console.log('Usuario solicitando video del chef...');
        socket.value.emit('requestVideoStream', { liveId: liveId.value });
      }
    };

    const initializeChat = async () => {
      await fetchUserData();

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

              if (response.isLiveActive) {
                isLiveStarted.value = true;
                showVideo.value = response.hasVideo || false;
                isStreamActive.value = response.hasVideo || false;

                if (response.hasVideo && !isChef.value) {
                  setTimeout(() => {
                    requestChefVideo();
                  }, 1000);
                }
              }
            }
          });
        });

        socket.value.on('liveStarted', (data) => {
          console.log('Live iniciado:', data);
          isLiveStarted.value = true;
          activeUsers.value = data.activeUsers || [];
          showVideo.value = data.hasVideo || false;
          isStreamActive.value = data.hasVideo || false;

          messages.value.push({
            username: 'Sistema',
            message: '¡El chat en vivo ha comenzado!',
            timestamp: new Date(),
            isSystem: true
          });

          if (!isChef.value) {
            if (data.hasVideo) {
              console.log('Solicitando stream de video al iniciar live');
              socket.value.emit('requestVideoStream', { liveId: liveId.value });
            }
            
            const helloMessage = {
              liveId: liveId.value,
              username: username.value,
              message: 'Hola!',
              isChef: isChef.value
            };
            socket.value.emit('sendChatMessage', helloMessage);
            
            messages.value.push({
              username: username.value,
              message: 'Hola!',
              timestamp: new Date(),
              isChef: isChef.value
            });
          }

          scrollToBottom();
        });

        socket.value.on('waitingRoomUpdate', (data) => {
          console.log('Actualización sala de espera:', data);
          waitingUsers.value = data.waitingUsers || [];
          activeUsers.value = data.activeUsers || [];
          chefName.value = data.chefName || 'Esperando chef...';

          if (data.hasVideo !== undefined) {
            showVideo.value = data.hasVideo;
          }
        });

        socket.value.on('offer', handleOffer);
        socket.value.on('answer', handleAnswer);
        socket.value.on('iceCandidate', handleIceCandidate);

        socket.value.on('chefCameraStatus', (data) => {
          console.log('Estado de cámara del chef actualizado:', data.status);
          showVideo.value = data.status;
          isStreamActive.value = data.status;

          messages.value.push({
            username: 'Sistema',
            message: data.status ? 'El chef ha encendido la cámara' : 'El chef ha apagado la cámara',
            timestamp: new Date(),
            isSystem: true
          });

          if (data.status && !isChef.value) {
            requestChefVideo();
          } else if (!data.status && !isChef.value && userVideo.value) {
            userVideo.value.srcObject = null;
            videoInitialized.value = false;
          }
          
          scrollToBottom();
        });

        socket.value.on('newViewer', (socketId) => {
          console.log('Nuevo espectador conectado:', socketId);
          if (isChef.value && isLiveStarted.value && isCameraOn.value && localStream.value) {
            console.log(`Chef iniciando llamada con usuario ${socketId}`);
            setTimeout(() => {
              startCall(socketId);
            }, 500);
          }
        });

        socket.value.on('chefDisconnected', () => {
          if (!isChef.value) {
            messages.value.push({
              username: 'Sistema',
              message: 'El chef se ha desconectado.',
              timestamp: new Date(),
              isSystem: true
            });

            if (userVideo.value) {
              userVideo.value.srcObject = null;
              videoInitialized.value = false;
            }
            isStreamActive.value = false;
            scrollToBottom();
          }
        });

        socket.value.on('newChatMessage', (msg) => {
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
          
          if (isChef.value && isCameraOn.value && localStream.value && data.socketId) {
            startCall(data.socketId);
          }
          
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

          if (isChef.value && data.socketId && peerConnections.value[data.socketId]) {
            peerConnections.value[data.socketId].close();
            delete peerConnections.value[data.socketId];
          }

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

      closeAllPeerConnections();

      if (localStream.value) {
        localStream.value.getTracks().forEach(track => track.stop());
      }
    });

    const canSendMessages = computed(() => {
      return isConnected.value && (isLiveStarted.value || isChef.value);
    });

    return {
      username, activeUsers, waitingUsers, messages, newMessage,
      isConnected, isLiveStarted, isChef, chefName, messagesContainer,
      canSendMessages, startLiveChat, sendMessage, formatTime,
      loadingUser, userError, chefPreviewVideo, chefLiveVideo,
      userVideo, isCameraOn, isStreamActive, showVideo, toggleCamera,
      requestChefVideo
    };
  }
};
</script>

<style scoped>
.live-chat-container {
  display: flex;
  flex-direction: column;
  height: 100vh;
  max-width: 1200px;
  margin: 0 auto;
  background-color: #f5f5f5;
}

.waiting-room {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100%;
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.waiting-content {
  text-align: center;
  padding: 2rem;
}

.waiting-info {
  margin: 1.5rem 0;
  font-size: 1.1rem;
  color: #555;
}

.loader {
  border: 5px solid #f3f3f3;
  border-top: 5px solid #3498db;
  border-radius: 50%;
  width: 50px;
  height: 50px;
  animation: spin 1s linear infinite;
  margin: 2rem auto;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.chef-control-panel {
  padding: 2rem;
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.user-count {
  margin: 1rem 0;
  font-size: 1.2rem;
  font-weight: bold;
}

.video-controls {
  margin: 1.5rem 0;
}

.preview-video {
  width: 100%;
  max-width: 500px;
  border-radius: 8px;
  margin-top: 1rem;
  background-color: #000;
}

.video-active {
  display: block;
}

.video-inactive {
  display: none;
}

.start-button {
  background-color: #4CAF50;
  color: white;
  border: none;
  padding: 0.8rem 1.5rem;
  font-size: 1.1rem;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.start-button:hover {
  background-color: #45a049;
}

.start-button:disabled {
  background-color: #cccccc;
  cursor: not-allowed;
}

.active-chat {
  display: flex;
  flex-direction: column;
  height: 100%;
}

.video-container {
  position: relative;
  width: 100%;
  background-color: #000;
}

.live-video {
  width: 100%;
  max-height: 500px;
  object-fit: contain;
}

.video-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: rgba(0, 0, 0, 0.7);
  color: white;
  font-size: 1.2rem;
}

.chat-header {
  padding: 1rem;
  background-color: #fff;
  border-bottom: 1px solid #eee;
}

.active-users {
  font-size: 0.9rem;
  color: #666;
  margin-top: 0.5rem;
}

.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 1rem;
  background-color: #fff;
}

.message {
  margin-bottom: 1rem;
  padding: 0.8rem;
  border-radius: 8px;
  max-width: 80%;
}

.my-message {
  margin-left: auto;
  background-color: #dcf8c6;
}

.other-message {
  margin-right: auto;
  background-color: #f1f1f1;
}

.message-meta {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.3rem;
  font-size: 0.8rem;
}

.message-time {
  color: #666;
}

.message-content {
  word-wrap: break-word;
}

.chat-input {
  display: flex;
  padding: 1rem;
  background-color: #fff;
  border-top: 1px solid #eee;
}

.chat-input input {
  flex: 1;
  padding: 0.8rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  margin-right: 0.5rem;
  font-size: 1rem;
}

.chat-input button {
  padding: 0.8rem 1.5rem;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
}

.chat-input button:disabled {
  background-color: #cccccc;
  cursor: not-allowed;
}

.connection-status {
  padding: 1rem;
  text-align: center;
  background-color: #fff3cd;
  color: #856404;
  border-radius: 4px;
  margin: 1rem;
}

.camera-button {
  background-color: #2196F3;
  color: white;
  border: none;
  padding: 0.6rem 1rem;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.9rem;
  margin-right: 0.5rem;
}

.camera-button:hover {
  background-color: #0b7dda;
}
</style>