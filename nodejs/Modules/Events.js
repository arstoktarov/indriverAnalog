const events = require('events');

// Create an eventEmitter object
var eventEmitter = new events.EventEmitter();

module.exports = eventEmitter;

// var events = require('events'),
//     EventEmitter = events.EventEmitter;
//
// var emitter = function() {
//     if ( arguments.callee._singletonInstance )
//         return arguments.callee._singletonInstance;
//     arguments.callee._singletonInstance = this;
//     EventEmitter.call(this);
// };
//
// emitter.prototype.__proto__ = EventEmitter.prototype;
//
// module.exports = new emitter();