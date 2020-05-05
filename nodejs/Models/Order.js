const { Model } = require('../Modules/db');

class Order extends Model {
    static get tableName() {
        return 't_orders';
    }

    createOrder() {

    }
}

module.exports = Order;