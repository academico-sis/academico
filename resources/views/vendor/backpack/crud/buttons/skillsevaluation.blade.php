@if($entry->evaluation_type->contains(2))
	<a href="{{ route('courseSkillsEvaluation', ['course' => $entry->getKey()]) }}" class="btn btn-xs btn-default">
		<i class="fa fa-th"></i>
	</a>
@endif