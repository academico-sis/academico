@if($entry->trashed())
<form action="{{ url()->previous()}}/{{ $entry->getKey() }}/restore" method="post">
	@csrf
	<button type="submit" class="btn btn-sm btn-success">
		<i class="la la-check"></i> @lang('Enable')
	</button>
</form>
@else
<form action="{{ url()->previous()}}/{{ $entry->getKey() }}/delete" method="post">
	@csrf
	<button type="submit" class="btn btn-sm btn-danger">
		<i class="la la-trash"></i> @lang('Disable')
	</button>
</form>
@endif