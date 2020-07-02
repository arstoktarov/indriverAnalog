function pluckAssoc(array, key) {
    array = Object.keys(array).map(k => array[k]);
    return array.map(o => o[key]);
}

function pluck(array, key) {
    return Array.from(array).map(o => o[key]);
}

function response(eventName, data) {
    return JSON.stringify({
        event: eventName,
        data: data,
    }, null, 4);
}

function errorResponse(data) {
    return JSON.stringify({
        event: "error",
        data: data,
    }, null, 4);
}

function setFind(set, callback) {
    for (const elem of set) {
        if (callback(elem)) {
            return elem;
        }
    }
    return undefined;
}

module.exports.pluckAssoc = pluckAssoc;
module.exports.pluck = pluck;
module.exports.response = response;
module.exports.errorResponse = errorResponse;
module.exports.setFind = setFind;