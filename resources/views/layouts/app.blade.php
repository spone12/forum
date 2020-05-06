<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title-block')</title>
    <!-- Scripts -->
    <!--script src="{{ asset('js/app.js') }}" defer></script-->

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('resource/css/forum.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('resource/libraries/bootstrap/css/bootstrap.min.css') }}" />
    <script src="{{ asset('resource/libraries/jQuery/jquery-3.3.1.js') }}"></script>
    <script src="{{ asset('resource/libraries/bootstrap/js/bootstrap.min.js') }}"></script>
</head>
<body>

        <nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm fix_nav">
            <div class="container">
            <a class="navbar-brand" href="/"><img width=30 src="{{ asset('img/logo/logo.png') }}" /></a>
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>
            

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                    
                    </ul>

                    <ul>
                        <form class="form-inline my-2 my-lg-0" method="POST" action="{{ route('search') }}">
                            @csrf
                            <li class="no_mark"> <input class="form-control mr-sm-2" type="search" name="search" placeholder="Поиск" aria-label="Search"></li>
                            <li class="no_mark"> <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Поиск</button></li>
                        </form>
                    </ul>
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle c" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="/profile" onclick="">Профиль</a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        Выйти
                                    </a>
                                    {{-- __('Logout') --}}
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>

                                   
                                </div>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>


        <main class="py-4 fix_main">
            @yield('content')
        </main>
    
</body>
</html>
