@extends('backpack::blank')

@section('header')
<section class="container-fluid">
    <h2>
        @lang('My Hours')
    </h2>
</section>
@endsection


@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                    {{ $teacher->name }}
                <div class="card-header-actions">
                    <!-- Period selection dropdown -->
                    @include('partials.period_selection')
                </div>
            </div>

            <div class="card-body">

                <table class="table table-striped responsive">
                    <thead>
                        <tr>
                            <th><strong>@lang('Period Total')</strong></th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td><strong>{{ number_format($teacher->plannedHoursInPeriod($selected_period->start, $selected_period->end), 2, '.', ',') }} h</strong></td>
                        </tr>
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>


<div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    @lang('Period Classes')
                    <div class="card-header-actions">
                        <!-- Period selection dropdown -->
                        @include('partials.period_selection')
                    </div>
                </div>

                <div class="card-body">

                    <table class="table table-striped responsive">
                        <thead>
                            <tr>
                                <th>@lang('Date')</th>
                                <th>@lang('Course')</th>
                                <th>@lang('Start')</th>
                                <th>@lang('End')</th>
                                <th>@lang('Length')</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($events as $event)
                            <tr>
                                <td>{{ $event->formattedDate }}</td>
                                <td>{{ $event->name }}</td>
                                <td>{{ $event->startTime }}</td>
                                <td>{{ $event->endTime }}</td>
                                <td>{{ $event->eventLength }}h</td>
                            </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
        </div>
@endsection
