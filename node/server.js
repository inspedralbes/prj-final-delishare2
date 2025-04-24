const express = require('express');
const app = express();
const http = require('http').createServer(app);
const { Server } = require('socket.io');

const port = process.env.PORT || 4000;

app.use(express.static('public'));

const io = new Server(http, {
  cors: {
    origin: "*",
    methods: ["GET", "POST"]
  }
});

// Mapa para guardar las salas y sus usuarios
const liveRooms = new Map();

io.on('connection', (socket) => {
  console.log('Nuevo usuario conectado:', socket.id);

  // Unirse a una sala de live
  socket.on('joinLiveRoom', async ({ liveId, username }) => {
    // Guardar el username en el socket
    socket.username = username;
    socket.liveId = liveId;

    // Unirse a la sala
    socket.join(liveId);

    // Registrar usuario en la sala
    if (!liveRooms.has(liveId)) {
      liveRooms.set(liveId, new Set());
    }
    liveRooms.get(liveId).add(username);

    // Notificar a la sala
    io.to(liveId).emit('userJoined', {
      username,
      users: Array.from(liveRooms.get(liveId)),
      message: `${username} se ha unido al chat`
    });

    console.log(`${username} se unió a la sala ${liveId}`);
  });

  // Manejar mensajes del chat
  socket.on('sendChatMessage', ({ liveId, username, message }) => {
    const timestamp = new Date();
    io.to(liveId).emit('newChatMessage', {
      username,
      message,
      timestamp
    });
  });

  // Manejar desconexión
  socket.on('disconnect', () => {
    if (socket.liveId && socket.username) {
      const { liveId, username } = socket;

      if (liveRooms.has(liveId)) {
        liveRooms.get(liveId).delete(username);

        // Notificar a la sala
        io.to(liveId).emit('userLeft', {
          username,
          users: Array.from(liveRooms.get(liveId)),
          message: `${username} ha abandonado el chat`
        });

        // Eliminar sala si está vacía
        if (liveRooms.get(liveId).size === 0) {
          liveRooms.delete(liveId);
        }
      }
    }
    console.log('Usuario desconectado:', socket.id);
  });
});

http.listen(port, () => {
  console.log(`Servidor de chat corriendo en el puerto ${port}`);
});