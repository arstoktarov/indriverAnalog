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
const PushSender = require('./PushSender');

let sockets = new Set();
let orders = new Set();
let search_orders = new Set();
let users = {};


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
    //let myOrder = null;

    let ordersInterval = function() {
        if (socket.user) {
            if (socket.user.technics && socket.user.type === 2) {

                let personalOrders = Array.from(search_orders)
                    .filter(function (order) {
                        return order.data.status == models.Order.NOT_STARTED
                        //&& order.city_id == socket.user.city_id
                        // && socket.user.technics.find(function (technic) {
                        //     return technic.id == order.data.technic_id;
                        // });
                    })
                    .map(order => order.getData());

                socket.send(functions.response('orders', personalOrders));
            }
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

        sockets.delete(socket);
        socket.clearInterval("orders");
        clearTimeout(pinger);
        try {
            removeExecResponses(socket);
        }catch (e) {
            consoleMsg.log(`Cannot removeExecResponses ${e}`);
        }
        if (socket.order && socket.order.data.status === models.Order.NOT_STARTED) {
            orders.delete(socket.order);
            search_orders.delete(socket.order);
            socket.order.responses.forEach(function(response) {
                if (response.socket) response.socket.send(functions.response("userDeclined", socket.order.getData()));
            });
        }
        socket.destroy();
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
            let order = functions.setFind(orders, function (order) {
                return socket.user.type === wsUserModule.TYPE_USER
                    ? order.data.user_id === socket.user.id
                    : order.data.executor_id === socket.user.id;
            });
            socket.setOrder(order);
            socket.send(functions.response(eventName, user));
            if (await socket.hasProcessingOrder()) {
                await socket.loadOrder(orders);
                socket.send(functions.response("orderStarted", socket.order.getData()));
                consoleMsg.log('User/Executor has already processing order', socket.order.getData());
            }

            if (socket.isExecutor()) {
                ordersInterval();
                socket.interval("orders", ordersInterval, 10000);
            }
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

        if (!socket.hasUser()) {
            socket.send(functions.errorResponse({message: 'You have no permissions'}));
            return;
        }

        let technic = await models.Technic.getTechnicById(data['technic_id']);
        let min_accepted_price = technic.type.min_order_price;
        if (data['price'] < min_accepted_price) {
            socket.send(functions.errorResponse({message: `Price of order should be more than or equal to ${min_accepted_price}`}));
            return;
        }

        let orderData = {
            uuid: uuid(),
            user_id: socket.user.id,
            status: models.Order.NOT_STARTED,
            executor_id: null,
            city_id: data['city_id'],
            technic_id: data['technic_id'],
            address: data['address'],
            lat: data['lat'].toString(),
            long: data['long'].toString(),
            price: data['price'].toString(),
            description: data['description'],
            technic: technic,
            executor_technic: null
        };

        let order = new Order(orderData, socket);
        order.setUserData(socket.user);
        socket.order = order;
        orders.add(order);
        search_orders.add(order);


        socket.send(functions.response('makeOrder', order.getData()));
    });

    socket.addEventListener("myOrder", async function(data, eventName) {
        if (!socket.hasUser()) {
            socket.send(functions.errorResponse({message: 'you have no permissions'}));
            return;
        }
        let order = socket.order;
        if (order) {
             order = await socket.loadOrder(orders);
            socket.send(functions.response("myOrder", order.getData()));
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

        let order = functions.setFind(orders, (order) => {
            return order.data.uuid === data['order_uuid']
        });
        let response = functions.setFind(order.responses, (response) => {
            return response.socket === socket;
        });
        if (!socket.user || !order) {
            socket.send(functions.errorResponse({message: 'you have no permissions'}));
            return;
        }
        if (await socket.hasProcessingOrder()) {
            socket.send(functions.errorResponse({message: "you already have order"}));
            return;
        }


        if (!response) {
            consoleMsg.log("there is no response");
            response = new Response(socket, data['price'], order.data.uuid, order);
            order.addResponse(response);
            socket.responses.add(response);
        }
        response.price = data['price'];

        let orderOwner = order.user_socket;
        consoleMsg.log(order.user_socket.uuid);
        if (orderOwner) {
            //PushSender.se
            consoleMsg.log(`Sending message to user ${orderOwner.user.name}`);
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

        if (!socket.hasUser()) {
            socket.send(functions.errorResponse({message: 'you have no permissions'}));
            return;
        }
        if (!socket.hasOrder()) {
            socket.send(functions.errorResponse({message: 'you have no permissions'}));
            return;
        }

        let executor_response = functions.setFind(socket.order.responses, function(elem) {
            return data['executor_uuid'].toString() === elem.socket.uuid.toString();
        });

        if (!executor_response) {
            socket.send(functions.errorResponse({"message": "Отклик не найден"}));
            return;
        }

        let executor_socket = executor_response.socket;

        if (!executor_response.socket) {
            socket.send(functions.errorResponse({"message": "Исполнитель отключен"}));
            return;
        }

        if (await executor_response.socket.hasProcessingOrder()) {
            socket.send(functions.errorResponse({"message": "Исполнитель принял другой заказ"}));
            return;
        }

        let order = socket.order;

        //search_orders.delete(socket.order);

        //socket.send(functions.response('chooseExecutor', socket.order.getData()));

        order.data.price = executor_response.price;
        order.executor_socket = executor_socket;
        order.setExecutorData(order.executor_socket.user);
        order.responses.clear();

        order.executor_socket.clearInterval("orders");

        //socket.order = order;

        search_orders.delete(socket.order);
        let comission = 5;
        let balance = socket.user.balance;
        let new_balance = balance - (socket.order.data.price * (comission / 100));
        consoleMsg.log(`${socket.user.name}'s new balance ${new_balance}`);

        let db_order = await models.Order.query().insert({
            uuid: order.data.uuid,
            user_id: order.user_socket.user.id,
            executor_id: order.executor_socket.user.id,
            status: models.Order.IN_PROCESS,
            city_id: order.data.city_id,
            technic_id: order.data.technic_id,
            address: order.data.address,
            lat: order.data.lat,
            long: order.data.long,
            price: order.data.price,
            description: order.data.description
        });

        if (executor_socket.user) {
            let executor = await models.User.query().patch({balance: new_balance}).findById(socket.user.id);
        }

        await order.reloadData();


        if (order.executor_socket) order.executor_socket.send(functions.response("orderStarted", order.getData()));
        if (order.user_socket) order.user_socket.send(functions.response("orderStarted", order.getData()));
        //executor_response.socket.send(functions.response('userResponded', socket.order.getData()));
    });

    socket.addEventListener("declineExecutor", async function(data, eventName) {
        let constraints = {
            'executor_uuid': {presence:true},
        };
        let errors = validate(data, constraints);
        if (errors !== undefined) {
            socket.send(functions.errorResponse(errors));
            return;
        }

        if (!socket.hasUser()) {
            socket.send(functions.errorResponse({message: 'you have no permissions'}));
            return;
        }
        if (!socket.hasOrder()) {
            socket.send(functions.errorResponse({message: 'you have no permissions'}));
            return;
        }

        let executor_response = functions.setFind(socket.order.responses, function(elem) {
            return data['executor_uuid'].toString() === elem.socket.uuid.toString();
        });
        search_orders.add(socket.order);

        if (socket.order) {
            socket.order.deleteResponse(executor_response);
        }
        socket.send(functions.response("responses", socket.order.getResponses()));

        executor_response.socket.send(functions.response("userDeclined", socket.order.getData()));
    });

    socket.addEventListener("startOrder", async function(data, eventName) {
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
        if (!order) return;

        order.executor_socket = socket;
        order.responses.clear();

        socket.order = order;
        socket.order.setExecutorData(order.executor_socket.user);
        socket.clearInterval("orders");
        search_orders.delete(socket.order);


        let comission = 5; //TODO set comission value from database
        let balance = socket.user.balance;
        let new_balance = balance - (socket.order.data.price * (comission / 100));
        consoleMsg.log(`${socket.user.name}'s new balance ${new_balance}`);



        let db_order = await models.Order.query().insert({
            uuid: order.data.uuid,
            user_id: order.user_socket.user.id,
            executor_id: order.executor_socket.user.id,
            status: models.Order.IN_PROCESS,
            city_id: order.data.city_id,
            technic_id: order.data.technic_id,
            address: order.data.address,
            lat: order.data.lat,
            long: order.data.long,
            price: order.data.price,
            description: order.data.description
        });

        await order.reloadData();

        if (socket.user) {
            let executor = await models.User.query().patch({balance: new_balance}).findById(socket.user.id);
        }

        order.executor_socket.send(functions.response("orderStarted", order.getData()));
        order.user_socket.send(functions.response("orderStarted", order.getData()));

    });

    async function acceptOrder(data, eventName) {
        let constraints = {
            'order_uuid': {presence:true},
        };
        let errors = validate(data, constraints);
        if (errors !== undefined) {
            consoleMsg.log('Hello');
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
        let response = functions.setFind(order.responses, (response) => {
            return response.socket === socket;
        });
        if (!order || !response) {
            return;
        }

        order.data.price = response.price;
        order.executor_socket = socket;
        order.setExecutorData(order.executor_socket.user);
        order.responses.clear();

        socket.order = order;
        search_orders.delete(socket.order);
        let comission = 5;
        let balance = socket.user.balance;
        let new_balance = balance - (socket.order.data.price * (comission / 100));
        consoleMsg.log(`${socket.user.name}'s new balance ${new_balance}`);

        let db_order = await models.Order.query().insert({
            uuid: order.data.uuid,
            user_id: order.user_socket.user.id,
            executor_id: order.executor_socket.user.id,
            status: models.Order.IN_PROCESS,
            city_id: order.data.city_id,
            technic_id: order.data.technic_id,
            address: order.data.address,
            lat: order.data.lat,
            long: order.data.long,
            price: order.data.price,
            description: order.data.description
        });

        await order.reloadData();

        if (socket.user) {
            let executor = await models.User.query().patch({balance: new_balance}).findById(socket.user.id);
        }

        order.executor_socket.send(functions.response("orderStarted", order.getData()));
        order.user_socket.send(functions.response("orderStarted", order.getData()));

    }

    socket.addEventListener("acceptOrder", async (data, eventName) => {
        await acceptOrder(data, eventName);
    });

    socket.addEventListener("orderStarted", function(data, eventName) {

    });

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

        orders.delete(socket.order);
        search_orders.delete(socket.order);
        socket.order.responses.forEach(function(response) {
            if (response.socket) response.socket.send(functions.response("userDeclined", socket.order.getData()));
        });
        socket.order.destroy();
        socket.order = null;
    });

    socket.addEventListener("orderDone", async function(data, eventName) {
        let constraints = {
            'order_uuid': {presence:true},
        };
        let errors = validate(data, constraints);
        if (errors !== undefined) {
            socket.send(functions.errorResponse(errors));
            return;
        }

        let order = socket.order;

        if (!order) return;

        let db_order = await models.Order.query().patch({status: models.Order.DONE}).where('uuid', order.data.uuid);

        if (order.user_socket) order.user_socket.send(functions.response(eventName, data));
        if (order.executor_socket) order.executor_socket.send(functions.response(eventName, data));


        if (order.executor_socket) {
            order.executor_socket.interval("orders", ordersInterval, 10000);
        }
        orders.delete(order);
        search_orders.delete(order);

        if (order.executor_socket) order.executor_socket.order = null;
        if (order.user_socket) order.user_socket.order = null;
        socket.order = null;
        order.destroy();
    });

    socket.addEventListener("declineOrder", async function(data, eventName) {});

    socket.addEventListener("setOnline", async function() {

    });


    socket.interval("resetOrders", function() {

    });

    function startOrder(order, socket) {
        socket.clearInterval("orders");
        socket.send(functions.response("orderStarted", order.getData()));
        search_orders.delete(order);
    }

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
    consoleMsg.info("sockets: " + JSON.stringify(functions.pluck(sockets, 'uuid')));
    consoleMsg.info("rooms: " + Array.from(rooms.getWsRooms().keys()));
    consoleMsg.info("orders: " + JSON.stringify(Array.from(orders).map(order => order.data.uuid)));
    consoleMsg.info("search_orders: " + JSON.stringify(Array.from(search_orders).map(order => order.data.uuid)));
}, 10000);

wss.on('close', function() {
    consoleMsg.log("Server disconnected: " + wss.clients.size);
});
