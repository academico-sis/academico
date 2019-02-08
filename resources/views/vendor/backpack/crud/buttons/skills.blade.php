@if($entry->evaluation_type->contains(2))
	<a href="courseskill?course={{$entry->getKey()}}" class="btn btn-xs btn-default">
		<i class="fa fa-list"></i>
	</a>
@endif