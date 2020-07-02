const pushService = require('./Modules/send_push');
const consoleMsg = require('./Modules/consoleMsg');

async function sendPush(device_token, device_type, title, body) {
    try {
        let params = pushService.paramsByToken(device_token, device_type, title, body);
        await pushService.sendPush(params);
    } catch (e) {
        consoleMsg.log("Error sending push: " + e);
    }
}

function sendToUser() {

}


module.exports.sendPush = sendPush;
