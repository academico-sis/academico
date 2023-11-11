@extends('backpack::blank')

@section('header')
    <section class="container-fluid">
        <h2>
            {{ __('Students by age') }}
        </h2>
    </section>
@endsection

@section('content')
    <div class="row">



        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card">


                <div class="card-body">
                    <table id ="reportsTable" class="table table-striped">
                        <thead>


                            @foreach ($ages as $key => $value)
                                <th>{{ $key }} years</th>
                            @endforeach


                        </thead>

                        <tbody>

                            <tr>


                                @foreach ($ages as $key => $value)
                                    @php
                                        $count_of_ages_in_range = 0;

                                        for ($i = 0; $i < sizeof($ages_of_all_students); $i++) {
                                            if ($ages_of_all_students[$i] >= $value[0] && $ages_of_all_students[$i] <= $value[1]) {
                                                $count_of_ages_in_range++;
                                            }
                                        }
                                    @endphp
                                    <td>{{ $count_of_ages_in_range }}</td>
                                @endforeach

                            </tr>


                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection


@section('before_scripts')
@endsection


@section('after_scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            var _labels = @json($labels);
            var _data = @json($data);

            const ctx = document.getElementById('myChart');
            const myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: _labels,
                    datasets: [{
                        label: 'Students # in ages',
                        data: _data,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                }
            });
        }, true);
    </script>



    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-html5-1.6.1/b-print-1.6.1/datatables.min.css" />

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript"
        src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-html5-1.6.1/b-print-1.6.1/datatables.min.js">
    </script>
    <script>
        $(document).ready(() =>
            $('#reportsTable').DataTable({
                dom: 'Bfrtip',
                "paging": false,
                "searching": false,
                "ordering": false,
                buttons: [
                    'excel',
                    'pdf',
                    'print'
                ]
            }));
    </script>
@endsection
