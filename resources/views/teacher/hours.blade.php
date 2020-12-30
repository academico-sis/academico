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
                            <th>@lang('Remote Work')</th>
                            <th>@lang('Planned Hours')</th>
                            <th>@lang('Period Max')</th>
                            <th><strong>@lang('Period Total')</strong></th>
                            <th>@lang('Worked Hours')</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $max_hours = $teacher->period_max_hours($selected_period);
                            $period_hours = $teacher->period_planned_hours($selected_period);
                            $remote_hours = $teacher->periodRemoteHours($selected_period)
                        @endphp
                        <tr>
                            <td>{{ number_format($remote_hours, 2, '.', ',') }} h</td>
                            <td>{{ number_format($period_hours, 2, '.', ',') }} h</td>
                            <td>{{ number_format($max_hours, 2, '.', ',') }} h</td>
                            <td><strong>{{ number_format($period_hours + $remote_hours, 2, '.', ',') }} h</strong></td>
                            <td>{{ number_format($teacher->period_worked_hours($selected_period), 2, '.', ',') }} h</td>
                        </tr>
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>


<div class="row">
        <div class="col-md-7">
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

            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                            @lang('Remote Work')
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
                                    <th>@lang('Project')</th>
                                    <th>@lang('Length')</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($remote_events as $event)
                                <tr>
                                    <td>{{ $event->formattedDate }}</td>
                                    <td>{{ $event->name }}</td>
                                    <td>{{ $event->worked_hours }}h</td>
                                </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
@endsection
