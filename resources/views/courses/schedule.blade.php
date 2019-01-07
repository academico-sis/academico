@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang_u('academico.course_schedule')
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    <div class="col-md-12">
        <div id="app">
            <course-time-component></course-time-component>
        </div>
    </div>
</div>

@endsection

@section('after_scripts')
    <script src="/js/app.js"></script>
@endsection
