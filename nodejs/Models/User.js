const { Model } = require('../Modules/db');

class User extends Model {
    static get tableName() {
        return 'users';
    }


    static async getUserByToken(token) {
        return await User.query().where('token', token).first();
    }
}


module.exports = User;