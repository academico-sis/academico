@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang('coursedetails')
    </h1>
</section>
@endsection


@section('content')

<div class="row">
        <div class="col-md-3">
        
                <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-users"></i></span>
                <div class="info-box-content">
                <span class="info-box-text">Enseignant(e)</span>
                <span class="info-box-number">{{ $course->teacher->name }}</span>
                </div>
                </div>
                </div>
        
                <div class="col-md-3">
                <div class="info-box">
          <span class="info-box-icon bg-aqua"><i class="fa fa-building-o"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Rythme</span>
            <span class="info-box-number">{{ $course->rythm->name . ' ' . $course->level->name  }}</span>
          </div>
        </div>
        </div>
        
                <div class="col-md-3">
                <div class="info-box">
          <span class="info-box-icon bg-aqua"><i class="fa fa-clock-o"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Horaires</span>
            <span class="info-box-number">{{ $course->course_times }}</span>
          </div>
        </div>
        </div>
        
                <div class="col-md-3">
                <div class="info-box">
          <span class="info-box-icon bg-aqua"><i class="fa fa-clock-o"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Salle</span>
            <span class="info-box-number">{{ $course->room->name }}</span>
          </div>
        </div>
        </div>
        
</div>

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang('courses')
                </div>
                <div class="box-tools pull-right">
                    
                    
                </div>
                
            </div>
            
            <div class="box-body">           
                
                <table id="studentsTable" class="table table-striped responsive" style="width:100%">
                    <thead>
                        <tr>
                            <th>@lang('students')</th>
                            <th>@lang('age')</th>
                            <th>@lang('birthdate')</th>
                            <th>@lang('email')</th>
                            <th>@lang('actions')</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @foreach ($students as $student)
                        <tr>
                            <td>{{ $student->student_name }}</td>
                            <td>{{ $student->student_age }}</td>
                            <td>{{ $student->student_birthdate }}</td>
                            <td>{{ $student->student_email }}</td>

                            <td><!-- available actions -->
                                <a href="/student/{{ $student->student_id }}" class='btn btn-secondary'>{{-- todo clean --}}
                                    <i class='fa fa-briefcase'></i>
                                </a>
   
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
 <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.18/b-1.5.4/b-html5-1.5.4/b-print-1.5.4/fh-3.1.4/r-2.2.2/datatables.min.css"/>
 
 <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
 <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
 <script type="text/javascript" src="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.18/b-1.5.4/b-html5-1.5.4/b-print-1.5.4/fh-3.1.4/r-2.2.2/datatables.min.js"></script>
  <script>
    $(document).ready( function () {
        $('#studentsTable').DataTable(
            {
                "scrollX": true,
                paging: false,
                responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.childRow,
                    type: 'inline'
                }
            },
            columnDefs: [

                { responsivePriority: 1, targets: 0 }
            ],

            dom: 'Bfrtip',
            buttons: [
		'copy', 'excel', 'pdf', 'print'
	]

            }
        );
    } );
</script>
@endsection