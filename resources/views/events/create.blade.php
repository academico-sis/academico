@extends('backpack::blank')

@section('content')
<div id="app">
    <event-creation-component :teachers="{{ $teachers }}" :rooms="{{ $rooms }}"></event-creation-component>
</div>
@endsection

@section('after_scripts')
    <script src="{{ mix('/js/app.js') }}"></script>
    <script src="{{ mix('/js/manifest.js') }}"></script>
    <script src="{{ mix('/js/vendor.js') }}"></script>
@endsection
