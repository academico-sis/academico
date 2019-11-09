@extends('backpack::blank')

@section('header')
<section class="container-fluid">
    <h2>
    @lang('Attendance for') {{ $student->name }}
    </h2>
</section>
@endsection


@section('content')

<div class="row">
    
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-header-actions">
                    <!-- Period selection dropdown -->
                    @include('partials.period_selection')
                </div>
            </div><!-- /.card-header -->
            
            <div class="card-body" id="app">
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
