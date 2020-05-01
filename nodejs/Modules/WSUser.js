//консоль с цветами, вместо нудного console.log() ;)
const consoleMsg = require('./consoleMsg');


//библотека для создания uuid
const uuid = require('uuid-random/index');

//дополнительные функции в другом файле
const functions = require('./additionalFunctions');

//Константы типа пользователя
const TYPE_USER = "user";
const TYPE_COURIER = "courier";


class WSUser {
    isOnline = false;
    user = null;

    //Тип: user или courier
    //type = "anonymous";

    //Только для курьера. Его локация
    // location = {
    //     lat: 0,
    //     long: 0
    // };

    //Ивенты подключенные к этому ws
    events = {
    };

    //все интервалы должны быть добавлены сюда по примеру ниже.
    // wsUser.interval(function() {}, 1000));
    // Это позволит удалить их в случае если подключение будет потеряно и ws удалится
    intervals = new Map();

    //Аналогично с интервалами.
    timeouts = new Map();

    //комнаты к которым ws подключен
    rooms = new Set();

    //Тут при создании автоматически задаётся uuid.
    constructor(ws, user = null) {
        this.ws = ws;
        this.user = user;
        this.uuid = uuid();
        ws.onclose = this.onclose;
    }

    //Аналогия функции ws.on()
    on(event, callback) {
        this.ws.on(event, callback);
    }

    //Функция для создания ивента.
    addEventListener(event, callback) {
        this.events[event] = callback;
    }

    //Аналогия функции ws.emit()
    //NOTE: ws.emit() и ws.send() не одно и то же.
    // ws.emit() принимаем event как первый параметр
    // а ws.send() автоматически отправляет на event "message".
    emit(data) {
        this.ws.emit(data);
    }

    // Аналогия функции ws.send()
    send(data) {
        if (this.ws.readyState === 1)
            this.ws.send(data);
    }

    //Вызов ивента
    callEvent(data) {
        if (this.events[data.event]) {
            consoleMsg.info("[EVENT]" + data.event);
            this.events[data.event](data.data, data.event);
        }
        else this.ws.send(functions.errorResponse({"message": 'Event not found'}));
    }

    //парсит json
    static parseJson(json) {
        try {
            return JSON.parse(json);
        }
        catch (e) {
            consoleMsg.log('Cannot parse json');
            return false;
        }
    }

    //функция для создания интервала который будет подключен к массиву intervals
    interval(name, callback, timeout, args) {
        let interval = setInterval(callback, timeout, args);
        this.intervals.set(name, interval);
        return interval;
    }

    //функция для создания таймаута который будет подключен к массиву timeouts
    timeout(name, callback, timeout, args) {
        let newTimeout = setTimeout(callback, timeout, args);
        this.timeouts.set(name, newTimeout);
        return newTimeout;
    }

    //функция которая удаляет все интервалы таймауты и остальные данные
    destroy() {
        this.destroyIntervals();
        this.destroyTimeouts();
        delete this.ws;
        delete this.user;
        delete this.events;
        delete this.uuid;
        delete this.isOnline;
    }

    //функция удаляет все интервалы
    destroyIntervals() {
        this.intervals.forEach(function(interval) {
            clearInterval(interval);
        });
        delete this.intervals;
        consoleMsg.info('Intervals destroyed');
    }

    //функция удаляет все таймауты
    destroyTimeouts() {
        this.timeouts.forEach(function(timeout) {
            clearTimeout(timeout);
        });
        consoleMsg.info('Timeouts destroyed');
    }

    //Аналогия функции .toString(). Предназначена для получения данных объкта wsUser
    getData() {
        return {
            user: this.user,
            uuid: this.uuid,
            events: Object.keys(this.events),
            isOnline: this.isOnline,
            type: this.type,
            location: this.location,
            rooms: {
                count: this.rooms.size,
                data: Array.from(this.rooms)
            },
            timeoutsCount: this.timeouts.length,
            intervalsCount: this.intervals.length,
        }
    }
}

module.exports.User = WSUser;
module.exports.TYPE_USER = TYPE_USER;
module.exports.TYPE_COURIER = TYPE_COURIER;
