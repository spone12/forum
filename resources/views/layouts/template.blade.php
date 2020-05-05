<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title-block')</title>


    <link rel="stylesheet" type="text/css" href="{{ asset('resource/libraries/bootstrap/css/bootstrap.min.css') }}" />
    <script src="{{ asset('resource/libraries/jQuery/jquery-3.3.1.js') }}"></script>
    <script src="{{ asset('resource/libraries/bootstrap/js/bootstrap.min.js') }}"></script>
</head>
<body>

    @include('layout.navbar')

    @yield('content')

</body>
</html>