class Rooms {

    wsRooms = new Map();

    constructor(wss) {
        this.wss = wss;
    }

    newRoom(roomId) {
        this.wsRooms.set(roomId, new Set());
    }

    attach(roomId, elem) {
        if (!this.wsRooms.get(roomId)) this.newRoom(roomId);
        this.wsRooms.get(roomId).add(elem);
    }

    removeRoom(roomId) {
        delete this.wsRooms.delete(roomId);
        console.log("Room removed");
    }

    removeElem(roomId, elem) {
        if (this.wsRooms.get(roomId)) this.wsRooms.get(roomId).delete(elem);
        if (this.wsRooms.get(roomId).size <= 0) this.removeRoom(roomId);
    }

    getWsRooms() {
        return this.wsRooms;
    }

    getWsRoom(key) {
        return this.wsRooms.get(key) ? Array.from(this.wsRooms.get(key)) : null;
    }

    broadcast(roomId, message) {
        if (this.wsRooms.get(roomId)) {
            this.wsRooms.get(roomId).forEach(function (user) {
                user.send(message);
            });
        }
    }

}

module.exports.roomsModel = Rooms;
