<template>
  <div class="live-chat-container">
    <!-- Página de espera para usuarios normales -->
    <div v-if="!isLiveStarted && !isChef" class="waiting-room">
      <div class="waiting-content">
        <div class="waiting-animation">
          <div class="cooking-icon">👨‍🍳</div>
          <div class="pulse-animation"></div>
        </div>
        <h2>Esperando a que el chef inicie el live...</h2>
        <div class="waiting-info">
          <p><span class="info-icon">👥</span> Usuarios esperando: <strong>{{ waitingUsers.length }}</strong></p>
          <p><span class="info-icon">🍳</span> Chef: <strong>{{ chefName || 'Conectando...' }}</strong></p>
        </div>
        <div class="loader">
          <div class="dot-flashing"></div>
        </div>
      </div>
    </div>

    <!-- Panel de control del chef -->
    <div v-if="isChef && !isLiveStarted" class="chef-control-panel">
      <div class="chef-header">
        <h2><span class="chef-icon">👨‍🍳</span> Panel del Chef</h2>
        <p>Preparado para iniciar tu transmisión en vivo</p>
      </div>
      <div class="user-count">
        <span class="users-waiting">👥 {{ waitingUsers.length }} usuario(s) esperando</span>
      </div>
      <div class="video-controls-container">
        <div class="preview-container">
          <video ref="chefPreviewVideo" autoplay muted playsinline class="preview-video"
            :class="{ 'video-active': isCameraOn, 'video-inactive': !isCameraOn }"></video>
          <div class="video-overlay" v-if="!isCameraOn">
            <p>Cámara apagada</p>
          </div>
        </div>
        <div class="control-buttons">
          <button @click="toggleCamera" class="control-button camera" :class="{ 'active': isCameraOn }">
            <span class="button-icon">{{ isCameraOn ? '📷' : '📷' }}</span>
            <span class="button-text">{{ isCameraOn ? 'Cámara ON' : 'Cámara OFF' }}</span>
          </button>
          <button @click="toggleAudio" class="control-button audio" :class="{ 'active': isAudioOn }">
            <span class="button-icon">{{ isAudioOn ? '🎤' : '🎤' }}</span>
            <span class="button-text">{{ isAudioOn ? 'Audio ON' : 'Audio OFF' }}</span>
          </button>
        </div>
      </div>
      <button @click="startLiveChat" class="start-button" :disabled="!isConnected">
        <span class="button-icon">🚀</span>
        <span class="button-text">Iniciar Live Chat</span>
      </button>
    </div>

    <!-- Chat activo -->
    <div v-if="isLiveStarted" class="active-chat">
      <div class="video-container" v-if="showVideo || isChef">
        <video :ref="isChef ? 'chefLiveVideo' : 'userVideo'" autoplay :muted="isMuted || isChef" playsinline
          class="live-video" :class="{ 'video-active': isStreamActive, 'video-inactive': !isStreamActive }">
        </video>
        <div class="video-overlay" v-if="!isStreamActive && !isChef">
          <p>El chef ha apagado la cámara</p>
        </div>
        <div class="live-controls" v-if="isChef">
          <button @click="toggleCamera" class="live-control-button camera" :class="{ 'active': isCameraOn }">
            <span class="control-icon">{{ isCameraOn ? '📷' : '📷' }}</span>
          </button>
          <button @click="toggleAudio" class="live-control-button audio" :class="{ 'active': isAudioOn }">
            <span class="control-icon">{{ isAudioOn ? '🎤' : '🎤' }}</span>
          </button>
        </div>
        <div class="viewer-controls" v-if="!isChef && isStreamActive">
          <button @click="toggleMute" class="live-control-button mute" :class="{ 'active': isMuted }">
            <span class="control-icon">{{ isMuted ? '🔇' : '🔊' }}</span>
          </button>
        </div>
        <div class="viewer-count" v-if="isChef">
          <span class="eye-icon">👁️</span> {{ activeUsers.length }} espectador(es)
        </div>
      </div>

      <div class="chat-header">
        <h3>💬 Chat en vivo</h3>
        <div class="active-users">
          <span class="online-icon">🟢</span>
          <span v-for="(user, index) in activeUsers" :key="index">
            {{ user }}{{ index < activeUsers.length - 1 ? ', ' : '' }} </span>
              <span v-if="activeUsers.length === 0">No hay otros usuarios conectados</span>
        </div>
      </div>

      <div class="chat-messages" ref="messagesContainer">
        <div v-for="(msg, index) in messages" :key="index" :class="['message', msg.username === username ? 'my-message' : 'other-message',
          { 'system-message': msg.isSystem, 'chef-message': msg.isChef && !msg.isSystem }]">
          <div class="message-meta">
            <span class="user-badge" v-if="msg.isChef && !msg.isSystem">👨‍🍳</span>
            <strong v-if="msg.username !== username && !msg.isSystem">{{ msg.username }}</strong>
            <span class="message-time">{{ formatTime(msg.timestamp) }}</span>
          </div>
          <div class="message-content">{{ msg.message }}</div>
        </div>
      </div>

      <div class="chat-input">
        <input v-model="newMessage" @keyup.enter="sendMessage" placeholder="Escribe tu mensaje..."
          :disabled="!isConnected" />
        <button @click="sendMessage" :disabled="!isConnected || !newMessage.trim()" class="send-button">
          <span class="send-icon">✉️</span>
          <span class="send-text">Enviar</span>
        </button>
      </div>
    </div>

    <div v-if="!isConnected" class="connection-status">
      <div class="connection-loader"></div>
      <span>Conectando al chat...</span>
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
    const isMuted = ref(false);
    const isAudioOn = ref(false);

    const configuration = {
      iceServers: [
        { urls: 'stun:stun.l.google.com:19302' },
        { urls: 'stun:stun1.l.google.com:19302' },
        { urls: 'stun:stun2.l.google.com:19302' },
        { urls: 'stun:stun3.l.google.com:19302' },
        { urls: 'stun:stun4.l.google.com:19302' }
      ]
    };

    const toggleMute = () => {
      isMuted.value = !isMuted.value;
      if (userVideo.value) {
        userVideo.value.muted = isMuted.value;
      }
    };

    const toggleAudio = () => {
      if (localStream.value) {
        const audioTracks = localStream.value.getAudioTracks();
        if (audioTracks.length > 0) {
          isAudioOn.value = !audioTracks[0].enabled;
          audioTracks[0].enabled = !audioTracks[0].enabled;

          if (isLiveStarted.value && socket.value) {
            socket.value.emit('chefAudioStatus', {
              liveId: liveId.value,
              status: audioTracks[0].enabled
            });
          }
        }
      }
    };


    const toggleCamera = async () => {
      try {
        if (isCameraOn.value) {
          // Apagar cámara y audio
          if (localStream.value) {
            localStream.value.getTracks().forEach(track => track.stop());
            localStream.value = null;
          }
          isCameraOn.value = false;
          isAudioOn.value = false;
          isStreamActive.value = false;

          if (isLiveStarted.value && socket.value) {
            socket.value.emit('chefCameraStatus', {
              liveId: liveId.value,
              status: false
            });
            socket.value.emit('chefAudioStatus', {
              liveId: liveId.value,
              status: false
            });
          }
        } else {
          // Encender cámara y audio
          const constraints = {
            video: {
              width: { ideal: 1280 },
              height: { ideal: 720 },
              facingMode: 'user'
            },
            audio: {
              echoCancellation: true,
              noiseSuppression: true,
              autoGainControl: true
            }
          };

          const stream = await navigator.mediaDevices.getUserMedia(constraints);
          localStream.value = stream;
          isAudioOn.value = true;

          // Configurar el audio para que no esté muteado (solo para el chef)
          if (isChef.value && chefPreviewVideo.value) {
            chefPreviewVideo.value.muted = false;
            chefPreviewVideo.value.srcObject = stream;
          } else if (isChef.value && chefLiveVideo.value) {
            chefLiveVideo.value.muted = false;
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
        alert('Error al acceder a la cámara/micrófono: ' + error.message);
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

      // Solo el chef añade sus tracks
      if (isChef.value && localStream.value) {
        localStream.value.getTracks().forEach(track => {
          pc.addTrack(track, localStream.value);
        });
      }

      // Configuración para los usuarios que reciben el stream
      if (!isChef.value) {
        pc.ontrack = (event) => {
          console.log('Recibiendo tracks:', event.streams);
          if (userVideo.value && event.streams && event.streams[0]) {
            userVideo.value.srcObject = event.streams[0];

            // Forzar la reproducción del audio/video
            const playPromise = userVideo.value.play();

            if (playPromise !== undefined) {
              playPromise.then(_ => {
                console.log('Reproducción exitosa');
                isStreamActive.value = true;
              })
                .catch(error => {
                  console.error("Error al reproducir:", error);
                  // Intentar con muted si falla
                  userVideo.value.muted = true;
                  userVideo.value.play().catch(e => console.error("Error en segundo intento:", e));
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
      userVideo, isCameraOn, isAudioOn,
      toggleAudio, isStreamActive, isMuted,
      toggleMute, showVideo, toggleCamera,
      requestChefVideo
    };
  }
};
</script>

<style scoped>
/* Variables CSS */
:root {
  --primary-color: #FF6B6B;
  --secondary-color: #4ECDC4;
  --dark-color: #292F36;
  --light-color: #F7FFF7;
  --accent-color: #FFE66D;
  --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  --transition: all 0.3s ease;
  --border-radius: 12px;
  --chat-header-height: 60px;
  --chat-input-height: 80px;
}

/* Estilos base */
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  line-height: 1.6;
  color: #333;
}

/* Contenedor principal */
.live-chat-container {
  display: flex;
  flex-direction: column;
  height: 100vh;
  max-width: 1200px;
  margin: 0 auto;
  background-color: #F7F7F7;
  overflow: hidden;
}

/* Sala de espera */
.waiting-room {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100%;
  padding: 20px;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

.waiting-content {
  text-align: center;
  padding: 2rem;
  max-width: 500px;
  width: 100%;
}

.waiting-animation {
  position: relative;
  width: 120px;
  height: 120px;
  margin: 0 auto 2rem;
}

.cooking-icon {
  font-size: 4rem;
  position: relative;
  z-index: 2;
  animation: bounce 2s infinite;
}

.pulse-animation {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: var(--secondary-color);
  border-radius: 50%;
  opacity: 0.2;
  animation: pulse 2s infinite;
}

.waiting-room h2 {
  color: var(--dark-color);
  margin-bottom: 1.5rem;
  font-size: 1.5rem;
  font-weight: 600;
}

.waiting-info {
  background: white;
  padding: 1rem;
  border-radius: var(--border-radius);
  margin: 1.5rem 0;
  box-shadow: var(--shadow);
}

.waiting-info p {
  margin: 0.5rem 0;
  color: var(--dark-color);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.95rem;
}

.info-icon {
  margin-right: 10px;
  font-size: 1.2rem;
}

/* Animaciones */
@keyframes bounce {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-15px); }
}

@keyframes pulse {
  0% { transform: scale(0.8); opacity: 0.2; }
  50% { transform: scale(1.1); opacity: 0.3; }
  100% { transform: scale(0.8); opacity: 0.2; }
}

.dot-flashing {
  position: relative;
  width: 10px;
  height: 10px;
  border-radius: 5px;
  background-color: var(--primary-color);
  color: var(--primary-color);
  animation: dot-flashing 1s infinite linear alternate;
  animation-delay: 0.5s;
  margin: 20px auto;
}

.dot-flashing::before,
.dot-flashing::after {
  content: '';
  display: inline-block;
  position: absolute;
  top: 0;
  width: 10px;
  height: 10px;
  border-radius: 5px;
  background-color: var(--primary-color);
  color: var(--primary-color);
}

.dot-flashing::before {
  left: -15px;
  animation: dot-flashing 1s infinite alternate;
  animation-delay: 0s;
}

.dot-flashing::after {
  left: 15px;
  animation: dot-flashing 1s infinite alternate;
  animation-delay: 1s;
}

@keyframes dot-flashing {
  0% { opacity: 0.2; transform: scale(0.8); }
  50% { opacity: 0.5; transform: scale(1); }
  100% { opacity: 1; transform: scale(1.2); }
}

/* Panel del chef */
.chef-control-panel {
  padding: 1.5rem;
  background: white;
  border-radius: var(--border-radius);
  box-shadow: var(--shadow);
  text-align: center;
  max-width: 600px;
  width: 100%;
  margin: 1rem auto;
}

.chef-header h2 {
  color: var(--dark-color);
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
}

.chef-header p {
  color: #666;
  margin-bottom: 1.5rem;
  font-size: 0.95rem;
}

.chef-icon {
  margin-right: 10px;
}

.user-count {
  margin: 1.5rem 0;
}

.users-waiting {
  background: var(--accent-color);
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-weight: bold;
  display: inline-flex;
  align-items: center;
  font-size: 0.95rem;
}

.video-controls-container {
  margin: 1.5rem 0;
}

.preview-container {
  position: relative;
  width: 100%;
  max-width: 500px;
  margin: 0 auto;
  border-radius: var(--border-radius);
  overflow: hidden;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
  aspect-ratio: 16/9;
}

.preview-video {
  width: 100%;
  height: 100%;
  background-color: #000;
  display: block;
  object-fit: cover;
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

.control-buttons {
  display: flex;
  justify-content: center;
  gap: 1rem;
  margin-top: 1.5rem;
  flex-wrap: wrap;
}

.control-button {
  display: flex;
  align-items: center;
  padding: 0.8rem 1.5rem;
  border: none;
  border-radius: 30px;
  font-weight: bold;
  cursor: pointer;
  transition: var(--transition);
  box-shadow: var(--shadow);
  font-size: 0.95rem;
}

.control-button.camera {
  background: #f0f0f0;
  color: #333;
}

.control-button.camera.active {
  background: #4CAF50;
  color: white;
}

.control-button.audio {
  background: #f0f0f0;
  color: #333;
}

.control-button.audio.active {
  background: #2196F3;
  color: white;
}

.button-icon {
  margin-right: 8px;
  font-size: 1.1rem;
}

.start-button {
  background: var(--primary-color);
  color: white;
  border: none;
  padding: 1rem 2rem;
  font-size: 1.1rem;
  border-radius: 30px;
  cursor: pointer;
  transition: var(--transition);
  box-shadow: 0 4px 10px rgba(255, 107, 107, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 1.5rem auto 0;
  font-weight: bold;
  width: 100%;
  max-width: 300px;
}

.start-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(255, 107, 107, 0.4);
}

.start-button:disabled {
  background: #cccccc;
  transform: none;
  box-shadow: none;
  cursor: not-allowed;
}

/* Chat activo */
.active-chat {
  display: flex;
  flex-direction: column;
  height: 100%;
  background: white;
  overflow: hidden;
}

.video-container {
  position: relative;
  width: 100%;
  background-color: #000;
  aspect-ratio: 16/9;
}

.live-video {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.live-controls {
  position: absolute;
  bottom: 15px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 10px;
  background: rgba(0, 0, 0, 0.5);
  padding: 8px;
  border-radius: 20px;
}

.live-control-button {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: var(--transition);
  background: rgba(255, 255, 255, 0.2);
  color: white;
}

.live-control-button.active {
  background: var(--primary-color);
}

.live-control-button:hover {
  transform: scale(1.1);
}

.viewer-controls {
  position: absolute;
  bottom: 15px;
  right: 15px;
  display: flex;
  gap: 10px;
}

.live-control-button.mute {
  background: rgba(255, 255, 255, 0.2);
}

.live-control-button.mute.active {
  background: #FF6B6B;
}

.live-control-button.mute:hover {
  transform: scale(1.1);
}

.viewer-count {
  position: absolute;
  top: 15px;
  right: 15px;
  background: rgba(0, 0, 0, 0.5);
  color: white;
  padding: 5px 10px;
  border-radius: 20px;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
}

.eye-icon {
  margin-right: 5px;
}

.chat-header {
  padding: 1rem;
  background: var(--dark-color);
  color: white;
  min-height: var(--chat-header-height);
}

.chat-header h3 {
  margin: 0;
  display: flex;
  align-items: center;
  font-size: 1.2rem;
}

.active-users {
  font-size: 0.8rem;
  margin-top: 0.5rem;
  color: rgba(255, 255, 255, 0.8);
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  line-height: 1.4;
}

.online-icon {
  margin-right: 5px;
}

.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 1rem;
  background: #f9f9f9;
  scroll-behavior: smooth;
  min-height: 0; /* Fix for flexbox scrolling in some browsers */
}

.message {
  margin-bottom: 1rem;
  padding: 0.8rem 1rem;
  border-radius: 12px;
  max-width: 80%;
  position: relative;
  animation: fadeIn 0.3s ease-out;
  word-break: break-word;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(5px); }
  to { opacity: 1; transform: translateY(0); }
}

.my-message {
  margin-left: auto;
  background: var(--secondary-color);
  color: white;
  border-bottom-right-radius: 4px;
}

.other-message {
  margin-right: auto;
  background: white;
  color: #333;
  border-bottom-left-radius: 4px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.chef-message {
  border-left: 4px solid var(--primary-color);
}

.system-message {
  margin-left: auto;
  margin-right: auto;
  background: #f0f0f0;
  color: #666;
  text-align: center;
  max-width: 90%;
  font-size: 0.9rem;
  padding: 0.5rem 1rem;
  border-radius: 20px;
}

.message-meta {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.3rem;
  font-size: 0.8rem;
}

.my-message .message-meta {
  color: rgba(255, 255, 255, 0.8);
}

.other-message .message-meta {
  color: #666;
}

.user-badge {
  margin-right: 5px;
}

.message-time {
  opacity: 0.8;
}

.message-content {
  line-height: 1.4;
}

.chat-input {
  display: flex;
  padding: 1rem;
  background: white;
  border-top: 1px solid #eee;
  min-height: var(--chat-input-height);
  align-items: center;
}

.chat-input input {
  flex: 1;
  padding: 0.8rem 1rem;
  border: 1px solid #ddd;
  border-radius: 30px;
  margin-right: 0.5rem;
  font-size: 1rem;
  transition: var(--transition);
  min-width: 0; /* Fix for flexbox overflow */
}

.chat-input input:focus {
  outline: none;
  border-color: var(--secondary-color);
  box-shadow: 0 0 0 2px rgba(78, 205, 196, 0.2);
}

.send-button {
  padding: 0.8rem 1.5rem;
  background: var(--primary-color);
  color: white;
  border: none;
  border-radius: 30px;
  cursor: pointer;
  font-size: 1rem;
  display: flex;
  align-items: center;
  transition: var(--transition);
  white-space: nowrap;
}

.send-button:hover {
  background: #ff5252;
  transform: translateY(-2px);
}

.send-button:disabled {
  background: #cccccc;
  cursor: not-allowed;
  transform: none;
}

.send-icon {
  margin-right: 5px;
}

.connection-status {
  padding: 1rem;
  text-align: center;
  background: #fff3cd;
  color: #856404;
  border-radius: 4px;
  margin: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.connection-loader {
  width: 20px;
  height: 20px;
  border: 3px solid rgba(133, 100, 4, 0.3);
  border-radius: 50%;
  border-top-color: #856404;
  animation: spin 1s ease-in-out infinite;
  margin-right: 10px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 768px) {
  .live-chat-container {
    height: 100vh;
    border-radius: 0;
  }

  .waiting-content,
  .chef-control-panel {
    padding: 1rem;
  }

  .video-container {
    max-height: 40vh;
  }

  .message {
    max-width: 90%;
    padding: 0.6rem 0.8rem;
  font-size: 0.95rem;
  }

  .control-buttons {
    flex-direction: row;
    gap: 0.5rem;
  }

  .control-button {
    padding: 0.6rem 1rem;
    font-size: 0.9rem;
  }

  .start-button {
    padding: 0.8rem 1.5rem;
    font-size: 1rem;
  }

  .chat-header h3 {
    font-size: 1.1rem;
  }

  .active-users {
    font-size: 0.75rem;
  }

  .chat-input {
    padding: 0.8rem;
    padding-bottom: 100px;
  }

  .chat-input input {
    padding: 0.6rem 1rem;
    font-size: 0.95rem;
  }

  .send-button {
    padding: 0.6rem 1rem;
    font-size: 0.95rem;
  }
}

@media (max-width: 480px) {
  .waiting-room h2 {
    font-size: 1.2rem;
  }

  .waiting-info p {
    font-size: 0.85rem;
  }

  .chef-header h2 {
    font-size: 1.3rem;
  }

  .control-buttons {
    flex-direction: column;
    align-items: stretch;
  }

  .control-button {
    width: 100%;
    justify-content: center;
  }

  .send-text {
    display: none;
  }

  .send-button {
    width: auto;
    padding: 0.8rem;
    border-radius: 50%;
  }

  .send-icon {
    margin-right: 0;
  }
}
</style>