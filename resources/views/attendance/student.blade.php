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
    
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang('Attendance for') {{ $student->name }}
                </div>
                <div class="box-tools pull-right">
                    <!-- Period selection dropdown -->
                    <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ $selected_period->name }} <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                @foreach ($periods as $period)
                                    <li><a href="{{ url()->current() }}?period={{ $period->id }}">{{ $period->name }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                </div>
            </div>
            
            <div class="box-body" id="app">
                <table class="table">
                    @foreach($absences as $absence)
                    <tr>
                        <td>{{ $absence->event->name }}</td>
                        <td>{{ $absence->event->start }}</td>
                        <td><absence-buttons :attendance="{{ $absence }}" route="{{ route('storeAttendance') }}"></absence-buttons></td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>

</div>

@endsection

@section('after_scripts')
    <script src="/js/app.js"></script>
@endsection
