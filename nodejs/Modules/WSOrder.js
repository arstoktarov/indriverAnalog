//консоль с цветами, вместо нудного console.log() ;)
const consoleMsg = require('./consoleMsg');


//библотека для создания uuid
const uuid = require('uuid-random/index');

//дополнительные функции в другом файле
const functions = require('./additionalFunctions');

class Order {
    uuid = null;
    socket_id = null;
    user = null;
    executor = null;
    responses = new Set();
    data = null;

    constructor(order, socket) {
        this.uuid = uuid();
        this.user = socket;
        this.data = order;
    }

    addResponse(response) {
        if (response.socket !== this.user) {
            this.responses.add(response);
            return true;
        }
        return false;
    }

    deleteResponse(response) {
        this.responses.delete(response);
    }

    setExecutor(socket) {
        this.executor = socket;
    }

    getExecutor() {
        return this.executor ? this.executor.getExecutor() : null;
    }

    getUser() {
        return this.user ? this.user.getUser() : null;
    }

    getData() {
        return {
            //uuid: this.uuid,
            socket_id: this.user.uuid ? this.user.uuid : null,
            data: this.data,
            executor: this.getExecutor(),
            user: this.getUser()
        };
    }

    getResponses() {
        //return Array.from(this.responses).map(response => response.getExecutor());
        return Array.from(this.responses).map(response => response.getData());
    }

    destroy() {
        this.responses.clear();
    }
}

class Response {
    socket = null;
    price = null;
    order_uuid = null;

    constructor(socket, price, order_uuid) {
        this.socket = socket;
        this.price = price;
        this.order_uuid = order_uuid;
    }

    getData() {
        if (!this.socket) return null;
        return {
            socket_id: this.socket.uuid,
            price: this.price,
            user: this.socket.getExecutor(),
        }
    }

}

module.exports = {
    Order: Order,
    Response: Response,
};