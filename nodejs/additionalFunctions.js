
//pluck custom realization for assoc array
function pluckAssoc(array, key) {
    array = Object.keys(array).map(k => array[k]);
    return array.map(o => o[key]);
}

//Pluck custom realization
function pluck(array, key) {
    return Array.from(array).map(o => o[key]);
}

//преобразует json
function errorResultJson(errorCode, error) {
    let result = {
        errorCode: errorCode,
        error: error
    };
    return JSON.stringify(result, null, 4);
}

//преобразует json
function resultJson(statusCode, data, message) {
    let result = {
        statusCode: statusCode ? statusCode : 200,
        data: data,
        message: message ? message : 'Success',
    };
    return JSON.stringify(result);
}

//преобразует json
function getJsonResponse(eventName, data) {
    return JSON.stringify({
        event: eventName,
        data: data
    }, null, 4);
}

//Own realization of rules. Не используется
function validateMessage(data, rules) {
    // let parsedData = null;
    // try {
    //     parsedData = JSON.parse(data);
    // }
    // catch (e) {
    //     return false;
    // }
    //if (!data) return false;

    return validateRule(data, rules, true);
}

//не используется
function validateRule(data, rules, correct) {
    Object.keys(rules).forEach(function(rule) {
        if (!data[rule]) {
            correct = false;
        }
        else if (typeof data[rule] == "object") correct = validateRule(data[rule], rules[rule], correct);
    });
    return correct;
}

module.exports.pluckAssoc = pluckAssoc;
module.exports.pluck = pluck;
module.exports.errorResultJson = errorResultJson;
module.exports.resultJson = resultJson;
module.exports.getJsonResponse = getJsonResponse;
module.exports.validateMessage = validateMessage;