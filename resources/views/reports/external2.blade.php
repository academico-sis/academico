@extends('backpack::blank')

@section('header')
    <section class="container-fluid">
	  <h2>@lang('External Courses Report')</h2>
    </section>
@endsection


@section('content')
    <div class="row">

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <form action="{{route('externalReport2')}}" method="GET">
                        <div class="form-row">
                        <div class="col">
                            <label for="report_start_date">Début : </label>
                            <input class="form-control" type="date" name="report_start_date" value="{{$start}}">
                        </div>

                        <div class="col">
                            <label for="report_end_date">Fin : </label>
                            <input class="form-control" type="date" name="report_end_date" value="{{$end}}">
                        </div>
                        <div class="col align-bottom">
                            <button id="sumbit" style="position: absolute; bottom: 0;" class="btn btn-primary" type="submit">OK</button>
                        </div>
                    </div>
                    </form>
                </div>
                
                <div class="card-body">
                    <table class="table table-striped" style="font-size: 150%;">
                        <thead>
                            <th>@lang('Number of Courses')</th>
                            <th>@lang('Enrollments')</th>
                            <th>@lang('New Students')</th>    
                            <th>@lang('Hours Taught')</th>
                            <th>@lang('Hours Sold')</th>
                        </thead>

                        <tbody>

                                <tr>

                                    <td>
                                        {{ $data['courses'] ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $data['enrollments'] ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $data['students'] ?? '-' }}
                                    </td>
                                    
                                    <td>
                                        {{ number_format($data['taught_hours'] ?? '0', 0, ',', ' ') }}
                                    </td>
                                    
                                    <td>
                                        {{ number_format($data['sold_hours'] ?? '0', 0, ',', ' ') }}
                                    </td>
                                </tr>

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    Détail
                </div>
                
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <th>Cours</th>
                            <th>@lang('Enrollments')</th>
                            <th>@lang('New Students')</th>    
                            <th>@lang('Volume')</th>
                        </thead>

                        <tbody>
                            @foreach($courses as $course)

                                <tr>

                                    <td>
                                        {{ $course->name ?? '-' }} du {{ Carbon\Carbon::parse($course->start_date)->locale(app()->getLocale())->isoFormat('Do MMM YYYY') }} au {{ Carbon\Carbon::parse($course->end_date)->locale(app()->getLocale())->isoFormat('Do MMM YYYY') }}
                                    </td>

                                    <td>
                                        {{ $course->head_count }}
                                    </td>

                                    <td>
                                        {{ $course->new_students }}
                                    </td>
                                    
                                    <td>
                                        {{ $course->volume }} h
                                    </td>
                                    
                                </tr>
                                @endforeach

                        </tbody>
                    </table>
                </div>
            </div>

        </div>


    </div>
@endsection
