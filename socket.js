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

var Redis = require('ioredis');
var redis = new Redis();

io.on('connection', socketioJwt.authorize({
    secret: process.env.JWT_SECRET,
    timeout: 15000
}));

io.on('authenticated', function (socket) {

    // we can access the token props via socket.decoded_token
    // these are set in App\User::getJWTCustomClaims()
    //
    // socket.decoded_token.userid
    // socket.decoded_token.email
    // socket.decoded_token.name
    
    //socket.join('auth.info');
    //console.log('authenticated');
    
    socket.on('auth.info', function (message) {
        socket.broadcast.emit('auth.info', message);
    });

    socket.join('user.' + socket.decoded_token.userid);
     
    socket.on('disconnect', function (socket) {
        //console.log('close');
    });

});

/*
io.on('join-channel', function( socket ) {
    console.log('JOIN');
});
*/

io.on('connection', function( socket ) {
    socket.on('public.info', function (message) {
        socket.broadacast.emit('public.info', message);
    });
});


redis.subscribe('public.info');

redis.on('message', function(channel, response) {
    console.log(channel);
    let res = JSON.parse(response);
    io.sockets.emit(channel, res.data.message);
});

server.listen(process.env.SOCKETIO_PORT);
