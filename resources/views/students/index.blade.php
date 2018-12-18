@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        {{ ucfirst(trans_choice('academico.students', 1)) }}
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                        {{ ucfirst(trans_choice('academico.students', 1)) }}
                </div>
                <div class="box-tools pull-right">
                    
                </div>
                
            </div>
            
            <div class="box-body">           
                
                <table id="studentsTable" class="table table-striped responsive" style="width:100%">
                    <thead>
                        <tr>
                            <th>{{ trans_choice('academico.students', 1) }}</th>
                            <th>{{ trans_choice('academico.actions', 1) }}</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @foreach ($students as $student)
                        <tr>

                            <td>{{ $student->name }}</td>

                            <td><!-- course available actions -->

                            <!-- student details -->
                            <a type="button" class="btn btn-xs btn-secondary" href="">
                                <i class="fa fa-user"></i>
                            </a>

                            <!-- todo enroll student -->    
   
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- todo refactor this --}}
@section('after_scripts')
 <!-- DATA TABLES -->
 <link href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />
 <link href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css" rel="stylesheet" type="text/css" />

 
 <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
 
 <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>

  <script>
    $(document).ready( function () {
        $('#coursesTable').DataTable(
            {
                "scrollX": true,
                paging: false,
                responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.childRow,
                    type: 'column'
                }
            },
            columnDefs: [
                {
                className: 'control',
                orderable: false,
                targets: 0
                },
                { responsivePriority: 5, targets: 3 },
                { responsivePriority: 6, targets: 5 },
                { responsivePriority: 11000, targets: 4 },
                { responsivePriority: 6, targets: -1 }
            ]
            }
        );
    } );
</script>
@endsection