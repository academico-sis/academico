@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang('attendance')
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    
    <div class="col-md-6">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang('pending_attendance')
                </div>
                <div class="box-tools pull-right">
                </div>
            </div>
            
            <div class="box-body">
                <table class="table">
                    @foreach($pending_attendance as $event)
                    <tr>
                        <td>{{ $event['event'] }}</td>
                        <td>{{ $event['event_date'] }}</td>
                        <td>{{ $event['teacher'] }}</td>
                        <td>{{ $event['pending'] }}</td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang('absences')
                </div>
                <div class="box-tools pull-right">
                </div>
            </div>
            
            <div class="box-body">
                <table class="table">
                    @foreach($absences as $absence)
                    <tr>
                        <td>{{ $absence->firstname }} {{ $absence->lastname }}</td>
                        <td>{{ $absence->course_name }}</td>
                        <td>{{ $absence->count }}</td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>


</div>

@endsection



@section('after_scripts')
@endsection
