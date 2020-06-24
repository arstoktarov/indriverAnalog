const { Model } = require('../Modules/db');
const Technic = require('./Technic');
const Technic_Type = require('./Technic_Type');

class User extends Model {
    static get tableName() {
        return 'users_technics';
    }

    static relationMappings = {
        technic: {
            relation: Model.BelongsToOneRelation,
            modelClass: Technic,
            join: {
                from: 'users_technics.technic_id',
                to: 'technics.id'
            }
        }
    };


}


module.exports = User;
