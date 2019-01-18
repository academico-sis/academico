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
                            <li>{{ $event['event_date'] }}</li>
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
                        <strong>@lang('Enrollments')</strong>
                    </div>
                    <div class="box-tools pull-right">

                    </div>
                </div>

                <div class="box-body">
                    {{ $enrollment_count }}
                </div>
            </div>
        </div>

    </div>
@endsection
