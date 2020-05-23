@extends('backpack::blank')


@section('content')

<div class="row">
    
    <div class="col-lg-8 col-lg-offset-2">
        <div class="card">
            <div class="card-header">
                Présences pour la classe du {{ Carbon\Carbon::parse($event->start)->locale(app()->getLocale())->isoFormat('Do MMM YYYY') }}
                <div class="card-header-actions">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#myModal">@lang('Enroll new student')</button>
                    <a href="/attendance/course/{{ $event->course_id }}">@lang('Back to course')</a>
                </div>
            </div><!-- /.card-header -->
            
            <div class="card-body">
                <div id="app">
                <table class="table table-striped">
                    <thead>
                        <td>Student</td>
                        <td></td>
                    </thead>

                    <tbody>
                        @foreach ($attendances as $attendance)
                            <tr is="event-attendance-component"
                                :attendance="{{ json_encode($attendance) }}"
                                :event="{{ json_encode($event) }}"
                                route="{{ route('storeAttendance') }}">
                            </tr>
                        @endforeach
                        
                    </tbody>
                </table>
                </div>
            </div>
            <div class="card-footer">
            <label class="badge badge-success">
                P <i class="la la-user"></i>
            </label> : @lang('Present')
            - 
            <label class="badge badge-warning">
                PP <i class="la la-clock-o"></i>
            </label> : @lang('Partial presence (arrived late or left early)')
            - 
            <label class="badge badge-info">
                AJ <i class="la la-exclamation"></i>
            </label> : @lang('justified absence')
            - 
            <label class="badge badge-danger">
                A <i class="la la-user-times"></i>
            </label> : @lang('unjustified absence')
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
    <script src="/js/app.js"></script>

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
        var idc = {{ $event->course->parent }};
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
