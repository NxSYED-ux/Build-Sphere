<!DOCTYPE html>
<head>
    <title>Pusher Test</title>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>

        Pusher.logToConsole = true;

        var pusher = new Pusher('9d0fb6df9f1d15dbc9bd', {
            cluster: 'ap2'
        });

        var channel = pusher.subscribe('private-App.Models.User.1');
        channel.bind('', function(data) {
            alert(JSON.stringify(data));
        });
    </script>
</head>
<body>
<h1>Pusher Test</h1>
<p>
    Try publishing an event to channel <code>my-channel</code>
    with event name <code>my-event</code>.
</p>
</body>
