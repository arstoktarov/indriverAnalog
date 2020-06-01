const { Model } = require('objection');
const Technic_Type = require('./Technic_Type');

class Technic extends Model {
    static columns = ['id', 'type_id', 'charac_value'];

    static get tableName() {
        return 'technics';
    }

    static relationMappings = {
        type: {
            relation: Model.BelongsToOneRelation,
            modelClass: Technic_Type,
            join: {
                from: 'technics.type_id',
                to: 't_types.id'
            }
        }
    };

    static async getTechnicById(id) {
        return await Technic.query()
            // .withGraphFetched('type')
            // .modifyGraph('type', builder => {
            //     builder.select(Technic_Type.select_columns);
            // })
            .select(this.columns)
            .findById(id);
    }
}

module.exports = Technic;