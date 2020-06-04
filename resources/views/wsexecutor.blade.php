@php

    $users = \App\Models\User::where('type', 2)->get();
    $cities = \App\Models\City::all();
    $technics = \App\Models\Technic::all();

@endphp
<!DOCTYPE html>
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
                    <h5 class="card-title">Connection - Executor</h5>
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
        <div class="row mb-2">
            <div class="card w-100">
                <div class="card-body">
                    <h5 class="card-title">Respond order</h5>
                    <h6 class="card-subtitle mb-2 text-muted">Card subtitle</h6>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="order_uuid_span">Order UUID</span>
                        </div>
                        <input type="text" class="form-control" value="" id="order_uuid" aria-describedby="order_uuid_span">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="order_price_span">Order Price</span>
                        </div>
                        <input type="text" class="form-control" value="" id="order_price" aria-describedby="order_price_span">
                    </div>
                    <div class="text-right">
                        <button id="respond_order_button" type="button" class="btn btn-primary">Respond order</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-2">
            <div class="card w-100">
                <div class="card-body">
                    <h5 class="card-title">Accept order</h5>
                    <h6 class="card-subtitle mb-2 text-muted">Card subtitle</h6>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="accept_order_span">Order UUID</span>
                        </div>
                        <input type="text" class="form-control" value="" id="accept_order_uuid" aria-describedby="accept_order_span">
                    </div>
                    <div class="text-right">
                        <button id="accept_order_button" type="button" class="btn btn-primary">Accept order</button>
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
                    <h5 class="card-title">OrderDone</h5>
                    <h6 class="card-subtitle mb-2 text-muted">Card subtitle</h6>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="accept_order_span">Order UUID</span>
                        </div>
                        <input type="text" class="form-control" value="" id="done_order_uuid" aria-describedby="accept_order_span">
                    </div>
                    <div class="text-right">
                        <button id="order_done_button" type="button" class="btn btn-success">DONE ORDER</button>
                    </div>
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
            document.getElementById('order_uuid').value = data.length ? data[0].data.uuid : '';
        });

        ws.events.set('makeOrder', function(data) {
            document.getElementById('order_uuid').value = data.data.uuid;
        });

        ws.events.set('getMyData', function(data) {
            let myData = document.getElementById('my_data');
            myData.innerText = toJson(data);
        });

        ws.events.set('connection', function(data) {
            console.log('hello');
        });

        ws.events.set('userResponded', function(data) {
            document.getElementById('accept_order_uuid').value = data.data.uuid;
        });

        ws.events.set('orderStarted', function(data) {
            document.getElementById('done_order_uuid').value = data.data.uuid;
        });

        document.getElementById('connection_button').onclick = function() {
            let token = document.getElementById('token_input').value;
            let data = {
                token: token,
            };
            sendMessage('connection', data, ws);
        };

        document.getElementById('respond_order_button').onclick = function() {
            let order_uuid = document.getElementById('order_uuid').value;
            let order_price = document.getElementById('order_price').value;

            let data = {
                order_uuid: order_uuid,
                price: order_price
            };

            sendMessage('respondOrder', data, ws);
        };

        document.getElementById('accept_order_button').onclick = function() {
            let order_uuid = document.getElementById('accept_order_uuid').value;

            let data = {
                order_uuid: order_uuid,
            };

            sendMessage('acceptOrder', data, ws);
        };

        document.getElementById('get_user_button').onclick = function() {
            sendMessage('getMyData', {}, ws);
        };

        document.getElementById('order_done_button').onclick = function() {
            let order_uuid = document.getElementById('done_order_uuid').value;
            sendMessage('orderDone', {order_uuid: order_uuid}, ws);
        };

        document.getElementById('clear_console_button').onclick = function() {
            messages.innerHTML = '';
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
                <h2 style="color:darkgreen">Received:</h2>
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
                <h2 style="color:darkgreen">Sent:</h2>
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
