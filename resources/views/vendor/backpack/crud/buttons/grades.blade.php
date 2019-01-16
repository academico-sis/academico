@if($entry->evaluation_type->contains(1))
	<a href="{{ url('/course/'.$entry->getKey()) }}/grades" class="btn btn-xs btn-default">
		<i class="fa fa-percent"></i>
	</a>
@endif