const WebSocket = require('ws');
const wsPort = 8080;
const wss = new WebSocket.Server({
    port: wsPort,
});
console.log('WS server started at ' + wsPort);
const Rooms = require('../Modules/rooms');
const rooms = new Rooms(wss);

module.exports.Websocket = WebSocket;
module.exports.wss = wss;
module.exports.rooms = rooms;