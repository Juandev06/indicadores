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
<body class="vertical-layout vertical-menu-modern navbar-floating footer-static" data-open="click" 
    data-menu="vertical-menu-modern" data-col="">
    @include('layouts.theme.sidebar')
    @yield('content')
    @include('layouts.theme.footer')
    {{-- GLOBAL SCRIPTS --}}
    @include('layouts.theme.scripts')
    @yield('jsAdicional')
    @livewireScripts
</body>
</html>