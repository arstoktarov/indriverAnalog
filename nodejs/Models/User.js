const { Model } = require('../Modules/db');
const Technic = require('./Technic');

class User extends Model {
    static get tableName() {
        return 'users';
    }

    static relationMappings = {
        technics: {
            relation: Model.ManyToManyRelation,
            modelClass: Technic,
            join: {
                from: 'users.id',
                through: {
                    // persons_movies is the join table.
                    from: 'users_technics.user_id',
                    to: 'users_technics.technic_id'
                },
                to: 'technics.id'
            }
        }
    };

    static async getUserByToken(token, relations) {
        return await User.query().withGraphFetched(relations).where('token', token).first();
    }
}


module.exports = User;