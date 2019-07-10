@extends('backpack::layout')

@section('header')
    <section class="content-header">
      <h1>@lang('External Courses Report')</h1>
      <ol class="breadcrumb">
        <li><a href="{{ backpack_url() }}">{{ config('backpack.base.project_name') }}</a></li>
        <li class="active">@lang('External Courses Report')</li>
      </ol>
    </section>
@endsection


@section('content')
    <div class="row">



    <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">
                    </div>
                    <div class="box-tools pull-right">

                    </div>
                </div>
                
                <div class="box-body">
                    <!-- <canvas id="myChart"></canvas> -->
                </div>
            </div>
        </div>


        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">
                    </div>
                    <div class="box-tools pull-right">
                    <span>@lang('Start from period:')</span>
                    <!-- Period selection dropdown -->
                    <div class="btn-group">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ $startperiod->name }} <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            @foreach ($periods as $period)
                            <li><a href="{{ url()->current() }}/?startperiod={{ $period->id }}">{{ $period->name }}</a></li>
                            @endforeach
                        </ul>
                    </div> 
                    </div>
                </div>
                
                <div class="box-body">
                    <table class="table table-striped">
                        <thead>
                            <th>@lang('Period')</th>
                            <th>@lang('Number of Courses')</th>
                            <th>@lang('Enrollments')</th>
                            <th>@lang('New Students')</th>    
                            <th>@lang('Hours Taught')</th>
                            <th>@lang('Hours Sold')</th>
                        </thead>

                        @php
                            $current_year = $startperiod->year_id;
                            $i = 0;
                            $year_enrollments = 0;
                            $year_taught_hours = 0;
                            $year_sold_hours = 0;
                            $year_students = 0;
                            $year_courses = 0;
                        @endphp

                        <tbody>
                            @foreach($data as $data_period)

                            {{-- if we are starting a new year, display previous year's totals first --}}
                            @if ($current_year != $data_period['year_id'])

                                <tr style="font-weight: bold">
                                    <td>
                                        {{ \App\Models\Year::find($current_year)->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $year_courses ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $year_enrollments ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $year_students ?? '-' }}
                                    </td>
                                    
                                    <td>
                                        {{ number_format($year_taught_hours ?? '0') }}
                                    </td>
                                    
                                    <td>
                                        {{ number_format($year_sold_hours ?? '0') }}
                                    </td>
                                </tr>

                                @php
                                    $i ++;
                                    $year_enrollments = 0;
                                    $year_students = 0;
                                    $year_courses = 0;
                                    $year_taught_hours = 0;
                                    $year_sold_hours = 0;
                                @endphp
                                
                            @endif

                            @php
                                $year_courses += $data_period['courses'];
                                $year_enrollments += $data_period['enrollments'];
                                $year_students += $data_period['students'];
                                $year_taught_hours += $data_period['taught_hours'];
                                $year_sold_hours += $data_period['sold_hours'];
                            @endphp

                                <tr>
                                    <td>
                                        {{ $data_period['period'] ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $data_period['courses'] ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $data_period['enrollments'] ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $data_period['students'] ?? '-' }}
                                    </td>
                                    
                                    <td>
                                        {{ number_format($data_period['taught_hours'] ?? '0') }}
                                    </td>
                                    
                                    <td>
                                        {{ number_format($data_period['sold_hours'] ?? '0') }}
                                    </td>
                                </tr>

                                @php
                                    $current_year = $data_period['year_id'];
                                    $i ++;
                                @endphp
                            @endforeach

                            <tr style="font-weight: bold">
                                    <td>
                                        {{ \App\Models\Year::find($current_year)->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $year_courses ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $year_enrollments ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $year_students ?? '-' }}
                                    </td>
                                    
                                    <td>
                                        {{ number_format($year_taught_hours ?? '0') }}
                                    </td>
                                    
                                    <td>
                                        {{ number_format($year_sold_hours ?? '0') }}
                                    </td>
                                </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


    </div>
@endsection


@section('before_scripts')
   
@endsection


@section('after_scripts')

@endsection