const { Model } = require('../Modules/db');
const Technic = require('./Technic');
const Technic_Type = require('./Technic_Type');

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

    static async getUserByToken(token) {
        return await User.query()
            .withGraphFetched('[technics.type]')
            .modifyGraph('technics', builder => {
                builder.select('technics.id', 'technic_id', 'image', 'description', 'type_id', 'charac_value')
            })
            .modifyGraph('technics.type', builder => {
                builder.select(Technic_Type.select_columns)
            })
            .where('token', token)
            .first();
    }
}


module.exports = User;