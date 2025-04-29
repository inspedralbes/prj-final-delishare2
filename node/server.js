// server.js
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

const liveRooms = new Map();

io.on('connection', (socket) => {
  console.log('🔌 Nuevo usuario conectado:', socket.id);

  const { liveId, username, isChef } = socket.handshake.query;
  if (liveId && username) {
    socket.liveId = liveId;
    socket.username = username;
    socket.isChef = isChef === 'true';
  }

  socket.on('joinLiveRoom', ({ liveId, username, isChef }, callback) => {
    try {
      if (!liveId || !username) {
        throw new Error('Datos de conexión incompletos');
      }

      socket.liveId = liveId;
      socket.username = username;
      socket.isChef = Boolean(isChef);

      socket.join(liveId);

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

      if (socket.isChef) {
        room.chef = username;
        room.chefSocketId = socket.id;
      }

      if (room.isLiveActive && !socket.isChef && room.hasVideo && room.chefSocketId) {
        console.log(`Nuevo usuario ${socket.id} se une a sala activa con video. Notificando al chef ${room.chefSocketId}`);
        io.to(room.chefSocketId).emit('newViewer', socket.id);
      }

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

      const updateData = {
        waitingUsers: Array.from(room.waitingUsers.values()),
        activeUsers: Array.from(room.activeUsers.values()),
        chefName: room.chef,
        hasVideo: room.hasVideo,
        isLiveActive: room.isLiveActive
      };

      io.to(liveId).emit('waitingRoomUpdate', updateData);

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

  socket.on('chefStartLive', ({ liveId, hasVideo }, callback) => {
    try {
      const room = liveRooms.get(liveId);
      if (!room) throw new Error('Sala no encontrada');

      if (room.chefSocketId !== socket.id) {
        throw new Error('No autorizado para iniciar el live');
      }

      room.isLiveActive = true;
      room.hasVideo = hasVideo || false;

      room.waitingUsers.forEach((username, socketId) => {
        room.activeUsers.set(socketId, username);
      });
      room.waitingUsers.clear();

      room.activeUsers.set(socket.id, room.chef);

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

  socket.on('sendChatMessage', ({ liveId, username, message, isChef }) => {
    try {
      const room = liveRooms.get(liveId);
      if (!room) throw new Error('Sala no encontrada');

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

  socket.on('offer', ({ liveId, target, offer }) => {
    console.log(`${socket.id} envía oferta a ${target}`);
    io.to(target).emit('offer', { 
      from: socket.id, 
      offer,
      liveId 
    });
  });

  socket.on('answer', ({ liveId, target, answer }) => {
    console.log(`${socket.id} envía respuesta a ${target}`);
    io.to(target).emit('answer', { 
      from: socket.id, 
      answer,
      liveId 
    });
  });

  socket.on('iceCandidate', ({ liveId, target, candidate }) => {
    io.to(target).emit('iceCandidate', { 
      from: socket.id, 
      candidate,
      liveId 
    });
  });

  socket.on('chefCameraStatus', ({ liveId, status }) => {
    const room = liveRooms.get(liveId);
    if (room && room.chefSocketId === socket.id) {
      console.log(`Chef cambió estado de cámara a: ${status}`);
      room.hasVideo = status;
      io.to(liveId).emit('chefCameraStatus', { status });
    }
  });

  socket.on('disconnect', () => {
    try {
      if (!socket.liveId) return;

      const room = liveRooms.get(socket.liveId);
      if (!room) return;

      room.waitingUsers.delete(socket.id);
      const disconnectedUsername = room.activeUsers.get(socket.id);
      room.activeUsers.delete(socket.id);

      if (room.chefSocketId === socket.id) {
        room.chef = null;
        room.chefSocketId = null;
        room.hasVideo = false;
        
        io.to(socket.liveId).emit('chefDisconnected');
      }

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

      if (room.waitingUsers.size === 0 && room.activeUsers.size === 0) {
        liveRooms.delete(socket.liveId);
      }

    } catch (error) {
      console.error('❌ Error en disconnect:', error);
    }
  });

  socket.on('leaveRoom', ({ liveId, username }) => {
    try {
      if (!liveId) return;
      
      const room = liveRooms.get(liveId);
      if (!room) return;

      room.waitingUsers.delete(socket.id);
      room.activeUsers.delete(socket.id);

      if (room.chefSocketId === socket.id) {
        room.chef = null;
        room.chefSocketId = null;
        room.hasVideo = false;
        io.to(liveId).emit('chefDisconnected');
      }

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

      if (room.waitingUsers.size === 0 && room.activeUsers.size === 0) {
        liveRooms.delete(liveId);
      }
      
      socket.leave(liveId);
    } catch (error) {
      console.error('❌ Error en leaveRoom:', error);
    }
  });
});

http.listen(port, () => {
  console.log(`🚀 Servidor de chat corriendo en el puerto ${port}`);
});