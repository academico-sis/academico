@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang_u('academico.course_creation')
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang_u('academico.course_creation')
                </div>
                <div class="box-tools pull-right">
                    

                    
                </div>  
            </div>
            
            <div class="box-body">           
                <div id="app">

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('after_scripts')
<script src="/js/app.js"></script>
@endsection
