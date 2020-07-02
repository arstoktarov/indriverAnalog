const app = require('express')();
let server = require('http').Server(app);
const httpPort = 3000;
server.listen(httpPort, 'localhost');
console.info('HTTP server started at ' + httpPort);

module.exports.app = app;
module.exports.server = server;