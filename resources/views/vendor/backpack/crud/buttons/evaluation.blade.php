@if($entry->evaluation_types->contains(2) && ($entry->course_enrollments_count > 0))
	<a href="{{ route('courseSkillsEvaluation', ['course' => $entry->getKey()]) }}" class="btn btn-sm btn-link">
		<i class="la la-th"></i> {{ __('Evaluate skills') }}
	</a>
@endif

@if($entry->evaluation_types->contains(1))
	<a href="{{ url('/course/'.$entry->getKey()) }}/grades" class="btn btn-sm btn-link">
		<i class="la la-bar-chart"></i> {{ __('Manage grades') }}
	</a>
@endif
