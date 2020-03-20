@extends('backpack::blank')

@section('header')
<section class="container-fluid">
    <h2>
        @lang('Courses')
    </h2>
</section>
@endsection


@section('content')

<div id="app" class="row">
<div class="col-md-12">
    <course-list-component :defaultperiod="{{ $defaultPeriod }}" :periods="{{ $periods }}" :teachers="{{ $teachers }}" :rhythms="{{ $rhythms }}" :levels="{{ $levels }}" :editable="{{ $isAllowedToEdit }}"></course-list-component>
</div>
</div>

@endsection

@section('before_scripts')
    <script src="/js/app.js"></script>
@endsection