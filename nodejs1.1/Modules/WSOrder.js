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
    user_data = null;
    executor_data = null;
    responses = new Set();
    technic = {};
    data = null;
    step = 0;

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

    setUserData(user) {
        if (user) {
            this.user_data = {};
            try {
                this.user_data.id = user.id;
                this.user_data.type = user.type;
                this.user_data.name = user.name;
                this.user_data.phone = user.phone;
                this.user_data.city_id = user.city_id;
                this.executor_data.device_token = user.device_token;
                this.executor_data.device_type = user.device_type;
            } catch (e) {
                consoleMsg.log(`Cannot set User data: ${e}`);
            }
        }
        else {
            consoleMsg.log(`Cannot set User data: user parameter is null`);
        }
    }

    setExecutorData(executor) {
        if (executor) {
            this.executor_data = {};
            try {
                this.executor_data.id = executor.id;
                this.executor_data.type = executor.type;
                this.executor_data.name = executor.name;
                this.executor_data.phone = executor.phone;
                this.executor_data.city_id = executor.city_id;
                this.executor_data.device_token = executor.device_token;
                this.executor_data.device_type = executor.device_type;
            } catch (e) {
                consoleMsg.log(`Cannot set Executor data: ${e}`);
            }
        }
        else {
            consoleMsg.log(`Cannot set Executor data: executor parameter is null`);
        }
    }

    getUserData() {
        return this.user_data ? this.user_data : null;
    }

    getExecutorData() {
        return this.executor_data ? this.executor_data : null;
    }

    getData() {
        return {
            //uuid: this.uuid,
            socket_id: this.user_socket ? this.user_socket.uuid : null,
            //data: this.data,
            data: {
                "uuid": this.data.uuid,
                "user_id": this.data.user_id,
                "status": this.data.status,
                "executor_id": this.data.executor_id,
                "city_id": this.data.city_id,
                "technic_id": this.data.technic_id,
                "address": this.data.address,
                "lat": this.data.lat.toString(),
                "long": this.data.long.toString(),
                "price": this.data.price.toString(),
                "description": this.data.description,
                "technic": {
                    "id": this.data.technic.id,
                    "type_id": this.data.technic.type_id,
                    "charac_value": this.data.technic.charac_value,
                    "type": {
                        "id": this.data.technic.type.id,
                        "title": this.data.technic.type.title,
                        "description": this.data.technic.type.description,
                        "image": this.data.technic.type.image,
                        "charac_title": this.data.technic.type.charac_title,
                        "charac_unit": this.data.technic.type.charac_unit,
                        "min_order_price": this.data.technic.type.min_order_price
                    }
                },
                "executor_technic": this.data.executor_technic,
            },
            executor: this.getExecutorData(),
            user: this.getUserData()
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
            executor_technic: order.executor_technic,
        };
    }

    async resetUser() {
        if (this.data) {
            consoleMsg.log(JSON.stringify(await models.User.getUserById(this.data.user_id)));
            this.setUserData(await models.User.getUserById(this.data.user_id));
        }
    }

    async resetExecutor() {
        if (this.data) {
            consoleMsg.log(await models.User.getUserById(this.data.user_id));
            this.setExecutorData(await models.User.getUserById(this.data.executor_id));
        }
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
    order = null;

    constructor(socket, price, order_uuid, order) {
        this.socket = socket;
        this.price = price;
        this.order_uuid = order_uuid;
        this.order = order;
    }

    getData() {
        if (!this.socket) return null;
        return {
            socket_id: this.socket.uuid,
            price: this.price,
            user: this.socket.getExecutor(),
            order: this.order.getData(),
            technic: this.socket.getTechnic(this.order.data.technic_id)
        }
    }

}

module.exports = {
    Order: Order,
    Response: Response,
};
