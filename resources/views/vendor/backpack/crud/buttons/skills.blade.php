@if($entry->evaluation_type->contains(2))
	<a href="{{ route('course-skills', ['course' => $entry->getKey()]) }}" class="btn btn-sm btn-link">
		<i class="fa fa-list"></i>
	</a>
@endif