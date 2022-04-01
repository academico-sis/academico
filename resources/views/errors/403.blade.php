@extends('errors.layout')

@section('title', __('Forbidden'))
@section('code', '403')
@section('message', e($exception->getMessage()))
