const { Model } = require('objection');
const Technic = require('./Technic');

class TechnicType extends Model {
    static select_columns = [
        'id', 'title', 'description', 'image', 'charac_title', 'charac_unit', 'min_order_price'
    ];

    static get tableName() {
        return 't_types';
    }

    static relationMappings = {
        type: {
            relation: Model.HasManyRelation,
            modelClass: Technic,
            join: {
                from: 't_types.id',
                to: 'technics.type_id',
            }
        }
    }
}

module.exports = TechnicType;
