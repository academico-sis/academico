@extends('backpack::blank')

@section('content')
<div>
    <event-creation-component :teachers="{{ $teachers }}" :rooms="{{ $rooms }}"></event-creation-component>
</div>
@endsection