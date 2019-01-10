@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang_u('academico.attendance')
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    
    <div class="col-md-6">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang_u('academico.pending_attendance')
                </div>
                <div class="box-tools pull-right">
                </div>
            </div>
            
            <div class="box-body">
                <table class="table">

                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang_u('academico.absences')
                </div>
                <div class="box-tools pull-right">
                </div>
            </div>
            
            <div class="box-body">
                
            </div>
        </div>
    </div>


</div>

@endsection



@section('after_scripts')
@endsection
