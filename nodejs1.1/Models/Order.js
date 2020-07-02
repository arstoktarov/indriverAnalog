const { Model } = require('../Modules/db');
const Technic = require('./Technic');
const Technic_Type = require('./Technic_Type');
const User_Technic = require('./User_Technic');

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
        },
        executor_technic: {
            relation: Model.BelongsToOneRelation,
            modelClass: User_Technic,
            join: {
                from: 't_orders.executor_technic_id',
                to: 'users_technics.id'
            }
        }
    };

    createOrder() {

    }

    static getOrderWithTechnic() {
        return Order.query()
            .withGraphFetched('technic', 'executor_technic')
            .modifyGraph('technic', builder => {
                builder.select(Technic.columns)
                    .withGraphFetched('type')
                    .modifyGraph('type', builder => {
                        builder.select(Technic_Type.select_columns);
                    });
            })
            .modifyGraph('executor_technic', builder => {
                builder.withGraphFetched('technic')
                    .modifyGraph('technic', builder => {
                        builder.select(Technic.columns)
                        .withGraphFetched('type')
                        .modifyGraph('type', builder => {
                            builder.select(Technic_Type.select_columns);
                        });
                    })
            })
            .where('status', Order.IN_PROCESS);
    }

    static NOT_STARTED = -1;
    static IN_PROCESS = 1;
    static DONE = 2;
    static CANCELED = 3;
}

module.exports = Order;
