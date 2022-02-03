@extends(backpack_view('blank'))

@section('content')

<div class="row" style="align-items: center; text-align: center;">
    <div class="col-sm-6">
        <h2>Bonjour {{ backpack_user()->firstname }}</h2>
    </div>
    <div class="col-sm-6">
        @include('partials.default_periods_info')
    </div>
</div>

@include('reports.insights')

    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="box-body">
                    <div class="chart-wrapper" style="padding: 10px;">
                            <dashboard-calendar
                                :resources="{{ json_encode($resources) }}"
                                :events="{{ json_encode($events) }}"
                                locale="{{ backpack_user()->locale }}"
                                :calendar-start="{{ json_encode(config('app.calendar_start')) }}"
                            ></dashboard-calendar>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
