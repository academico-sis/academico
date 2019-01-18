@if($entry->evaluation_type->contains(2))
	<a href="{{ url('/course/'.$entry->getKey()) }}/skills" class="btn btn-xs btn-default">
		<i class="fa fa-list"></i>
	</a>
@endif