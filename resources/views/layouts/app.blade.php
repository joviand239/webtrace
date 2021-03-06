<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Webtrace') }}</title>

    <!-- Styles -->
    @include('layouts.parts.css')

    @yield('cssCustom')
    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
    <div id="app">
        @include('layouts.parts.header')

        @yield('content')

        @include('layouts.parts.footer')
    </div>
    <!-- Scripts -->
    @include('layouts.parts.js')

    @yield('jsCustom')
</body>
</html>
