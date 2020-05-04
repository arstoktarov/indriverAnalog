const db = require('./Modules/db');
const validate = require('validate.js');
const consoleMsg = require('./Modules/consoleMsg');
const wsUserModule = require('./Modules/WSUser');
const functions = require('./Modules/additionalFunctions');
const { app, server } = require('./Loaders/Express');
const { Websocket, wss, rooms } = require('./Loaders/Websocket');
const os = require('os');

let sockets = new Set();
let realUsers = new Map();
let activeOrders = new Map();


const models = require('./Models/Models');



wss.on('connection',  function connection(ws) {
    let socket = new wsUserModule.User(ws);
    sockets.add(socket);
    consoleMsg.info('New connection catched. Current clients count: ' + wss.clients.size);

    socket.ws.ping();
    socket.isOnline = true;
    let pinger = null;

    socket.on('pong', function() {
        clearTimeout(pinger);
        socket.isOnline = true;
        pinger = setTimeout(function() {
            if (socket.isOnline === false) {
                socket.ws.close();
            }
            else {
                socket.isOnline = false;
                socket.ws.ping();
            }
        }, 1000);
    });

    socket.on('message', function (json) {
        consoleMsg.log("MESSAGE: " + json);
        let data = wsUserModule.User.parseJson(json);
        let constraints = {
            "event": {presence: true},
            "data": {presence:true}
        };
        let errors = validate(data, constraints);

        if (errors !== undefined) {
            socket.send(functions.errorResponse(errors));
            return;
        }

        socket.callEvent(data);
    });

    socket.on('close', function(reason) {
        consoleMsg.log('User ' + ((socket.uuid) ? socket.uuid : 'anon') + ' has disconnected with reasonCode ' + reason);

        socket.rooms.forEach(function(room) {
            rooms.removeElem(room, socket);
        });
        clearTimeout(pinger);
        socket.destroy();
        sockets.delete(socket);
        socket = null;
    });

    //pong from user
    socket.addEventListener("connection", async function(data, eventName) {
        let constraints = {
            "token": {presence: true}
        };
        let errors = validate(data, constraints);
        if (errors !== undefined) {
            socket.send(functions.errorResponse(errors));
            return;
        }


    });

    socket.addEventListener("getMyData", function(data, eventName) {
        socket.send(functions.response(eventName, socket.getData()));
    });

    socket.addEventListener("location", function(data, eventName) {
        let constraints = {
            "lat": {presence: true},
            'long': {presence: true}
        };
        let errors = validate(data, constraints);
        if (errors !== undefined) {
            socket.send(functions.errorResponse(errors));
            return;
        }

        socket.location = data;
    });

    socket.addEventListener("makeOrder", function() {
        let constraints = {
            'city_id': {presence:true},
            'address.lat': {presence:true},
            'address.long': {presence:true},
            'technic_id': {presence:true},
            'price': {presence:true},
            'description': {presence:true},
        };
        let errors = validate(data, constraints);
        if (errors !== undefined) {
            socket.send(functions.errorResponse(errors));
            return;
        }

        let order = models.Order.query().insert({

        });
    });

    socket.interval('sendOrders', function() {
        let response = functions.response('orders', activeOrders);
        socket.send(response);
    }, 5000);
});


setInterval(function() {
    //consoleMsg.log("realUsers: " + JSON.stringify(functions.pluckAssoc(functions.pluckAssoc(realUsers, 'user'), 'id')));
    consoleMsg.log("sockets: " + JSON.stringify(functions.pluck(sockets, 'uuid')));
    //consoleMsg.log("rooms: " + Array.from(rooms.getWsRooms().keys()));
    //consoleMsg.info(`Total memory: ${os.totalmem()}`);
    //consoleMsg.info(`Free memory: ${os.freemem()}`);
}, 10000);

wss.on('close', function() {
    consoleMsg.log("Server disconnected: " + wss.clients.size);
});


console.log(wss);

