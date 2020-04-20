// run the following command to install:
// npm install objection knex sqlite3
const { Model } = require('objection');
const Knex = require('knex');

// Initialize knex.
const knex = require('knex')({
    client: 'mysql',
    connection: {
        host : '127.0.0.1',
        user : 'root',
        password : 'adgjmp96',
        database : 'indriveranalog'
    }
});

// Give the knex instance to objection.
Model.knex(knex);



class User extends Model {
    static get tableName() {
        return 'users';
    }
}

async function getUserByToken(token) {
    return await User.query().where('token', token).first();
}



module.exports.User = User;
module.exports.getUserByToken = getUserByToken;