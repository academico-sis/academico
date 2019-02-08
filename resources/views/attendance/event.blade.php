@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang('Attendance')
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    
    <div class="col-lg-8 col-lg-offset-2">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang('Attendance')
                </div>
                <div class="box-tools pull-right">
                </div>
            </div>
            
            <div class="box-body">
                <div id="app">
                <table class="table table-striped">
                    <thead>
                        <td>Student</td>
                        <td></td>
                    </thead>

                    <tbody>
                        @foreach ($attendances as $attendance)
                            <tr is="event-attendance-component"
                                :attendance="{{ json_encode($attendance) }}"
                                :event="{{ json_encode($event) }}"
                                route="{{ route('storeAttendance') }}">
                            </tr>
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
