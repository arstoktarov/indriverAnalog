
//knex database connection
const db = require('./Modules/db');

//validator https://validatejs.org/
const validate = require('validate.js');

//console with colors
const consoleMsg = require('./Modules/consoleMsg');
const wsUserModule = require('./Modules/WSUser');

//just additional functions in another file.
const functions = require('./Modules/additionalFunctions');

//custom rooms realization
const roomModule = require('./Modules/rooms');

//express app
const port = 3000;
const app = require('express')();
let server = require('http').Server(app);
server.listen(port, 'localhost');
consoleMsg.info('Server started at ' + port);


//websocket
const WebSocket = require('ws');
const wss = new WebSocket.Server({
    port: 8080,
});
consoleMsg.info('WS Server started at ' + 8080);

//wss sockets
let sockets = new Set();

//real users connected by connection event by sending id
let realUsers = new Map();

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
    wsUser.isOnline = true;
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

    wsUser.on('orders', function() {

    });

    wsUser.interval(function() {
        wsUser.send(functions.getJsonResponse('orders', {smth: 'asdasd'}));
    }, 3000);


    //pong from user
    wsUser.on('pong', function() {
        clearTimeout(pinger);
        //consoleMsg.log("Received pong from " + wsUser.uuid);
        wsUser.isOnline = true;
        pinger = wsUser.timeout(function() {
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

    wsUser.addEventListener("connection", async function(data, eventName)   {
        let constraints = {
            "event": {presence: true},
            "token": {presence: true}
        };
        let errors = validate(data, constraints);
        if (errors !== undefined) {
            wsUser.send(functions.errorResultJson(404, errors));
            return;
        }

        let user = await db.getUserByToken(data['token']);

        if (!user) {
            wsUser.send(functions.errorResultJson(404, 'User not found'));
            return;
        }

        wsUser.user = user;
        realUsers.set(user.id, user);
        console.log(JSON.stringify(user));
    });

    wsUser.addEventListener("getMyData", function(data, eventName) {
        wsUser.send(functions.getJsonResponse(eventName, wsUser.getData()));
    });


});

setInterval(function() {
    //consoleMsg.log("realUsers: " + JSON.stringify(functions.pluckAssoc(functions.pluckAssoc(realUsers, 'user'), 'id')));
    //consoleMsg.log("realCouriers: " + JSON.stringify(Array.from(realCouriers.keys())));
    consoleMsg.log("sockets: " + JSON.stringify(functions.pluck(sockets, 'uuid')));
    consoleMsg.log("rooms: " + Array.from(rooms.getWsRooms().keys()));
}, 10000);

wss.on('close', function() {
    consoleMsg.log("Server disconnected: " + wss.clients.size);
});





app.get('/applyCourier', function(req, res) {
    consoleMsg.log(JSON.stringify(req.parameters));
});