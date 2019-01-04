@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        {{ ucfirst(trans_choice('academico.attendance', 1)) }}
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    {{ ucfirst(trans_choice('academico.attendance', 1)) }}
                </div>
                <div class="box-tools pull-right">
                </div>
            </div>
            
            <div class="box-body">
                <div id="app">
                <table class="table">
                    <thead>
                        <td>Student</td>
                        <td></td>
                    </thead>

                    <tbody>
                        @foreach ($attendances as $attendance)
                        <student-attendance-component
                            :attendance="{{ json_encode($attendance) }}">
                        </student-attendance-component>
                        @endforeach
                        
                    </tbody>
                </table>
            </div>
            </div>
        </div>
    </div>


</div>

@endsection



@section('after_scripts')
    <script src="/js/app.js"></script>
@endsection
