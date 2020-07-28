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
        var idc = {{ $course_id }};
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