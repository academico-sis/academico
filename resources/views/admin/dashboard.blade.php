@extends('backpack::layout')

@section('header')
    <section class="content-header">
      <h1>
        {{ trans('backpack::base.dashboard') }}
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ backpack_url() }}">{{ config('backpack.base.project_name') }}</a></li>
        <li class="active">{{ trans('backpack::base.dashboard') }}</li>
      </ol>
    </section>
@endsection


@section('content')

@include('reports.insights')


    <div class="row">

        @if(isset($pending_attendance))
        <div class="col-md-4">
                <div class="box">
                    <div class="box-header with-border">
                        <div class="box-title">
                            <strong>
                                @lang('Pending Attendance')
                            </strong>
                        </div>
                        <div class="box-tools pull-right">
                        
                        </div>
                    </div>
    
                    <div class="box-body">
                        <ul>
                            @foreach($pending_attendance as $event)
                            <li>{{ $event['event'] }} ({{ $event['event_date'] }})</li>
                            @endforeach
                        </ul>
    
                    </div>
                </div>
            </div>
        @endif

        <div class="col-md-4">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">                          
                        <strong>@lang('Upcoming classes with no teacher assigned')</strong>
                    </div>
                    <div class="box-tools pull-right">

                    </div>
                </div>

                <div class="box-body">
                    <ul>
                        @foreach ($unassigned_teacher as $event)
                            <li>{{ $event->name }} ({{ $event->start }})</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>


        <div class="col-md-4">
                <div class="box">
                    <div class="box-header with-border">
                        <div class="box-title">                          
                            <strong>@lang('Human Resources')</strong>
                        </div>
                        <div class="box-tools pull-right">
    
                        </div>
                    </div>
    
                    <div class="box-body">
                        <p>@lang('Teachers') : {{ count($teachers) }}</p>
{{--                         <p>@lang('Upcoming Leaves') : <ul>
                            @foreach ($upcoming_leaves as $leave)
                                {{ $leave }}
                            @endforeach
                        </ul> --}}

                        {{-- nombre d'heures de cours aujourd'hui, cette semaine, ce mois, ce cycle --}}
                        {{-- comparaison par rapport au cycle dernier --}}
                    </div>
                </div>
            </div>

    </div>
@endsection

