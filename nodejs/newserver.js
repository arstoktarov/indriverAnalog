
//knex database connection
const db = require('./db');

//validator https://validatejs.org/
const validate = require('validate.js');

//console with colors
const consoleMsg = require('./consoleMsg');
const wsUserModule = require('./WSUser');

//just additional functions in another file.
const functions = require('./additionalFunctions');

//express app
const app = require('express')();
let server = require('http').Server(app);
const port = 3000;

//websocket
const WebSocket = require('ws');
const wss = new WebSocket.Server({
    port: 8080,
});

//custom rooms realization
const roomModule = require('./rooms');


//wss sockets
let sockets = new Set();

//real users connected by connection event by sending id
let realUsers = new Map();

//real couriers connected by connection event by sending id
let realCouriers = new Map();

//http server
server.listen(port, 'localhost');
consoleMsg.info('Server started at ' + port);

//rooms module
let rooms = new roomModule.roomsModel(wss);

wss.on('connection',  function connection(ws) {
    //создает объект класса wsUser.
    let wsUser = new wsUserModule.User(ws);
    sockets.add(wsUser);
    consoleMsg.info('New connection catched. Current clients count: ' + wss.clients.size);

    //wss pings every connection to check whether user online or not
    //if user connected and online it sends pong to server
    wsUser.ws.ping();
    let pinger = null;

    //Каждое сообщения проверяется через функцию parseJson. Если проверка пройдена вызывает event который должен присутствовать в json.
    wsUser.on('message', function (json) {
        consoleMsg.log("MESSAGE: " + json);
        let data = wsUserModule.User.parseJson(json);
        let constraints = {
            "event": {presence: true},
        };
        let errors = validate(data, constraints);

        if (errors !== undefined) {
            wsUser.send(functions.errorResultJson(404, errors));
            return;
        }

        wsUser.callEvent(data);
    });

    //Если подключение websocket-a прервется или он долго не будет отвечать
    //то удалятся все данные wsUser с интервалами и таймаутами
    wsUser.on('close', function(reason) {
        consoleMsg.log('User ' + ((wsUser.uuid) ? wsUser.uuid : 'anon') + ' has disconnected with reasonCode ' + reason);
        if (wsUser.user) {
            if (wsUser.type === wsUserModule.TYPE_USER) realUsers.delete(wsUser.user.id);
            else if (wsUser.type === wsUserModule.TYPE_COURIER) realCouriers.delete(wsUser.user.id);
        }
        wsUser.rooms.forEach(function(room) {
            rooms.removeElem(room, wsUser);
        });
        sockets.delete(wsUser);
        wsUser.destroy();
        wsUser = null;
    });

    //pong from user
    wsUser.on('pong', function() {
        clearTimeout(pinger);
        //consoleMsg.log("Received pong from " + wsUser.uuid);
        wsUser.isOnline = true;
        pinger = setTimeout(function() {
            if (wsUser.isOnline === false) {
                wsUser.ws.close();
            }
            else {
                wsUser.isOnline = false;
                //consoleMsg.log('wsUser now offlined');
                ws.ping();
            }
            //consoleMsg.log('ping timeout stopped: ' + wsUser.uuid);
        }, 30000);
    });

    wsUser.addEventListener("connection", async function(data, eventName) {
        let constraints = {
            "event": {presence: true},
            "type": {presence: true},
            "token": {presence: true}
        };
        let errors = validate(data, constraints);
        if (errors !== undefined) {
            wsUser.send(functions.errorResultJson(404, errors));
            return;
        }

        if (data['type'] === wsUserModule.TYPE_USER) {
            //setUser возвращает id прикрепленного пользователя
            let result = await wsUser.setUser(data['token']);
            if (!result) wsUser.send(functions.errorResultJson(404, 'User not found'));
            realUsers.set(result, wsUser);
            //wsUser.attachUser(data, realUsers);
        }
        else {
            //setCourier возвращает id прикрепленного курьера
            let result = await wsUser.setCourier(data['token']);
            if (!result) wsUser.send(functions.errorResultJson(404, 'Courier not found'));
            realCouriers.set(result, wsUser);
            //wsUser.attachCourier(data, realCouriers);
        }

    });

    wsUser.addEventListener("sendLocation", function(data, eventName) {
        //Правила для ошибок
        let constraints = {
            "event": {presence: true},
            "location.lat": {presence: true},
            "location.long": {presence: true},
            //"location.orderId": {presence: true}
        };
        let errors = validate(data, constraints);
        //Отправляет ошибки в запросе обратно пользователю
        if (errors !== undefined) {
            wsUser.send(functions.errorResultJson(404, errors));
            return;
        }


        data = data['location'];
        let location = {
            lat: data['lat'],
            long: data['long'],
        };
        wsUser.location = location;

        if (wsUser.user && wsUser.type === wsUserModule.TYPE_COURIER) {
            consoleMsg.log('broadcasting to room: ' + wsUser.user.id);
            rooms.broadcast(wsUser.user.id, functions.getJsonResponse(eventName, location));
        }
        //TODO realization of couriers placement
    });

    wsUser.addEventListener("subscribeOrder", async function(data, eventName) {
        let constraints = {
            "event": {presence: true},
            "orderId": {presence: true},
        };
        let errors = validate(data, constraints);
        if (errors !== undefined) {
            wsUser.send(functions.errorResultJson(404, errors));
            return;
        }

        let order = await db.getOrderById(data['orderId']);
        consoleMsg.log("Order: " + JSON.stringify(order));
        if (!order) {
            wsUser.send(functions.errorResultJson('404', 'Order not found'));
            return;
        }
        if (!order['courier_id']) {
            wsUser.send(functions.errorResultJson('404', 'Courier not found'));
            return;
        }
        if (!rooms.getWsRoom(order['courier_id'])) rooms.newRoom(order['courier_id']);
        rooms.attach(order['courier_id'], wsUser);
        wsUser.rooms.add(order['courier_id']);
        consoleMsg.log("User joined new room: " +
            order['courier_id'] + ', roomSize: ' +
            rooms.getWsRoom(order['courier_id']).length);
    });

    wsUser.addEventListener("unsubscribeOrder", async function(data, eventName) {
        let constraints = {
            "event": {presence: true},
            "orderId": {presence: true},
        };
        let errors = validate(data, constraints);
        if (errors !== undefined) {
            wsUser.send(functions.errorResultJson(404, errors));
            return;
        }

        let order = await db.getOrderById(data['orderId']);
        if (!order) return;

        if (order['courier_id']) {
            rooms.removeElem(order['courier_id'], wsUser);
            wsUser.rooms.delete(order['courier_id']);
            consoleMsg.log(rooms.getWsRoom(order['courier_id']));
        }

    });

    wsUser.addEventListener("getMyData", function(data, eventName) {
        wsUser.send(functions.getJsonResponse(eventName, wsUser.getData()));
    });
});

setInterval(function() {
    //consoleMsg.log("realUsers: " + JSON.stringify(functions.pluckAssoc(functions.pluckAssoc(realUsers, 'user'), 'id')));
    consoleMsg.log("realCouriers: " + JSON.stringify(Array.from(realCouriers.keys())));
    consoleMsg.log("sockets: " + JSON.stringify(functions.pluck(sockets, 'uuid')));
    consoleMsg.log("rooms: " + Array.from(rooms.getWsRooms().keys()));
}, 10000);

wss.on('close', function() {
    consoleMsg.log("Server disconnected: " + wss.clients.size);
});




app.get('/couriers', function(req, res) {
    let couriers = [];
    let array = Array.from(realCouriers.values());
    array.forEach(function(courier) {
        if (courier.user) {
            couriers.push({
                id: courier.user.id,
                location: courier.location,
            });
        }
    });
    res.send(JSON.stringify(couriers));
});

app.get('/applyCourier', function(req, res) {
    consoleMsg.log(JSON.stringify(req.parameters));
});

