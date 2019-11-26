<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <base href="{{ url('/') }}">
    <link href="{{ mix('/css/app.css') }}" rel="stylesheet" />
    <script src="https://www.gstatic.com/charts/loader.js" defer></script>
    <script src="{{ mix('/js/app.js') }}" defer></script>
  </head>
  <body class="antialiased">
    @inertia
  </body>
</html>
