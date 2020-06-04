const { Model } = require('../Modules/db');
const Technic = require('./Technic');

class Order extends Model {
    static get tableName() {
        return 't_orders';
    }

    static relationMappings = {
        technic: {
            relation: Model.BelongsToOneRelation,
            modelClass: Technic,
            join: {
                from: 't_orders.technic_id',
                to: 'technics.id'
            }
        }
    };

    createOrder() {

    }

    static NOT_STARTED = -1;
    static IN_PROCESS = 0;
    static DONE = 1;
    static CANCELED = 2;
}

module.exports = Order;