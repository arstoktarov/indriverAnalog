@php

$users = \App\Models\User::where('type', 1)->get();
$cities = \App\Models\City::all();
$technics = \App\Models\Technic::all();



@endphp

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <title>Hello, world!</title>
</head>
<body style="background-color: grey">
        <div class="row m-3">
            <div class="col-6">
               <!--  Connection -->
                <div class="row mb-2">
                    <div class="card w-100">
                        <div class="card-body">
                            <h5 class="card-title">Connection - User</h5>
                            <div class="input-group">
                                <select class="custom-select" id="token_input" aria-label="Example select with button addon">
                                    @foreach ($users as $user)
                                    <option value="{{$user->token}}">{{$user->name}} - {{$user->type == 1 ? 'User' : 'Executor'}}</option>
                                    @endforeach
                                </select>
                                <div class="input-group-append">
                                    <button id="connection_button" class="btn btn-outline-secondary" type="button">Connect</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--  Make Order -->
                <div class="row mb-2">
                    <div class="card w-100">
                        <div class="card-body">
                            <h5 class="card-title">Make Order</h5>
                            <h6 class="card-subtitle mb-2 text-muted">Event: makeOrder</h6>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="city_id_select">City</label>
                                </div>
                                <select class="custom-select" id="city_id_select">
                                    @foreach($cities as $city)
                                    <option value="{{$city->id}}">{{$city->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="technic_id_select">Technic</label>
                                </div>
                                <select class="custom-select" id="technic_id_select">
                                    @foreach($technics as $technic)
                                        <option value="{{$technic->id}}">{{$technic->id}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon3">address</span>
                                </div>
                                <input type="text" class="form-control" value="Klochkova 130" id="address" aria-describedby="basic-addon3">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon3">lat</span>
                                </div>
                                <input type="text" class="form-control" value="9.123123" id="lat" aria-describedby="basic-addon3">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon3">long</span>
                                </div>
                                <input type="text" class="form-control" value="9.123123" id="long" aria-describedby="basic-addon3">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon3">price</span>
                                </div>
                                <input type="text" class="form-control" value="5000" id="price" aria-describedby="basic-addon3">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon3">description</span>
                                </div>
                                <input type="text" class="form-control" value="Some desc" id="description" aria-describedby="basic-addon3">
                            </div>
                            <div class="text-right">
                                <button id="close_order_button" type="button" class="btn btn-danger">Close order</button>
                                <button id="make_order_button" type="button" class="btn btn-success">Make order</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="card w-100">
                        <div class="card-body">
                            <h5 class="card-title">Choose executor</h5>
                            <h6 class="card-subtitle mb-2 text-muted">event: ChooseExecutor</h6>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon3">Executor UUID</span>
                                </div>
                                <input type="text" class="form-control" value="" id="executor_uuid" aria-describedby="basic-addon3">
                            </div>
                            <div class="text-right">
                                <button id="decline_executor_button" type="button" class="btn btn-danger">Decline</button>
                                <button id="choose_executor_button" type="button" class="btn btn-success">Choose</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!--  Get My Data -->
                <div class="row mb-2">
                    <div class="card w-100">
                        <div class="card-body">
                            <h5 class="card-title">My data</h5>
                            <h6 class="card-subtitle mb-2 text-muted">Card subtitle</h6>
                            <div class="text-right">
                                <button id="get_user_button" type="button" class="btn btn-primary">Refresh</button>
                            </div>
                            <pre id="my_data"></pre>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="card w-100">
                        <div class="card-body">
                            <h5 class="card-title">My order</h5>
                            <h6 class="card-subtitle mb-2 text-muted">Card subtitle</h6>
                            <div class="text-right">
                                <button id="get_order_button" type="button" class="btn btn-primary">Refresh</button>
                            </div>
                            <pre id="my_order_data"></pre>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card">
                    <button id="clear_console_button" type="button" class="btn btn-primary">Clear</button>
                    <div id="messages" class="card" style="overflow: scroll; height: 700px">
                </div>
                </div>
            </div>
        </div>
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

<script>
    let reconnectInterval = null;
    let messages = document.getElementById('messages');
    function connect() {
        const url = 'ws://194.4.58.237:8080';
        renderMessage(`Connecting to ${url}`, 'red');
        const ws = new WebSocket(url);

        ws.events = new Map();

        ws.onopen = function open() {
            clearTimeout(reconnectInterval);
            renderMessage('Successfully connected', 'green');
            console.log('Connection opened');
        };

        ws.onmessage = function incoming(msgEvent) {
            handleEvent(msgEvent.data);
        };

        ws.onclose = function (errorCode) {
            clearTimeout(reconnectInterval);
            console.log('Connection closed:', errorCode);
            reconnectInterval = setTimeout(connect, 5000);
        };

        function handleEvent(data) {
            renderReceived(data);
            let parsed = null;
            try {
                parsed = JSON.parse(data);
            }
            catch (e) {
                console.log('Error:', e);
            }
            if (parsed) {
                if (parsed['event'] && parsed['data']) {
                    if (ws.events.has(parsed['event'])) {
                        ws.events.get(parsed['event'])(parsed['data']);
                    }
                }
            }
        }

        ws.events.set('orders', function(data) {
            console.log("get data:", data);
            document.getElementById('order_uuid').value = data.data.uuid;
        });

        ws.events.set('getMyData', function(data) {
            let myData = document.getElementById('my_data');
            myData.innerText = toJson(data);
        });

        ws.events.set('connection', function(data) {
            console.log('hello');
        });

        ws.events.set('newResponse', function(data) {
            document.getElementById('executor_uuid').value = data[0].socket_id;
            //let confirmed = window.confirm("You have new response!");
            //let response = {executor_uuid: data[0].socket_id};

            //if (confirmed) sendMessage('chooseExecutor', response, ws);
            //else sendMessage('declineExecutor', response, ws);
        });

        ws.events.set('myOrder', function(data) {
            document.getElementById('my_order_data').innerText = toJson(data);
        });

        document.getElementById('connection_button').onclick = function() {
            let token = document.getElementById('token_input').value;
            let data = {
                token: token,
            };
            sendMessage('connection', data, ws);
        };

        document.getElementById('make_order_button').onclick = function() {
            let city_id = document.getElementById('city_id_select').value;
            let technic_id = document.getElementById('technic_id_select').value;
            let address = document.getElementById('address').value;
            let lat = document.getElementById('lat').value;
            let long = document.getElementById('long').value;
            let price = document.getElementById('price').value;
            let description = document.getElementById('description').value;

            let data = {
                city_id: city_id,
                technic_id: technic_id,
                address: address,
                lat: lat,
                long: long,
                price: price,
                description: description,
            };

            sendMessage('makeOrder', data, ws);
        };

        document.getElementById('close_order_button').onclick = function() {

            sendMessage('closeOrder', {}, ws);
        };

        document.getElementById('get_user_button').onclick = function() {
            sendMessage('getMyData', {}, ws);
        };

        document.getElementById('get_order_button').onclick = function() {
            sendMessage('myOrder', {}, ws);
        };

        document.getElementById('clear_console_button').onclick = function() {
            messages.innerHTML = '';
        };

        document.getElementById('choose_executor_button').onclick = function() {
            let executor_uuid = document.getElementById('executor_uuid').value;
            let data = {
                executor_uuid: executor_uuid
            };
            sendMessage('chooseExecutor', data, ws);
        };

        document.getElementById('decline_executor_button').onclick = function() {
            let executor_uuid = document.getElementById('executor_uuid').value;
            let data = {
                executor_uuid: executor_uuid
            };
            sendMessage('declineExecutor', data, ws);
        };
    }
    connect();


    function toJson(data, format = true) {
        return format ? JSON.stringify(data, null, 4) : JSON.stringify(data);
    }

    function sendData(event, data, ws) {
        sendMessage(event, data, ws);
    }

    function sendMessage(event, data, ws) {
        data = JSON.stringify({
            event: event,
            data: data,
        });
        renderSent(data);
        ws.send(data);
    }

    function renderReceived(message, color) {
        let div = document.createElement('div');
        div.innerHTML =
            `<div>
                <p style="color:darkgreen">Received:<p>
                <pre class="m-2" style="color:${color}">${message}</pre>
                <hr>
            </div>`;

        messages.appendChild(div);
    }

    function renderMessage(message, color) {
        let div = document.createElement('div');
        div.innerHTML =
            `<div>
                <pre class="m-2" style="color:${color}">${message}</pre>
                <hr>
            </div>`;

        messages.appendChild(div);
    }

    function renderSent(message, color) {
        let div = document.createElement('div');
        div.innerHTML =
            `<div>
                <p style="color:darkgreen">Sent:<p>
                <pre class="m-2" style="color:${color}">${message}</pre>
                <hr>
            </div>`;

        messages.appendChild(div);
    }
</script>
<script>
</script>
</body>
</html>
