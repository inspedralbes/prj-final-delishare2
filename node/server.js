const express = require('express');
const app = express();
const http = require('http').createServer(app);
const { Server } = require('socket.io');

const port = process.env.PORT || 4000;

app.use(express.static('public'));

const io = new Server(http, {
  cors: {
    origin: "*",
    methods: ["GET", "POST"],
    transports: ['websocket', 'polling']
  }
});

// Estructura para manejar las salas
const liveRooms = new Map(); // liveId -> { chef, chefSocketId, waitingUsers, activeUsers, isLiveActive }


io.on('connection', (socket) => {
  console.log('🔌 Nuevo usuario conectado:', socket.id);

  // Extraer datos de la conexión
  const { liveId, username, isChef } = socket.handshake.query;
  if (liveId && username) {
    socket.liveId = liveId;
    socket.username = username;
    socket.isChef = isChef === 'true';
  }

  // Unirse a una sala de live
  socket.on('joinLiveRoom', ({ liveId, username, isChef }, callback) => {
    try {
      // Validación de datos
      if (!liveId || !username) {
        throw new Error('Datos de conexión incompletos');
      }

      // Actualizar datos del socket
      socket.liveId = liveId;
      socket.username = username;
      socket.isChef = Boolean(isChef);

      socket.join(liveId);

      // Inicializar sala si no existe
      if (!liveRooms.has(liveId)) {
        liveRooms.set(liveId, {
          chef: null,
          chefSocketId: null,
          waitingUsers: new Map(), // Ahora usamos Map para guardar socketId y username
          activeUsers: new Map(),
          isLiveActive: false
        });
      }

      const room = liveRooms.get(liveId);

      // Registrar chef si corresponde
      if (socket.isChef) {
        room.chef = username;
        room.chefSocketId = socket.id;
      }

      // Gestionar usuarios en espera/activos
      if (room.isLiveActive) {
        room.activeUsers.set(socket.id, username);
      } else {
        if (socket.isChef) {
          room.waitingUsers.delete(socket.id);
        } else {
          room.waitingUsers.set(socket.id, username);
        }
      }

      // Notificar a todos en la sala
      const updateData = {
        waitingUsers: Array.from(room.waitingUsers.values()),
        activeUsers: Array.from(room.activeUsers.values()),
        chefName: room.chef
      };

      io.to(liveId).emit('waitingRoomUpdate', updateData);

      // Notificar cuando un usuario se une (solo si el live está activo)
      if (room.isLiveActive && !socket.isChef) {
        io.to(liveId).emit('userJoined', {
          username,
          users: Array.from(room.activeUsers.values())
        });
      }

      callback && callback({ success: true });

    } catch (error) {
      console.error('❌ Error en joinLiveRoom:', error);
      callback && callback({ success: false, error: error.message });
    }
  });

  // El chef inicia el live
  socket.on('chefStartLive', ({ liveId }, callback) => {
    try {
      const room = liveRooms.get(liveId);
      if (!room) throw new Error('Sala no encontrada');

      // Verificar que sea el chef
      if (room.chefSocketId !== socket.id) {
        throw new Error('No autorizado para iniciar el live');
      }

      room.isLiveActive = true;

      // Mover todos los usuarios de waiting a active
      room.waitingUsers.forEach((username, socketId) => {
        room.activeUsers.set(socketId, username);
      });
      room.waitingUsers.clear();

      // Asegurar que el chef está en activeUsers
      room.activeUsers.set(socket.id, room.chef);

      // Notificar a todos
      const startData = {
        activeUsers: Array.from(room.activeUsers.values()),
        chefName: room.chef
      };

      io.to(liveId).emit('liveStarted', startData);
      callback && callback({ success: true });

    } catch (error) {
      console.error('❌ Error al iniciar live:', error);
      callback && callback({ success: false, message: error.message });
    }
  });

  // Mensajes del chat
  socket.on('sendChatMessage', ({ liveId, username, message, isChef }) => {
    try {
      const room = liveRooms.get(liveId);
      if (!room) throw new Error('Sala no encontrada');

      // Verificar permisos para enviar mensajes
      if (!room.isLiveActive && !isChef) {
        throw new Error('El chat no ha comenzado todavía');
      }

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

  // Manejar desconexión
  socket.on('disconnect', () => {
    try {
      if (!socket.liveId) return;

      const room = liveRooms.get(socket.liveId);
      if (!room) return;

      // Eliminar usuario de las listas
      room.waitingUsers.delete(socket.id);
      const disconnectedUsername = room.activeUsers.get(socket.id);
      room.activeUsers.delete(socket.id);

      // Si era el chef, limpiar referencia
      if (room.chefSocketId === socket.id) {
        room.chef = null;
        room.chefSocketId = null;
      }

      // Notificar a los demás solo si el live está activo
      if (room.isLiveActive && disconnectedUsername) {
        io.to(socket.liveId).emit('userLeft', {
          username: disconnectedUsername,
          users: Array.from(room.activeUsers.values()),
          message: `${disconnectedUsername} ha abandonado el chat`
        });
      } else {
        // Actualizar sala de espera
        io.to(socket.liveId).emit('waitingRoomUpdate', {
          waitingUsers: Array.from(room.waitingUsers.values()),
          activeUsers: Array.from(room.activeUsers.values()),
          chefName: room.chef
        });
      }

      // Eliminar sala si está vacía
      if (room.waitingUsers.size === 0 && room.activeUsers.size === 0) {
        liveRooms.delete(socket.liveId);
      }

    } catch (error) {
      console.error('❌ Error en disconnect:', error);
    }
  });

  http.listen(port, () => {
    console.log(`🚀 Servidor de chat corriendo en el puerto ${port}`);
  });
});