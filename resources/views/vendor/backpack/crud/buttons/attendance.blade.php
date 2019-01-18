@if($entry->events->count() > 0 && $entry->exempt_attendance !== 1)
	<a href="{{ url('/attendance/course/'.$entry->getKey()) }}" class="btn btn-xs btn-default">
		<i class="fa fa-calendar"></i>
	</a>
@endif