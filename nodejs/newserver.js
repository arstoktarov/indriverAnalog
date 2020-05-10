const db = require('./Modules/db');
const validate = require('validate.js');
const consoleMsg = require('./Modules/consoleMsg');
const wsUserModule = require('./Modules/WSUser');
const functions = require('./Modules/additionalFunctions');
const { app, server } = require('./Loaders/Express');
const { Websocket, wss, rooms } = require('./Loaders/Websocket');
const redis = require('./Loaders/Redis');
const uuid = require('uuid-random/index');
const os = require('os');

let sockets = new Set();
let realUsers = new Map();
let activeOrders = new Map();
let orders = new Map();


const models = require('./Models/Models');



wss.on('connection',  function connection(ws) {
    let socket = new wsUserModule.User(ws);
    sockets.add(socket);
    consoleMsg.info('New connection catched. Current clients count: ' + wss.clients.size);

    // socket.ws.ping();
    // socket.isOnline = true;
     let pinger = null;
    //
    // socket.on('pong', function() {
    //     clearTimeout(pinger);
    //     socket.isOnline = true;
    //     pinger = setTimeout(function() {
    //         if (socket.isOnline === false) {
    //             socket.ws.close();
    //         }
    //         else {
    //             socket.isOnline = false;
    //             socket.ws.ping();
    //         }
    //     }, 30000);
    // });

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

        //let user = await models.User.query().where('token', data['token']).first();
        let user = await models.User.getUserByToken(data['token'], 'technics');
        if (user) {
            socket.user = user;
            socket.send(functions.response(eventName, user));
        }
        else {
            socket.send(functions.errorResponse({message: "User not found"}));
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

    socket.addEventListener("makeOrder", async function(data, eventName) {
        let constraints = {
            'city_id': {presence:true},
            'technic_id': {presence:true},
            'address.title': {presence:true},
            'address.lat': {presence:true},
            'address.long': {presence:true},
            'price': {presence:true},
            'description': {presence:true},
        };
        let errors = validate(data, constraints);
        if (errors !== undefined) {
            socket.send(functions.errorResponse(errors));
            return;
        }

        if (!socket.user) {
            socket.send(functions.errorResponse({message: 'you have no permissions'}));
            return;
        }

        socket.order = {
            executor: null,
            responses: new Set(),
            data: {
                uuid: uuid(),
                city_id: data['city_id'],
                technic_id: data['technic_id'],
                address: data['address']['title'],
                lat: data['address']['lat'],
                long: data['address']['long'],
                price: data['price'],
                description: data['description'],
                created_at: Date.now(),
                updated_at: Date.now(),
                user: socket.id,
            }
        };

        orders.set(socket.uuid, socket.order);

        socket.send(functions.response('makeOrder', socket.order));
    });

    socket.addEventListener("myOrder", async function(data, eventName) {
        socket.send(functions.response("myOrder", orders.get(socket.uuid)));
    });

    socket.addEventListener("respondOrder", async function(data, eventName) {
        let constraints = {
            'order_uuid': {presence:true},
        };
        let errors = validate(data, constraints);
        if (errors !== undefined) {
            socket.send(functions.errorResponse(errors));
            return;
        }

        let order = Array.from(orders.values()).find((order) => {
            return order.data.uuid === data['order_uuid']
        });
        if (!order) return;
        order.responses.add(socket.user);

        let orderOwner = sockets[order.user];
        if (orderOwner) {
            orderOwner.send(functions.response('newResponse', socket.user));
        }
    });

    socket.addEventListener("closeOrder", async function(data, eventName) {
        let constraints = {
            'order': {presence:true}
        };
        let errors = validate(data, constraints);
        if (errors !== undefined) {
            socket.send(functions.errorResponse(errors));
            return;
        }
    });

    socket.addEventListener("declineOrder", async function(data, eventName) {});

    socket.interval("sendOrders", function() {
        if (socket.user && socket.user.technics) {
            let personalOrders = Array.from(orders.values())
                .filter(function (order) {
                    return socket.user.technics.find(function (technic) {
                        return technic.id == order.data.technic_id
                    });
                })
                .map(order => order.data);
            socket.send(functions.response('sendOrders', personalOrders));
        }
    }, 10000);


});


setInterval(function() {
    consoleMsg.log("sockets: " + JSON.stringify(functions.pluck(sockets, 'uuid')));
    consoleMsg.log("rooms: " + Array.from(rooms.getWsRooms().keys()));
    consoleMsg.log("orders: " + Array.from(orders.keys()));
}, 10000);

wss.on('close', function() {
    consoleMsg.log("Server disconnected: " + wss.clients.size);
});

