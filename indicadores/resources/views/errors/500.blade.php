@extends('errors::minimal')

@section('title', __('Error de servidor'))
@section('code', '500')
@section('message', __('Error de servidor'))
@section('error-desc')
<hr class="border-t dark:border-gray-700">
Por favor notifique al administrador de la aplicación con el siguiente mensaje de error: <br>
<div class="dark:bg-gray-800 p-2 mt-2">
    {{ $exception->getMessage() }}
</div>
@endsection
