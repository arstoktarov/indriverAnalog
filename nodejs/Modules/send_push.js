const request = require('request');

const FIREBASE_API_KEY = 'AAAAnxx4nsM:APA91bEaLnE2pZsPQoJE7bNDirNELU0mOQQacy2sjzYJ3QLcZ97XyHy7sJBZJlvj7oydRMj72Jis0PQY93Wo16ZvOtLK4-ZgY3NrR_WiNDSJ2fz4JPDnF8Nl2JSdZmDgx1z9yfoVSNDc';
const URL = 'https://fcm.googleapis.com/fcm/send';


async function sendPush(params) {
    let headers = {
        'Authorization': `key=${FIREBASE_API_KEY}`,
        'Content-Type': 'application/json'
    };
    let query = {
        url: URL,
        method: 'POST',
        headers: headers,
        time_to_live: 300,
    };

    query.json = params;

    request(query, function (error, response, body) {
        console.log(body);
    });
}

function paramsByDeviceTokens(registration_id, type, title, body) {
    if (type === 'ios') {
        return {
            'registration_ids': [registration_id],
            'data': {
                'title': title,
                'body': body,
                'sound': 'default',
            },
            'notification': {
                'title': title,
                'body': body,
                'sound': 'default',
            },
        };
    }
    else {
        return {
            'registration_ids': [registration_ids],
            'data': {
                'title': title,
                'body': body,
                'sound': 'default',
            },
            'notification': {
                'title': title,
                'body': body,
                'sound': 'default',
            },
        };
    }
}

function paramsByTopic(topic, type, title, body) {
    if (type === 'ios') {
        return {
            'to': `${topic}_a`,
            'data': {
                'title': title,
                'body': body,
                'sound': 'default',
            },
            'notification': {
                'title': title,
                'body': body,
                'sound': 'default',
            },
        };
    }
    else {
        return {
            'to': `${topic}`,
            'data': {
                'title': title,
                'body': body,
                'sound': 'default',
            },
            'notification': {
                'title': title,
                'body': body,
                'sound': 'default',
            },
        };
    }
}

module.exports.sendPush = sendPush;
module.exports.paramsByDeviceTokens = paramsByDeviceTokens;
module.exports.paramsByTopic = paramsByTopic;

