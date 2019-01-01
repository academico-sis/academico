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
                            <th>{{ ucfirst(trans_choice('academico.idnumber', 1)) }}</th>
                            <th>{{ ucfirst(trans_choice('academico.firstname', 1)) }}</th>
                            <th>{{ ucfirst(trans_choice('academico.lastname', 1)) }}</th>
                            <th>{{ ucfirst(trans_choice('academico.email', 1)) }}</th>
                            <th class="hidden-xs hidden-sm">Opciones</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>{{ ucfirst(trans_choice('academico.idnumber', 1)) }}</th>
                            <th>{{ ucfirst(trans_choice('academico.firstname', 1)) }}</th>
                            <th>{{ ucfirst(trans_choice('academico.lastname', 1)) }}</th>
                            <th>{{ ucfirst(trans_choice('academico.email', 1)) }}</th>
                            <th class="hidden-xs hidden-sm">Opciones</th>
                        </tr>
                    </tfoot>
                    
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
    $(document).ready(function() {
        var table = $('#studentsTable').DataTable( {

            ajax: {
        url: '/students/get',
        dataSrc: ''
    },

    fixedHeader: true,
    
    columns: [
        { data: 'idnumber' },
        { data: 'firstname' },
        { data: 'lastname' },
        { data: 'email' },
        {
            "targets": -1,
            "data": 'id',
            "render": function ( data) {
                return `
                <a href='{{url("students") }}/${data}' class='btn btn-secondary'>
                    <i class='fa fa-briefcase'></i>
                </a>

                <a href="students/${data}/enroll" class='btn btn-secondary'>
                    <i class='fa fa-user-plus'></i>
                </a>
            `;

            }
        } ],
});
});

    </script>
@endsection