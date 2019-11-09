@extends('backpack::blank')


@section('content')

<div class="row">
    
    <div class="col-md-12">
        <div class="card">
                    <div class="card-header">@lang('Attendance')</div>
            <div class="card-body">
                <div id="app">
                <table class="table">
                    <thead>
                        <td></td>
                        @foreach ($events as $event)
                            <td>
                                <a href="{{ route('eventAttendance', ['event' => $event->id]) }}">
                                    {{ Carbon\Carbon::parse($event->start)->toFormattedDateString() }}
                                </a>
                            </td>
                        @endforeach
                    </thead>

                    <tbody>
                        @foreach ($attendances as $attendance)
                            <tr is="course-attendance-component"
                                :student="{{ json_encode($attendance) }}">
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
