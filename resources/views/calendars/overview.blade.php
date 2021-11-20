@extends('backpack::blank')

@section('header')
    <section class="container-fluid">
	  <h2>
        @lang('resource Calendars')
      </h2>
    </section>
@endsection


@section('content')
    <div class="row">

        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                <div class="row">
                    <overview-calendar
                        :leaves="{{ json_encode($leaves ?? null) }}"
                        :events="{{ json_encode($events) }}"
                        :unassigned-events="{{ json_encode($unassigned_events) }}"
                        :resources="{{ json_encode($resources) }}"
                        locale="{{ backpack_user()->locale }}">
                </overview-calendar>
                </div>
            </div>
        </div>

    </div>
@endsection
