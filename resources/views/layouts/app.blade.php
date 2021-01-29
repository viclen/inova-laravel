<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/mobile-check.js') }}"></script>
    <script src="{{ asset('js/app.js') }}?{{ filemtime(__DIR__ . '/../../../public/js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="{{ asset('css/fontawesome/css/all.css') }}" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        .text-ellipsis {
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px;
        }

        .text-ellipsis:hover {
            max-width: 100%;
        }
    </style>
</head>

<body>
    <div id="app">
        {{-- <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
        {{ config('app.name', 'Inova') }}
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto">
                <a href="{{route('clientes.index')}}" class="nav-link">Clientes</a>
                <a href="{{route('carros.index')}}" class="nav-link">Carros</a>
                <a href="{{route('marcas.index')}}" class="nav-link">Marcas</a>
                <a href="{{route('estoques.index')}}" class="nav-link">Estoques</a>
                <a href="{{route('regras.index')}}" class="nav-link">Regras</a>
                <a href="{{route('interesses.index')}}" class="nav-link">Interesses</a>
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
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        {{ Auth::user()->name }} <span class="caret"></span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </li>
                @endguest
            </ul>
        </div>
    </div>
    </nav> --}}

    <main class="py-4">
        <sidebar :user="{{ json_encode(Auth::user()) ?: '' }}" csrf="{{ csrf_token() }}">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                @foreach (explode(".", Route::currentRouteName()) as $i => $item)
                                <li class="breadcrumb-item text-capitalize" aria-current="page">
                                    @if ($i == 0 && Route::has($item . '.index'))
                                    <a href="{{ route($item . '.index') }}">
                                        {{ __($item) }}
                                    </a>
                                    @else
                                    {{ __($item) }}
                                    @endif
                                </li>
                                @endforeach
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            @yield('content')
        </sidebar>
    </main>
    </div>

    @yield('scripts')

    <script>
        setTimeout(() => {
            if(window.mobileCheck){
                let el;
                if (window.mobileCheck()) {
                    el = document.getElementById("whatsWeb");
                } else {
                    el = document.getElementById("whatsMobile");
                    const web= document.getElementById("whatsWeb");
                    if(web)
                        web.setAttribute("target", "_blank");
                }
                if (el)
                    el.remove();
            }
        }, 1000);
    </script>
</body>

</html>
