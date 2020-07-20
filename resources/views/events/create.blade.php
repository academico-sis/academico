@extends('backpack::blank')

@section('content')
<div id="app">
    <event-creation-component :teachers="{{ $teachers }}" :rooms="{{ $rooms }}"></event-creation-component>
</div>
@endsection

@section('after_scripts')
<script src="/js/app.js"></script>
@endsection
