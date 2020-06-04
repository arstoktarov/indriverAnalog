//консоль с цветами, вместо нудного console.log() ;)
const consoleMsg = require('./consoleMsg');


//библотека для создания uuid
const uuid = require('uuid-random/index');

//дополнительные функции в другом файле
const functions = require('./additionalFunctions');

const models = require('../Models/Models');

class Order {
    uuid = null;
    socket_id = null;
    user_socket = null;
    executor_socket = null;
    responses = new Set();
    data = null;

    constructor(order, socket) {
        this.uuid = uuid();
        this.user_socket = socket;
        this.setData(order);
    }

    addResponse(response) {
        if (response.socket !== this.user_socket) {
            this.responses.add(response);
            return true;
        }
        return false;
    }

    deleteResponse(response) {
        this.responses.delete(response);
    }

    setExecutor(socket) {
        this.executor_socket = socket;
    }

    getExecutor() {
        return this.executor_socket ? this.executor_socket.getExecutor() : null;
    }

    getUser() {
        return this.user_socket ? this.user_socket.getUser() : null;
    }

    getData() {
        return {
            //uuid: this.uuid,
            socket_id: this.user_socket.uuid ? this.user_socket.uuid : null,
            data: this.data,
            executor: this.getExecutor(),
            user: this.getUser()
        };
    }

    setData(order) {
        this.data = {
            uuid: order.uuid,
            user_id: order.user_id,
            status: order.status,
            executor_id: order.executor_id,
            city_id: order.city_id,
            technic_id: order.technic_id,
            address: order.address,
            lat: order.lat,
            long: order.long,
            price: order.price,
            description: order.description,
            technic: {
                id: order.technic.id,
                type_id: order.technic.type_id,
                charac_value: order.technic.charac_value,
                type: order.technic.type ? order.technic.type    : null,
            },
        };
    }

    async reloadData() {
         let order = await models.Order.query()
            .withGraphFetched('technic')
            .modifyGraph('technic', builder => {
                builder.select(models.Technic.columns)
                    .withGraphFetched('type')
                    .modifyGraph('type', builder => {
                        builder.select(models.Technic_Type.select_columns);
                    });
            })
            .where('uuid', this.data.uuid).first();
         if (order) this.setData(order);
    }


    getResponses() {
        //return Array.from(this.responses).map(response => response.getExecutor());
        return Array.from(this.responses).map(response => response.getData());
    }

    destroy() {
        this.responses.clear();
        delete this;
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