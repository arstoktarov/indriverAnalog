const db = require('./Modules/db');
const validate = require('validate.js');
const consoleMsg = require('./Modules/consoleMsg');
const wsUserModule = require('./Modules/WSUser');
const functions = require('./Modules/additionalFunctions');
const app = require('express')();
let server = require('http').Server(app);
const port = 3000;
const WebSocket = require('ws');
const wss = new WebSocket.Server({
    port: 8080,
});
server.listen(port, 'localhost');
consoleMsg.info('Server started at ' + port);
const roomModule = require('./Modules/rooms');
let rooms = new roomModule.roomsModel(wss);

let sockets = new Set();
let realUsers = new Map();
let activeOrders = new Map();


const User = require('./Models/User');


wss.on('connection',  function connection(ws) {
    let wsUser = new wsUserModule.User(ws);
    sockets.add(wsUser);
    consoleMsg.info('New connection catched. Current clients count: ' + wss.clients.size);

    wsUser.ws.ping();
    wsUser.isOnline = true;
    let pinger = null;
    wsUser.on('pong', function() {
        clearTimeout(pinger);
        wsUser.isOnline = true;
        pinger = setTimeout(function() {
            if (wsUser.isOnline === false) {
                wsUser.ws.close();
            }
            else {
                wsUser.isOnline = false;
                wsUser.ws.ping();
            }
        }, 1000);
    });

    wsUser.on('message', function (json) {
        consoleMsg.log("MESSAGE: " + json);
        let data = wsUserModule.User.parseJson(json);
        let constraints = {
            "event": {presence: true},
            "data": {presence:true}
        };
        let errors = validate(data, constraints);

        if (errors !== undefined) {
            wsUser.send(functions.errorResponse(errors));
            return;
        }

        wsUser.callEvent(data);
    });

    wsUser.on('close', function(reason) {
        consoleMsg.log('User ' + ((wsUser.uuid) ? wsUser.uuid : 'anon') + ' has disconnected with reasonCode ' + reason);

        wsUser.rooms.forEach(function(room) {
            rooms.removeElem(room, wsUser);
        });
        clearTimeout(pinger);
        wsUser.destroy();
        sockets.delete(wsUser);
        wsUser = null;
    });

    //pong from user
    wsUser.addEventListener("connection", async function(data, eventName) {
        let constraints = {
            "token": {presence: true}
        };
        let errors = validate(data, constraints);
        if (errors !== undefined) {
            wsUser.send(functions.errorResponse(errors));
            return;
        }

    });

    wsUser.addEventListener("getMyData", function(data, eventName) {
        wsUser.send(functions.response(eventName, wsUser.getData()));
    });

    wsUser.addEventListener("location", function(data, eventName) {
        let constraints = {
            "lat": {presence: true},
            'long': {presence: true}
        };
        let errors = validate(data, constraints);
        if (errors !== undefined) {
            wsUser.send(functions.errorResponse(errors));
            return;
        }

        wsUser.location = data;
    });

    //wsUser.addEventListener("");
    wsUser.interval('sendOrders', function() {
        let response = functions.response('orders', activeOrders);
        wsUser.send(response);
    }, 5000);
});

setInterval(function() {
    //consoleMsg.log("realUsers: " + JSON.stringify(functions.pluckAssoc(functions.pluckAssoc(realUsers, 'user'), 'id')));
    consoleMsg.log("sockets: " + JSON.stringify(functions.pluck(sockets, 'uuid')));
    consoleMsg.log("rooms: " + Array.from(rooms.getWsRooms().keys()));
}, 10000);

wss.on('close', function() {
    consoleMsg.log("Server disconnected: " + wss.clients.size);
});

