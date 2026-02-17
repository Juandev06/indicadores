@extends('errors::minimal')

@section('title', 'Error: no tiene permiso')
@section('code', '403')
@section('message', $exception->getMessage() ?: 'No tiene permisos para este módulo')
