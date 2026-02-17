<!DOCTYPE html>
<html class="loading" lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <title>{{ env('APP_NAME') }}</title>
    <meta name="description" content="{{ env('APP_TITLE') }}">
    <meta name="author" content="ID Innovacion Digital">
    {{-- FAVICON --}}
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('img/site.webmanifest') }}">
    {{-- GLOBAL STYLES --}}
    @include('layouts.theme.styles')
</head>

<body class="vertical-layout vertical-menu-modern blank-page navbar-floating footer-static" data-open="click"
    data-menu="vertical-menu-modern" data-col="blank-page">
    {{-- CONTENT --}}
    <div class="app-content content width-pc">
        <div class="content-wrapper">
            <div class="content-header row"></div>
            <div class="content-body">
                <div class="auth-wrapper auth-cover">
                    <div class="auth-inner row m-0">
                        <a class="brand-logo" href="index.html">
                            <img src="{{ asset('img/logo.png') }}" alt="Sistema de indicadores" width="200">
                        </a>
                        <div class="d-none d-lg-flex col-lg-8 align-items-center p-5">
                            <div class="w-100 d-lg-flex align-items-center justify-content-center px-5">
                                <img class="img-fluid" src="{{ asset('img/logo_ini.jpg') }}"
                                    alt="Sistema indicadores" />
                            </div>
                        </div>
                        <div class="d-flex col-lg-4 align-items-center auth-bg px-2 p-lg-5">
                            <div class="col-12 col-sm-8 col-md-6 col-lg-12 px-xl-2 mx-auto">
                                <h2 class="card-title fw-bold mb-1">Bienvenido!</h2>
                                <p class="card-text mb-2">Sistema de información de indicadores</p>
                                <form class="auth-login-form mt-2" method="POST" action="{{ route('login') }}">
                                    @if (Session::get('fail'))
                                        <div class="alert alert-danger">
                                            {{ Session::get('fail') }}
                                        </div>
                                    @endif
                                    @csrf
                                    <div class="mb-1">
                                        <label class="form-label" for="login-email">Usuario</label>
                                        <input class="form-control" id="email" name="email" type="email"
                                            aria-describedby="login-email" autofocus="" tabindex="1" />
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="mb-1">
                                        <div class="d-flex justify-content-between">
                                            <label class="form-label" for="login-password">Contraseña</label>
                                        </div>
                                        <input id="password" name="password" type="password"
                                            class="form-control form-control-merge @error('password') is-invalid @enderror"
                                            placeholder="············" aria-describedby="login-password" tabindex="2"
                                            required autocomplete="current-password">
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <button class="btn btn-dark w-100" type="submit" tabindex="4">Ingresar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- GLOBAL SCRIPTS --}}
    @include('layouts.theme.scripts')
</body>

</html>
