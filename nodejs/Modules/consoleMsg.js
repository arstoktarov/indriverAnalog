var colors = require('colors/safe');

function log(message) {
    console.log(colors.green("[Log] ") + message);
}

function error(message) {
    console.log(colors.red("[Error] ") + message);
}

function info(message) {
    console.log(colors.blue("[Info] ") + message);
}

function warning(message) {
    console.log(colors.yellow("[Warning] ") + message);
}

module.exports.log = log;
module.exports.error = error;
module.exports.info = info;
module.exports.warning = warning;