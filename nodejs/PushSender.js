const pushSender = require('./Modules/send_push');
const consoleMsg = require('./Modules/consoleMsg');

function sendPush(device_token, device_type, title, body) {
    try {
        let params = pushSender.paramsByToken(device_token, device_type, title, body);
        pushSender.sendPush(params);
    } catch (e) {
        consoleMsg.log("Error sending push: " + e);
    }
}


module.exports.sendPush = sendPush;
