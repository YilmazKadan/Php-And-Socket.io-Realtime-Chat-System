const app = require('express')();
const http = require('http').createServer(app);
const io = require('socket.io')(http);

module.exports = function(app) {
  app.use(function(req, res, next) {
  res.header('Access-Control-Allow-Origin', '*');
  next();
  })};

io.on('connection', (socket) => {
  console.log('Biri bağlandı');
  socket.on('disconnect', () => {
    console.log('Biri ayrıldı');
  });
  socket.on("message", (msg) => {
    io.emit("message", msg);
  });
  socket.on("writing", (writing) => {
    io.emit("writing", writing);
  });
  socket.on("message_seen",(data)=>{
    io.emit("message_seen",data);
  })
});

http.listen(3000, () => {
  console.log('listening on *:3000');
});