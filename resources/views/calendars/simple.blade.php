@extends('backpack::blank')

@section('header')
    <section class="container-fluid">
	  <h2>
        @lang('Calendar for') {{ $resource->name ?? '-' }}
      </h2>
    </section>
@endsection


@section('content')
    <div class="row">

        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <simple-calendar
                        :leaves="{{ json_encode($leaves ?? null) }}"
                        :events="{{ json_encode($events) }}"
                        locale="{{ backpack_user()->locale }}">
                    </simple-calendar>
                </div>
            </div>
        </div>

    </div>
@endsection