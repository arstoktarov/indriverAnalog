const { Model } = require('objection');

class Technic extends Model {
    static get tableName() {
        return 'users';
    }
}

module.exports.TechnicModel = Technic;