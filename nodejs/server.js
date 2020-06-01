const db = require('./Modules/db');
const validate = require('validate.js');
const consoleMsg = require('./Modules/consoleMsg');
const wsUserModule = require('./Modules/WSUser');
const { Order, Response } = require('./Modules/WSOrder');
const functions = require('./Modules/additionalFunctions');
const emitter = require('./Modules/Events');
const { app, server } = require('./Loaders/Express');
const { Websocket, wss, rooms } = require('./Loaders/Websocket');
const redis = require('./Loaders/Redis');
const uuid = require('uuid-random/index');

let sockets = new Set();
let orders = new Set();


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
    let myOrder = null;

    let ordersInterval = function() {
        if (socket.user && socket.user.technics && socket.user.type === 2) {

            let personalOrders = Array.from(orders)
                .filter(function (order) {
                    return order.executor == null && socket.user.technics.find(function (technic) {
                        return technic.id.toString() === order.data.technic_id.toString()
                    });
                })
                .map(order => order.getData());

            socket.send(functions.response('orders', personalOrders));
        }
    };

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
        removeExecResponses(socket);
        orders.delete(socket.order);
        sockets.delete(socket);
        socket.destroy();
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
        let user = await models.User.getUserByToken(data['token']);
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
            'address': {presence:true},
            'lat': {presence:true},
            'long': {presence:true},
            'price': {presence:true},
            'description': {presence:true},
        };
        let errors = validate(data, constraints);
        if (errors !== undefined) {
            socket.send(functions.errorResponse(errors));
            return;
        }

        if (!socket.user) {
            socket.send(functions.errorResponse({message: 'You have no permissions'}));
            return;
        }

        let orderData = {
            uuid: uuid(),
            city_id: data['city_id'],
            technic_id: data['technic_id'],
            address: data['address'],
            lat: data['lat'],
            long: data['long'],
            price: data['price'],
            description: data['description'],
            technic: await models.Technic.getTechnicById(data['technic_id'])
        };

        let order = new Order(orderData, socket);
        myOrder = order;
        socket.order = order;
        orders.add(order);


        socket.send(functions.response('makeOrder', order.getData()));
    });

    socket.addEventListener("myOrder", async function(data, eventName) {
        if (!socket.user) {
            socket.send(functions.errorResponse({message: 'you have no permissions'}));
            return;
        }
    });

    socket.addEventListener("respondOrder", async function(data, eventName) {
        let constraints = {
            'order_uuid': {presence:true},
            'price': {presence:true},
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

        let order = functions.setFind(orders, (order) => {
            return order.data.uuid === data['order_uuid']
        });

        consoleMsg.log(data['price']);
        let executor_response = new Response(socket, data['price'], order.data.uuid);

        //let added = order.addResponse(socket);

        let added = order.addResponse(executor_response);

        socket.responses.add(executor_response);

        let orderOwner = order.user;
        consoleMsg.log(order.user.uuid);
        consoleMsg.log(added);
        if (orderOwner && added) {
            orderOwner.send(functions.response('newResponse', order.getResponses()));
        }
    });

    socket.addEventListener("chooseExecutor", async function(data, eventName) {
        let constraints = {
            'executor_uuid': {presence:true},
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
        if (!socket.order) {
            socket.send(functions.errorResponse({message: 'you have no permissions'}));
            return;
        }

        let executor_response = functions.setFind(socket.order.responses, function(elem) {
            return data['executor_uuid'].toString() === elem.socket.uuid.toString();
        });

        let exec_order = models.Order.query()
            .where('executor_id', executor_response.socket.user.id)
            .first();
        if (exec_order) {
            socket.send(functions.errorResponse({"message": "Исполнитель принял другой заказ"}));
            return;
        }

        socket.order.data.price = executor_response.price;
        socket.order.executor = executor_response.socket;
        socket.order.responses.clear();
        executor_response.socket.clearInterval("orders");
        let comission = 5;
        let balance = executor_response.socket.user.balance;
        let new_balance = balance - (socket.order.data.price * (comission / 100));
        consoleMsg.log(new_balance);
        if (executor_response.socket.user) {
            let executor = models.User.query().patch({balance: new_balance}).findById(executor_response.socket.user.id);
        }

        socket.send(functions.response('chooseExecutor', socket.order.getData()));
        socket.order.executor.send(functions.response('userResponded', socket.order.getData()));

    });

    socket.addEventListener("acceptOrder", async function(data, eventName) {
        let constraints = {
            'order_uuid': {presence:true},
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

        let order = functions.setFind(orders, (order) => {
            return order.data.uuid === data['order_uuid']
        });

        let db_order = models.Order.query().insert({
            uuid: order.data.uuid,
            executor_id: socket.id,
            status: 0,
            city_id: order.data.city_id,
            technic_id: order.data.technic_id,
            address: order.data.address,
            lat: order.data.lat,
            long: order.data.long,
            price: order.data.price,
            description: order.data.description
        });


    });

    socket.addEventListener("myOrder", async function(data, eventName) {
        let constraints = {
            'executor_uuid': {presence:true},
        };
        let errors = validate(data, constraints);
        if (errors !== undefined) {
            socket.send(functions.errorResponse(errors));
            return;
        }


    });

    // socket.addEventListener("chooseExecutor", async function(data, eventName) {
    //     let constraints = {
    //         'executor_uuid': {presence:true},
    //     };
    //     let errors = validate(data, constraints);
    //     if (errors !== undefined) {
    //         socket.send(functions.errorResponse(errors));
    //         return;
    //     }
    //
    //     if (!socket.user) {
    //         socket.send(functions.errorResponse({message: 'you have no permissions'}));
    //         return;
    //     }
    //     if (!socket.order) {
    //         socket.send(functions.errorResponse({message: 'you have no permissions'}));
    //         return;
    //     }
    //
    //     let executor_response = functions.setFind(socket.order.responses, function(elem) {
    //         return data['executor_uuid'].toString() === elem.socket.uuid.toString();
    //     });
    //
    //     socket.order.data.price = executor_response.price;
    //     socket.order.executor = executor_response.socket;
    //     socket.order.responses.clear();
    //     executor_response.socket.clearInterval("orders");
    //     let comission = 5;
    //     let balance = executor_response.socket.user.balance;
    //     let new_balance = balance - (socket.order.data.price * (comission / 100));
    //     consoleMsg.log(new_balance);
    //     if (executor_response.socket.user) {
    //         let executor = models.User.query().patch({balance: new_balance}).findById(executor_response.socket.user.id);
    //     }
    //
    //     socket.send(functions.response('chooseExecutor', socket.order.getData()));
    //     socket.order.executor.send(functions.response('userResponded', socket.order.getData()));
    //
    // });

    socket.addEventListener("closeOrder", async function(data, eventName) {
        let constraints = {
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


    });

    socket.addEventListener("orderDone", async function(data, eventName) {
        let constraints = {
        };
        let errors = validate(data, constraints);
        if (errors !== undefined) {
            socket.send(functions.errorResponse(errors));
            return;
        }

    });

    socket.addEventListener("declineOrder", async function(data, eventName) {});

    socket.addEventListener("setOnline", async function() {

    });


    socket.interval("orders", ordersInterval, 10000);


});

function removeExecResponses(socket) {
    socket.responses.forEach(function(executor_responses) {
        let order = functions.setFind(orders, function(order) {
            return order.data.uuid === executor_responses.order_uuid
        });
        if (order) {
            order.responses.delete(executor_responses)
        }
    });
}

setInterval(function() {
    consoleMsg.log("sockets: " + JSON.stringify(functions.pluck(sockets, 'uuid')));
    consoleMsg.log("rooms: " + Array.from(rooms.getWsRooms().keys()));
    //consoleMsg.log("orders: " + JSON.stringify(Array.from(orders).map(order => order.socket.uuid)));
}, 10000);

wss.on('close', function() {
    consoleMsg.log("Server disconnected: " + wss.clients.size);
});

