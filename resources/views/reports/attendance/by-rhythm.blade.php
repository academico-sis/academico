@extends('backpack::blank')

@section('content')

    <div class="row">

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{ __('Attendance report by rhythm') }}

                    <div class="card-header-actions">
                        @include('partials.period_selection', ['allow_year_selection' => true, 'selected_period' => $selected_period])
                    </div>
                </div>

                <div class="card-body">
                    @if ($chartData)
                        <canvas id="myChart"></canvas>
                    @else
                        <p>Aucune donnée pour la période sélectionnée</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('before_scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.2/dist/chart.umd.min.js"></script>
@endsection

@if ($chartData)
    @section('after_scripts')
        <script>
            const ctx = $("#myChart");

            const footer = () => {
                return '% rapporté à la totalité des cours dans le niveau';
            };

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
                            min: 0,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Pourcentage des présences enregistrées dans chaque statut, sur la totalité des séances de cours',
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                footer: footer,
                            }
                        }
                    }
                }
            });


        </script>
    @endsection
@endif
