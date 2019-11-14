<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Scoreboard</title>

    <link href="{{ mix('/css/sb.css') }}" rel="stylesheet">

    <script>
        window.app = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>

</head>

<body>

    <div id="app">

        <feedback></feedback>

        <div class="container">
            @yield('content')
        </div>

    </div>

    <script defer src="https://use.fontawesome.com/releases/v5.2.0/js/all.js" integrity="sha384-4oV5EgaV02iISL2ban6c/RmotsABqE4yZxZLcYMAdG7FAPsyHYAPpywE9PJo+Khy" crossorigin="anonymous"></script>
    <script src="{{ mix('/js/scoreboard.js') }}"></script>

</body>

</html>
