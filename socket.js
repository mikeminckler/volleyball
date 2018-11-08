require('dotenv').config();
var fs = require('fs');

var ssl_conf = {
      key: (process.env.SOCKETIO_SSL_KEY_FILE ? fs.readFileSync(process.env.SOCKETIO_SSL_KEY_FILE) : null),
      cert: (process.env.SOCKETIO_SSL_CERT_FILE ? fs.readFileSync(process.env.SOCKETIO_SSL_CERT_FILE) : null),
      ca: (process.env.SOCKETIO_SSL_CA_FILE ? [fs.readFileSync(process.env.SOCKETIO_SSL_CA_FILE1), fs.readFileSync(process.env.SOCKETIO_SSL_CA_FILE2)] : null)
};

var server = require('http').createServer();
//var server = require('https').createServer(ssl_conf, handler);

var io = require('socket.io')(server);
var socketioJwt = require('socketio-jwt');

//var namespace = io.of('/socket');

var Redis = require('ioredis');
var redis = new Redis();

io.on('authenticated', function (socket) {

    /** we can access the token props via socket.decoded_token
     *  these are set in App\User::getJWTCustomClaims()
     *
     *  socket.decoded_token.userid
     *  socket.decoded_token.email
     *  socket.decoded_token.name
     */

    //console.log('AUTH: ' + socket.decoded_token.name);

    socket.join('user.' + socket.decoded_token.userid);

    socket.on('auth.info', function (message) {
        socket.broadcast.to('auth.info').emit('auth.info', message);
    });

    /**
     * these listeners will join and leave rooms
     * we send requests from the client to join
     */

    socket.on('join-room', function (room) {
        
        socket.join(room);
        //io.sockets.in('user.' + socket.decoded_token.userid).emit('auth.info', 'You have joined room ' + room);
        io.sockets.in(room).emit('update-room', room);

        socket.broadcast.to(room).emit('room-info', socket.decoded_token.name + ' has joined');
    });

    socket.on('leave-room', function (room) {

        socket.leave(room);
        //io.sockets.in('user.' + socket.decoded_token.userid).emit('auth.info', 'You have left room ' + room);
        io.sockets.in(room).emit('update-room', room);

        socket.broadcast.to(room).emit('room-info', socket.decoded_token.name + ' has left');
    });
     
    socket.on('room-list', function(room) {

        if (io.sockets.adapter.rooms[room] != undefined) {
            let users = [];
           
            for (let user in io.sockets.adapter.rooms[room].sockets) {
                users.push(
                    {
                        'id': io.sockets.connected[user].decoded_token.userid,
                        'name': io.sockets.connected[user].decoded_token.name
                    }
                );
            }

            io.sockets.in(room).emit('room-list', {'users': users, 'room': room});
        }

    });
});

io.on('connection', socketioJwt.authorize({
    secret: process.env.JWT_SECRET,
    timeout: 15000,
}));

io.on('connection', function( socket ) {

    socket.join('public.info');

    socket.on('public.info', function (message) {
        socket.broadcast.to('public.info').emit('public.info', message);
    });

    socket.on('join-scoreboard', function() {
        //console.log('JOIN SCOREBOARD');
        socket.join('scoreboard');
    });

});

/** this will match anything that starts with user
 *  users, or user.id
 */

redis.subscribe('auth.info');
redis.subscribe('public.info');
redis.subscribe('scoreboard');

redis.psubscribe('user.*');
redis.psubscribe('team.*');
redis.psubscribe('game.*');

redis.on('message', function(channel, message) {
    message = JSON.parse(message);
    io.emit(message.event, message.data);
});

redis.on('pmessage', function(channel, pattern, message) {

    message = JSON.parse(message);

    /** 
     * We are going to handle most game updates in Vue
     * instead of waiting for the queue to push things 
     * out to us, this will avoid the 1 second queue
     * delay for interaction on screen
     */

    if (pattern.startsWith('game')) {
        io.to(pattern).emit(message.event, message.data, send_to_self=false);
    } else {
        io.to(pattern).emit(message.event, message.data);
    }

});

server.listen(process.env.SOCKETIO_PORT);
