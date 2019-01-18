@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang('Course Schedule')
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    <div class="col-md-12">
        <div id="app">
            <course-time-component
            :course="{{ json_encode($course->id) }}"></course-time-component>
        </div>
    </div>
</div>

@endsection

@section('after_scripts')
    <script src="/js/app.js"></script>
@endsection
