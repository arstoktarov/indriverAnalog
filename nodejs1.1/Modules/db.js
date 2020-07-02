// run the following command to install:
// npm install objection knex sqlite3
const { Model } = require('objection');

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
Model.knex(knex);

module.exports.Model = Model;
module.exports.knex = knex;