@extends('errors.layout')

@php
  $error_number = 404;
@endphp

@section('title')
  Esta página no existe.
@endsection

@section('description')
  @php
    $default_error_message = "Puede <a href='javascript:history.back()''>regresar a la página anterior</a> o ingresar a la <a href='".url('')."'>página principal</a>.";
  @endphp
  {!! isset($exception)? ($exception->getMessage()?$exception->getMessage():$default_error_message): $default_error_message !!}
@endsection