const { Model } = require('objection');

class Technic extends Model {
    static get tableName() {
        return 'technics';
    }
}

module.exports = Technic;