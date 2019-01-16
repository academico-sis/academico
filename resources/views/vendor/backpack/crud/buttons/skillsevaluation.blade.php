@if($entry->evaluation_type->contains(2))
	<a href="{{ url('/course/'.$entry->getKey()) }}/skillsevaluation" class="btn btn-xs btn-default">
		<i class="fa fa-th"></i>
	</a>
@endif