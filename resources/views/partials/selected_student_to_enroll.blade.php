<div class="row">
<div class="col-md-4">
  <div class="card">
    <div class="card-header">
      @lang('Student')
    </div>
    <div class="card-body">
      {{ $crud->student->name }}
    </div>
  </div>
</div>

@if($crud->student->enrollments->count() > 0)
<div class="col-md-4">
    <div class="card">
      <div class="card-header">
        @lang('Last Enrollment')
      </div>
      <div class="card-body">
          {{ $crud->student->enrollments->last()->course->period->name }} : {{ $crud->student->enrollments->last()->course->name }}
        @if(isset($crud->student->enrollments->last()->result))
          - <label class="label-info">{{ $crud->student->enrollments->last()->result->result_name->name }}</label>
        @endif
      </div>
    </div>
  </div>
@endif

  @if($crud->student->comments->count() > 0)
  <div class="col-md-4">
      <div class="card">
        <div class="card-header">
          @lang('Comments')
        </div>
        <div class="card-body">
          @foreach($crud->student->comments as $comment)
            <p>{{ $comment->body }} ({{ $comment->date }})</p>
          @endforeach
        </div>
      </div>
    </div>
@endif

</div>