const express = require('express');
const app = express();
const http = require('http').createServer(app);
const { Server } = require('socket.io');

const port = process.env.PORT || 4000;

// Configura middleware para servir archivos estáticos desde la carpeta 'public'
app.use(express.static('public'));

// Configura el servidor Socket.IO con opciones CORS
const io = new Server(http, {
  cors: {
    origin: ["https://delishare.cat"],
    methods: ["GET", "POST"],
    credentials: true
  },
  path: "/socket.io",
  transports: ['websocket', 'polling'],
  allowEIO3: true // Compatibilidad con clientes antiguos si es necesario
});

// Mapa para almacenar las salas de transmisión en vivo
const liveRooms = new Map();
// Mapa para almacenar conexiones de usuarios (para notificaciones)
const userConnections = new Map();
const pendingNotifications = new Map();

// Maneja nuevas conexiones de socket
io.on('connection', (socket) => {
  console.log('🔌 Nuevo usuario conectado:', socket.id);

  // Extrae parámetros de conexión del handshake
  const { liveId, username, isChef, userId } = socket.handshake.query;
  if (liveId && username) {
    socket.liveId = liveId;
    socket.username = username;
    socket.isChef = isChef === 'true';
  }
  
  // Registrar conexión de usuario para notificaciones
  if (userId) {
    userConnections.set(userId, socket.id);
    console.log(`👤 Usuario ${userId} conectado (socket ${socket.id})`);
  }

  /// Maneja la solicitud de unirse a una sala de transmisión
  socket.on('joinLiveRoom', ({ liveId, username, isChef }, callback) => {
    try {
      if (!liveId || !username) {
        throw new Error('Datos de conexión incompletos');
      }

      // Asigna propiedades al socket
      socket.liveId = liveId;
      socket.username = username;
      socket.isChef = Boolean(isChef);

      socket.join(liveId);

      // Crea la sala si no existe
      if (!liveRooms.has(liveId)) {
        liveRooms.set(liveId, {
          chef: null,
          chefSocketId: null,
          waitingUsers: new Map(),
          activeUsers: new Map(),
          isLiveActive: false,
          hasVideo: false
        });
      }

      const room = liveRooms.get(liveId);

      // Si es chef, actualiza la información del chef
      if (socket.isChef) {
        room.chef = username;
        room.chefSocketId = socket.id;
      }

      // Maneja usuarios que se unen a una transmisión activa con video
      if (room.isLiveActive && !socket.isChef && room.hasVideo && room.chefSocketId) {
        console.log(`Nuevo usuario ${socket.id} se une a sala activa con video. Notificando al chef ${room.chefSocketId}`);
        io.to(room.chefSocketId).emit('newViewer', socket.id);
      }

      // Agrega usuario a la lista correspondiente
      if (room.isLiveActive) {
        room.activeUsers.set(socket.id, username);

        if (!socket.isChef && room.chefSocketId && room.hasVideo) {
          io.to(room.chefSocketId).emit('newViewer', socket.id);
        }
      } else {
        if (socket.isChef) {
          room.waitingUsers.delete(socket.id);
        } else {
          room.waitingUsers.set(socket.id, username);
        }
      }

      // Prepara datos de actualización para la sala de espera
      const updateData = {
        waitingUsers: Array.from(room.waitingUsers.values()),
        activeUsers: Array.from(room.activeUsers.values()),
        chefName: room.chef,
        hasVideo: room.hasVideo,
        isLiveActive: room.isLiveActive
      };

      io.to(liveId).emit('waitingRoomUpdate', updateData);

      // Notifica a la sala cuando un usuario se une a una transmisión activa
      if (room.isLiveActive && !socket.isChef) {
        const userList = Array.from(room.activeUsers.entries()).map(([socketId, name]) => {
          return { socketId, username: name };
        });

        io.to(liveId).emit('userJoined', {
          username,
          socketId: socket.id,
          users: Array.from(room.activeUsers.values())
        });
      }

      callback && callback({
        success: true,
        isLiveActive: room.isLiveActive,
        hasVideo: room.hasVideo
      });

    } catch (error) {
      console.error('❌ Error en joinLiveRoom:', error);
      callback && callback({ success: false, error: error.message });
    }
  });

  
/// Maneja el evento de live terminado en los clientes
socket.on('liveEnded', (data) => {
  // Este evento se manejará en el cliente para mostrar el mensaje y redirigir
  console.log('Live terminado:', data.message);
});

/// Maneja la finalización del live por parte del chef
socket.on('chefEndLive', ({ liveId }, callback) => {
  try {
    const room = liveRooms.get(liveId);
    if (!room) throw new Error('Sala no encontrada');

    if (room.chefSocketId !== socket.id) {
      throw new Error('No autorizado para finalizar el live');
    }

    // Notificar a todos que el live ha terminado
    io.to(liveId).emit('liveEnded', {
      message: 'El chef ha finalizado la transmisión en vivo. Serás redirigido...',
      endedBy: room.chef,
      redirectTo: '/live'
    });

    // Limpiar la sala
    room.waitingUsers.clear();
    room.activeUsers.clear();
    room.isLiveActive = false;
    room.hasVideo = false;
    room.chef = null;
    room.chefSocketId = null;

    // Eliminar la sala del mapa
    liveRooms.delete(liveId);

    callback({ success: true });

  } catch (error) {
    console.error('❌ Error al finalizar live:', error);
    callback({ success: false, message: error.message });
  }
});

  /// Maneja solicitudes de transmisión de video
  socket.on('requestVideoStream', ({ liveId }) => {
    try {
      console.log(`Usuario ${socket.id} solicita stream de video para sala ${liveId}`);

      const room = liveRooms.get(liveId);
      if (!room) throw new Error('Sala no encontrada');

      if (room.chefSocketId && room.hasVideo) {
        console.log(`Notificando al chef ${room.chefSocketId} sobre nuevo espectador ${socket.id}`);
        io.to(room.chefSocketId).emit('newViewer', socket.id);
      } else {
        console.log('No hay chef con video disponible');
      }
    } catch (error) {
      console.error('Error en requestVideoStream:', error);
      socket.emit('error', { message: 'Error al solicitar video: ' + error.message });
    }
  });

  /// Proporciona la lista de usuarios activos en una sala
  socket.on('requestActiveUsers', ({ liveId }, callback) => {
    try {
      const room = liveRooms.get(liveId);
      if (!room) throw new Error('Sala no encontrada');

      const activeUsersList = Array.from(room.activeUsers.entries()).map(([socketId, username]) => {
        return { socketId, username };
      });

      callback && callback({
        success: true,
        activeUsers: activeUsersList
      });
    } catch (error) {
      console.error('Error en requestActiveUsers:', error);
      callback && callback({ success: false, message: error.message });
    }
  });

  /// Maneja el inicio de una transmisión por parte del chef
  socket.on('chefStartLive', ({ liveId, hasVideo }, callback) => {
    try {
      const room = liveRooms.get(liveId);
      if (!room) throw new Error('Sala no encontrada');

      if (room.chefSocketId !== socket.id) {
        throw new Error('No autorizado para iniciar el live');
      }

      // Actualiza estado de la sala
      room.isLiveActive = true;
      room.hasVideo = hasVideo || false;

      // Mueve usuarios de espera a activos
      room.waitingUsers.forEach((username, socketId) => {
        room.activeUsers.set(socketId, username);
      });
      room.waitingUsers.clear();

      room.activeUsers.set(socket.id, room.chef);

      // Prepara datos de inicio de transmisión
      const startData = {
        activeUsers: Array.from(room.activeUsers.values()),
        chefName: room.chef,
        hasVideo: room.hasVideo
      };

      io.to(liveId).emit('liveStarted', startData);

      callback && callback({ success: true });

    } catch (error) {
      console.error('❌ Error al iniciar live:', error);
      callback && callback({ success: false, message: error.message });
    }
  });

  /// Maneja el envío de mensajes de chat
  socket.on('sendChatMessage', ({ liveId, username, message, isChef }) => {
    try {
      const room = liveRooms.get(liveId);
      if (!room) throw new Error('Sala no encontrada');

      if (!room.isLiveActive && !isChef) {
        throw new Error('El chat no ha comenzado todavía');
      }

      // Crea objeto de mensaje con timestamp
      const timestamp = new Date();
      const msgData = {
        username,
        message,
        timestamp,
        isChef
      };

      io.to(liveId).emit('newChatMessage', msgData);

    } catch (error) {
      console.error('❌ Error al enviar mensaje:', error);
      socket.emit('error', { message: error.message });
    }
  });

  /// Maneja ofertas WebRTC para conexión P2P
  socket.on('offer', ({ liveId, target, offer }) => {
    console.log(`Oferta recibida de ${socket.id} para ${target} - Tipo: ${offer.type}`);
    io.to(target).emit('offer', {
      from: socket.id,
      offer,
      liveId
    });
  });

  /// Maneja respuestas WebRTC para conexión P2P
  socket.on('answer', ({ liveId, target, answer }) => {
    console.log(`Respuesta recibida de ${socket.id} para ${target} - Tipo: ${answer.type}`);
    io.to(target).emit('answer', {
      from: socket.id,
      answer,
      liveId
    });
  });

  /// Maneja candidatos ICE para conexión WebRTC
  socket.on('iceCandidate', ({ liveId, target, candidate }) => {
    io.to(target).emit('iceCandidate', {
      from: socket.id,
      candidate,
      liveId
    });
  });

  /// Maneja cambios en el estado de la cámara del chef
  socket.on('chefCameraStatus', ({ liveId, status }) => {
    const room = liveRooms.get(liveId);
    if (room && room.chefSocketId === socket.id) {
      console.log(`Chef cambió estado de cámara a: ${status}`);
      room.hasVideo = status;
      io.to(liveId).emit('chefCameraStatus', { status });
    }
  });

  // ==============================================
  // SECCIÓN DE NOTIFICACIONES
  // ==============================================

  /**
   * Maneja la creación de nuevas notificaciones
   */
  socket.on('createNotification', async (notificationData) => {
    try {
      const { recipientId, message, recipeId, type, triggeredBy } = notificationData;
      
      console.log(`📢 Nueva notificación para usuario ${recipientId}: ${message}`);
      
      // Buscar socket del destinatario
      const recipientSocketId = userConnections.get(recipientId);
      
      const notification = {
        id: Date.now(), // Temporal, luego se reemplaza con ID real de BD
        message,
        recipe_id: recipeId,
        type,
        read: false,
        created_at: new Date().toISOString(),
        triggered_by: triggeredBy
      };

      if (recipientSocketId) {
        // Usuario conectado - enviar inmediatamente
        io.to(recipientSocketId).emit('newNotification', notification);
        console.log(`📩 Notificación enviada en tiempo real a ${recipientId}`);
      } else {
        // Usuario desconectado - guardar en pendientes
        if (!pendingNotifications.has(recipientId)) {
          pendingNotifications.set(recipientId, []);
        }
        pendingNotifications.get(recipientId).push(notification);
        console.log(`⏳ Notificación guardada para ${recipientId} (usuario desconectado)`);
      }
    } catch (error) {
      console.error('❌ Error al crear notificación:', error);
      socket.emit('notificationError', { error: error.message });
    }
  });

  /**
   * Sincronizar notificaciones con la base de datos
   */
  socket.on('syncNotifications', async ({ userId, notifications }) => {
    try {
      console.log(`🔄 Sincronizando notificaciones para usuario ${userId}`);
      // Aquí iría la lógica para sincronizar con tu backend PHP
      // Por ejemplo, marcar como leídas en la base de datos
    } catch (error) {
      console.error('❌ Error al sincronizar notificaciones:', error);
    }
  });
  /**
   * Maneja la marcación de notificaciones como leídas
   */
  socket.on('markNotificationAsRead', (notificationId) => {
    try {
      console.log(`📌 Notificación ${notificationId} marcada como leída`);
      // Aquí actualizarías el estado en tu base de datos
      // await markNotificationAsReadInDB(notificationId);
      
      socket.emit('notificationRead', { success: true, notificationId });
    } catch (error) {
      console.error('❌ Error al marcar notificación como leída:', error);
      socket.emit('notificationError', { error: error.message });
    }
  });

  /**
   * Maneja la solicitud de notificaciones no leídas
   */
  socket.on('getUnreadNotifications', async (userId) => {
    try {
      console.log(`🔍 Solicitando notificaciones no leídas para usuario ${userId}`);
      // Aquí obtendrías las notificaciones de tu base de datos
      // const notifications = await getUnreadNotificationsFromDB(userId);
      const mockNotifications = [
        {
          id: 1,
          message: "Tienes un nuevo comentario en tu receta",
          recipe_id: 123,
          read: false,
          created_at: new Date().toISOString()
        }
      ];
      
      socket.emit('unreadNotifications', mockNotifications);
    } catch (error) {
      console.error('❌ Error al obtener notificaciones:', error);
      socket.emit('notificationError', { error: error.message });
    }
  });

  /// Maneja la desconexión de un socket
  socket.on('disconnect', () => {
    try {
      console.log('🔌 Usuario desconectado:', socket.id);
      
      if (!socket.liveId) return;

      const room = liveRooms.get(socket.liveId);
      if (room) {
        // Elimina usuario de las listas
        room.waitingUsers.delete(socket.id);
        const disconnectedUsername = room.activeUsers.get(socket.id);
        room.activeUsers.delete(socket.id);

        // Maneja desconexión del chef
        if (room.chefSocketId === socket.id) {
          room.chef = null;
          room.chefSocketId = null;
          room.hasVideo = false;

          io.to(socket.liveId).emit('chefDisconnected');
        }

        // Notifica a la sala sobre la desconexión
        if (room.isLiveActive && disconnectedUsername) {
          io.to(socket.liveId).emit('userLeft', {
            username: disconnectedUsername,
            socketId: socket.id,
            users: Array.from(room.activeUsers.values()),
            message: `${disconnectedUsername} ha abandonado el chat`
          });
        } else {
          io.to(socket.liveId).emit('waitingRoomUpdate', {
            waitingUsers: Array.from(room.waitingUsers.values()),
            activeUsers: Array.from(room.activeUsers.values()),
            chefName: room.chef,
            hasVideo: room.hasVideo
          });
        }

        // Elimina sala si está vacía
        if (room.waitingUsers.size === 0 && room.activeUsers.size === 0) {
          liveRooms.delete(socket.liveId);
        }
      }
      
      // Eliminar de userConnections si existe
      userConnections.forEach((socketId, userId) => {
        if (socketId === socket.id) {
          userConnections.delete(userId);
          console.log(`👤 Usuario ${userId} desconectado (socket ${socketId})`);
        }
      });
    } catch (error) {
      console.error('❌ Error en disconnect:', error);
    }
  });

  /// Maneja la salida voluntaria de una sala
  socket.on('leaveRoom', ({ liveId, username }) => {
    try {
      if (!liveId) return;

      const room = liveRooms.get(liveId);
      if (!room) return;

      // Elimina usuario de las listas
      room.waitingUsers.delete(socket.id);
      room.activeUsers.delete(socket.id);

      // Maneja salida del chef
      if (room.chefSocketId === socket.id) {
        room.chef = null;
        room.chefSocketId = null;
        room.hasVideo = false;
        io.to(liveId).emit('chefDisconnected');
      }

      // Notifica a la sala sobre la salida
      if (room.isLiveActive) {
        io.to(liveId).emit('userLeft', {
          username: username,
          socketId: socket.id,
          users: Array.from(room.activeUsers.values())
        });
      } else {
        io.to(liveId).emit('waitingRoomUpdate', {
          waitingUsers: Array.from(room.waitingUsers.values()),
          activeUsers: Array.from(room.activeUsers.values()),
          chefName: room.chef,
          hasVideo: room.hasVideo
        });
      }

      // Elimina sala si está vacía
      if (room.waitingUsers.size === 0 && room.activeUsers.size === 0) {
        liveRooms.delete(liveId);
      }

      socket.leave(liveId);

    } catch (error) {
      console.error('❌ Error en leaveRoom:', error);
    }
  });
});

// Inicia el servidor HTTP
http.listen(port, () => {
  console.log(`🚀 Servidor de chat corriendo en el puerto ${port}`);
});