@if($entry->evaluationType && $entry->evaluationType->skills->count() > 0 && $entry->course_enrollments_count > 0)
	<a href="{{ route('courseSkillsEvaluation', ['course' => $entry->getKey()]) }}" class="btn btn-sm btn-link">
		<i class="la la-th"></i> {{ __('Evaluate skills') }}
	</a>
@endif

@if($entry->evaluationType && $entry->evaluationType->gradeTypes->count() > 0 && $entry->course_enrollments_count > 0)
	<a href="{{ url('/course/'.$entry->getKey()) }}/grades" class="btn btn-sm btn-link">
		<i class="la la-bar-chart"></i> {{ __('Manage grades') }}
	</a>
@endif
