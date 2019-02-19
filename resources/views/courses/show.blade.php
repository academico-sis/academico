@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang('Course Details')
    </h1>
</section>
@endsection


@section('content')

<div class="row">

    @if(isset($course->teacher))
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">@lang('Teacher')</span>
                    <span class="info-box-number">{{ $course->teacher->name }}</span>
                </div>
            </div>
        </div>
    @endif
    
    @if(isset($course->rhythm) || isset($course->level))
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-building-o"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">@lang('Rhythm')/@lang('level')</span>
                    <span class="info-box-number">{{ $course->rhythm->name ?? '-' . ' ' . $course->level->name ?? '-' }}</span>
                </div>
            </div>
        </div>
    @endif
    
    @if(isset($course->course_times))
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-clock-o"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">@lang('Schedule')</span>
                    <span class="info-box-number">{{ $course->course_times }}</span>
                </div>
            </div>
        </div>
    @endif

    @if(isset($course->room))
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-clock-o"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">@lang('Room')</span>
                    <span class="info-box-number">{{ $course->room->name }}</span>
                </div>
            </div>
        </div>
    @endif
        
</div>

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang('courses')
                </div>
                <div class="box-tools pull-right">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#myModal">@lang('Enroll new student')</button>
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
                        @foreach ($enrollments as $enrollment)
                        <tr>
                            <td>{{ $enrollment->student->name }}</td>
                            <td>{{ $enrollment->student->student_age }}</td>
                            <td>{{ $enrollment->student->student_birthdate }}</td>
                            <td>{{ $enrollment->student->email }}</td>

                            <td><!-- available actions -->
                                <a href="{{ url('student', $enrollment->student_id) }}" class='btn btn-secondary'>
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


<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
          <h4 class="modal-title" id="myModalLabel">@lang('Enroll new student')</h4>
        </div>
  
        <div class="modal-body">
              <select required name="student" id="student" class="select2"></select>
          </div>
      
      <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">@lang('Close')</button>
          <button type="button" class="btn btn-primary" onclick="enroll()">@lang('Enroll')</button>
  
      </div>
      </div>
    </div>
  </div>


@endsection


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
                buttons: ['copy', 'excel', 'pdf', 'print']
            }
        );
    });
</script>


<script>
    
    $('#myModal').on('show.bs.modal', function(e) {
        $('.select2').select2({
            width: '100%', // fill the modal body
            
            ajax: {
                
                url: '/searchstudents',
                dataType: 'json', 
                delay: 250,
                
                processResults: function (data) {
                    return {
                        results:  $.map(data, function (item) { 
                            return {  
                                text: item.firstname + ' ' + item.lastname,
                                id: item.id
                            }
                        })
                    };

                },
                
                cache: true
                
            },
            minimumInputLength: 2 // wait for 2+ characters to make the request
        });
    });
    
    function enroll()
    {
        var idc = {{ $course->parent }};
        var student = document.getElementById("student").value;
    
    $.ajax({
            url: "/student/enroll",
            type: "POST",
            data: {
                course_id: idc,
                student_id: student
                },
    
            success: function() {   
                location.reload();  
            }
        })
        
        }
    </script>

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
@endsection