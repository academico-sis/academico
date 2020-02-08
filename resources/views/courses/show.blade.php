@extends('backpack::blank')

@section('header')
<section class="container-fluid">
    <h2>
        @lang('Course Details')
    </h2>
</section>
@endsection


@section('content')

<div class="row">

    @if(isset($course->teacher))
    <div class="col-md-3">
        <div class="card">
            <div class="card-body p-3 d-flex align-items-center"><i class="fa fa-user bg-primary p-3 font-2xl mr-3"></i>
                <div>
                    <div class="text-value-sm text-primary">{{ $course->teacher->name }}</div>
                    <div class="text-muted text-uppercase font-weight-bold small">@lang('Teacher')</div>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    @if(isset($course->rhythm) || isset($course->level))
    <div class="col-md-3">
        <div class="card">
            <div class="card-body p-3 d-flex align-items-center"><i class="fa fa-building-o bg-primary p-3 font-2xl mr-3"></i>
                <div>
                    <div class="text-value-sm text-primary">{{ $course->name ?? '' }}</div>
                    <div class="text-muted text-uppercase font-weight-bold small">{{ $course->rhythm->name ?? '-' . ' ' . $course->level->name ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    @if(isset($course->course_times))
    <div class="col-md-3">
        <div class="card">
            <div class="card-body p-3 d-flex align-items-center"><i class="fa fa-clock-o bg-primary p-3 font-2xl mr-3"></i>
                <div>
                    <div class="text-value-sm text-primary">{{ $course->course_times }}</div>
                    <div class="text-muted text-uppercase font-weight-bold small">@lang('Schedule')</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(isset($course->room))
    <div class="col-md-3">
        <div class="card">
            <div class="card-body p-3 d-flex align-items-center"><i class="fa fa-clock-o bg-primary p-3 font-2xl mr-3"></i>
                <div>
                    <div class="text-value-sm text-primary">{{ $course->room->name }}</div>
                    <div class="text-muted text-uppercase font-weight-bold small">@lang('Room')</div>
                </div>
            </div>
        </div>
    </div>
    @endif
        
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-header-actions">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#myModal">@lang('Enroll new student')</button>
                </div>
            </div>
            
            <div class="card-body">           
                
                <table id="studentsTable" class="table table-striped responsive" style="width:100%">
                    <thead>
                        <tr>
                            <th>@lang('students')</th>
                            <th>@lang('age')</th>
                            <th>@lang('birthdate')</th>
                            <th>@lang('email')</th>
                            <th>@lang('Phone Number')</th>
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
                            <td>@foreach ($enrollment->student->phone as $phone) {{ $phone->phone_number }} - @endforeach</td>

                            <td><!-- available actions -->
                                <a href="/student/{{ $enrollment->student_id }}/show" class='btn btn-sm btn-secondary'>
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


@section('after_scripts')
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
  
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-html5-1.6.1/b-print-1.6.1/fh-3.1.6/datatables.min.css"/>
 
<!-- DATA TABLES -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-html5-1.6.1/b-print-1.6.1/fh-3.1.6/datatables.min.js"></script>

<script>
    $(document).ready( function () {
        $('#studentsTable').DataTable(
            {
                "scrollX": true,
                paging: false,
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