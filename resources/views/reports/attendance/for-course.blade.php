@extends('backpack::blank')

@section('content')

    <div class="row">

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    @if ($course)
                        {{ __('Attendance report for course') }} {{ $course->name }}

                        @if ($course->period?->name)
                            - {{ __('Period') }} {{ $course->period->name }}
                        @endif

                        @if ($course->teacher?->name)
                            - {{ __('Teacher') }} {{ $course->teacher->name }}
                        @endif
                    @endif

                    <div class="card-header-actions">
                        @include('partials.period_selection', ['allow_year_selection' => false, 'selected_period' => $selected_period])
                        @include('partials.course_selection', ['courses' => $otherCourses])
                    </div>
                </div>

                <div class="card-body">
                    @if ($chartData)
                        <canvas id="myChart"></canvas>
                        @else
                        <p>Choisissez le cours que vous souhaitez visualiser</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('before_scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.2/dist/chart.umd.min.js"></script>
@endsection

@if ($course)
@section('after_scripts')
    <script>
        const ctx = $("#myChart");

        new Chart(ctx, {
            type: 'bar',
            data: @json($chartData),
            options: {
                legend: { display: true },
                responsive: true,
                scales: {
                    x: {
                        stacked: true,
                    },
                    y: {
                        stacked: true,
                        title: {
                            display: true,
                            text: 'Nombre d\'inscrits dans chaque statut',
                        }
                    }
                }
            }
        });


    </script>
@endsection
@endif
