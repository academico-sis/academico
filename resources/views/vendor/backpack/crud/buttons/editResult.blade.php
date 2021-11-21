@if($entry->course->evaluationType && $entry->course->evaluationType->skills->count() > 0 && $entry->course->course_enrollments_count > 0)
<a href="{{ route('studentSkillsEvaluation', ['enrollment' => $entry->getKey()]) }}" class="btn btn-sm btn-link">
    <i class="la la-th"></i> {{ __('Evaluate skills') }}
</a>
@endif

@if($entry->course->evaluationType && $entry->course->evaluationType->gradeTypes->count() > 0 && $entry->course->course_enrollments_count > 0)
	<a href="{{ url('/course/'.$entry->course->id) }}/grades" class="btn btn-sm btn-link">
		<i class="la la-bar-chart"></i> {{ __('Manage grades') }}
	</a>
@endif
