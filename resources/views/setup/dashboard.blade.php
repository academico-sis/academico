@extends('backpack::blank')

@section('header')
    <section class="container-fluid">
	  <h2>
        {{ trans('backpack::base.dashboard') }}
      </h2>
    </section>
@endsection


@section('content')
    <div class="row">

        <div class="col-md-4">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">                          
                        <strong>@lang('Scheduled Tasks')</strong>
                    </div>
                    <div class="box-tools pull-right">

                    </div>
                </div>

                <div class="box-body">
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">                          
                        <strong>@lang('Jobs Queue')</strong>
                    </div>
                    <div class="box-tools pull-right">

                    </div>
                </div>

                <div class="box-body">
                    <ul>
                        <li>Number of jobs in queue (should be 0) : {{ $queue }}</li>
                        <li>Number of failed jobs : {{ $failed }}</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">                          
                        <strong>@lang('Default Period')</strong>
                    </div>
                    <div class="box-tools pull-right">

                    </div>
                </div>

                <div class="box-body">
                    <p>Current period is {{ \App\Models\Period::get_default_period()->name }}</p>
                    <p>Default enrollment period is {{ \App\Models\Period::get_enrollments_period()->name }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">                          
                        <strong>@lang('Leads Status')</strong>
                    </div>
                    <div class="box-tools pull-right">
                        <form action="{{ route('resetAllConvertedLeads') }}" method="post" onsubmit="return confirm('Are you sure? There is no going back!');">
                            @csrf
                            <input type="submit" class="btn btn-small" value="@lang('Change all Converted to Pending')">
                        </form>
                    </div>
                </div>

                <div class="box-body">
                    <ul>
                        @foreach ($lead_types as $lead_type)
                            <li>{{ $lead_type->name }} : {{ $lead_type->students_count }}</li>
                        @endforeach
                            <li>@lang('Students with no leadStatus') : {{ $orphan_students }}</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
@endsection

