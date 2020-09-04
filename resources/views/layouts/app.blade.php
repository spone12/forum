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
    <!--link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet"-->

    <!-- Styles -->
    <!--link href="{{ asset('resource/css/forum.css') }}" rel="stylesheet"-->
    <link href="{{ asset('resource/css/app.css') }}" rel="stylesheet" type='text/css'>
    <link rel="stylesheet" type="text/css" href="{{ asset('resource/libraries/bootstrap/css/bootstrap.min.css') }}" />

    <script src="{{ asset('resource/libraries/jQuery/jquery-3.3.1.js') }}"></script>
    <script src="{{ asset('resource/libraries/popper/popper.min.js') }}"></script>
    <script src="{{ asset('resource/libraries/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('resource/js/nav.js') }}"></script>
    @stack('scripts')
</head>
<body>

        <nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm fix_nav">
            <div class="container">
            <a class="navbar-brand" href="{{ route('home')}}"><img width=30 class='logo' src="{{ asset('img/logo/logo.png') }}" /></a>
                <a class="navbar-brand" href="{{ route('home')}}">
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
                            <div class="input-group">
                                <div class="input-group-prepend search-button">
                                    <button class="input-group-text" id="search-button">
                                        <img src="{{ url('/img/icons/search.png') }}" width="20">
                                    </button>
                                </div>
                                <input id='search' name="search" type="text" class="form-control" placeholder="{{ __('app.search') }}" aria-label="search" aria-describedby="search-button">
                                    <span class="search-marker">/</span>
                                <input type='hidden' name='search-by' value='search-by__user' id='search-by' />    
                            </div>
                        </form>
                    </ul>
                    
                    <ul class="mb-3 btn-group">
                        <button class="btn btn-secondary btn-sm lang" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class='lang__flag' src="{{ url( __('app.lang_src') ) }}" width="30">
                        </button>
                        <div class="dropdown-menu lang__drop">
                            <div class='col'>
                                <img class='lang__flag' src="{{ url('/img/icons/lang/ru.png') }}" width="20">
                                <a href="{{ route('locale', ['locale' => 'ru']) }}">Русский</a>
                            </div>
                            <div class='col'>
                                <img class='lang__flag' src="{{ url('/img/icons/lang/en.png') }}" width="20">
                                <a href="{{ route('locale', ['locale' => 'en']) }}">English</a>
                            </div>
                        </div>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ trans('auth.enter') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ trans('auth.registration') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown menu_user">
                                    <!--dropdown-toggle class-->
                                <a id="navbarDropdown" class="nav-link  c" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <span class='name_profile'>{{ Auth::user()->name }} </span>
                                    <img class='mini_avatar' style='margin-right: 5px;' width=30 src=@getSessValue('avatar') />
                                    <span class="caret">
                                     <img id='navigation_arrow' width=10 src="{{ url('/img/icons/arrow.svg') }}" /> 
                                    </span>
                                </a>
                               
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                   
                                    <a class="dropdown-item" href="{{ route('profile')}}" onclick="">
                                        <div>
                                            <img width=20 src="{{ url('/img/icons/profile.png') }}" /> 
                                            <span class='icon-text'>{{ trans('app.profile') }}</span>
                                        </div>
                                    </a>

                                    <a class="dropdown-item" href="{{ url('/') }}"
                                       onclick=''>
                                        <div>
                                            <img width=20 src="{{ url('/img/icons/message.png') }}" /> 
                                            <span class='icon-text'>{{ trans('app.messages') }}</span>
                                        </div>
                                    </a>

                                    <a class="dropdown-item" href="{{ route('map') }}" >
                                        <div>
                                            <img width=20 src="{{ url('/img/icons/map.svg') }}" /> 
                                            <span class='icon-text'>{{ trans('app.map') }}</span>
                                        </div>
                                    </a>

                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <div>
                                            <img width=20 src="{{ url('/img/icons/logout.png') }}" /> 
                                            <span class='icon-text'>{{ trans('app.logout') }}</span>
                                        </div>
                                    </a>

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

        <div id='modal_window' class="modal fade">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                <div class="modal-header">
                    <button type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        <a href='#' id='modal_window_close'>
                            &times;
                        </a>
                    </span>
                    </button>
                 </div>
                <div class="modal-body row justify-content-center"> 
                    <div class='col-8'><p class='c' id='modal_window_text'>&hellip;</p></div>
                    <div class='col-2'>
                        <input type='hidden' class='btn btn-danger' value='Удалить' id='modal_window_button' />
                    </div>
                </div>

                </div>
            </div>
        </div>

        <main class="py-4 fix_main">
            @include('layouts.messages')
            @yield('content')
        </main>
    
</body>
</html>
