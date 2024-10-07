const http = require('http');
const io = require('socket.io');

const server = http.createServer((req, res) => {
  res.writeHead(200, {'Content-Type': 'text/plain'});
  res.end('Socket.IO server running\n');
});

const socketServer = io(server);

socketServer.on('connection', socket => {
  console.log('A user connected');

  socket.on('new-comment', (data) => {
    console.log('New comment:', data);
    socketServer.emit('new-comment', data); // Broadcast to all clients
  });

  socket.on('new-notification', (data) => {
    console.log('New notification:', data);
    socketServer.emit('new-notification', data); // Broadcast to all clients
  });

  socket.on('disconnect', () => {
    console.log('A user disconnected');
  });
});

server.listen(3000, () => {
  console.log('Socket.IO server running on port 3000');
});
