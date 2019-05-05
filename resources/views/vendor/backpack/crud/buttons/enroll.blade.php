@if($entry->opened == 1 && $entry->course_enrollments_count < $entry->spots)

<form method="POST" action="{{ route('storeEnrollment') }}">
    <input type="hidden" name="student_id" value="{{ $crud->student->user->id }}">
    <input type="hidden" name="course_id" value="{{ $entry->getKey() }}">
    @csrf
    <button type="submit" class="btn btn-xs btn-primary"><i class="fa fa-user-plus"></i></button>
</form>

@endif