@if ($crud->bulk_actions)
	<a href="javascript:void(0)" onclick="bulkAttachEntries(this)" class="btn btn-success bulk-button"><i class="fa fa-check"></i> {{ trans('Attach skills to a course') }}</a>
@endif

@push('after_scripts')
<script>
	if (typeof bulkAttachEntries != 'function') {
	  function bulkAttachEntries(button) {

	      if (typeof crud.checkedItems === 'undefined' || crud.checkedItems.length == 0)
	      {
	      	new PNotify({
	              title: "{{ trans('backpack::crud.bulk_no_entries_selected_title') }}",
	              text: "{{ trans('backpack::crud.bulk_no_entries_selected_message') }}",
	              type: "warning"
	          });

	      	return;
	      }

	      var message = ("{{ trans('Are you ') }}").replace(":number", crud.checkedItems.length);
	      var button = $(this);

	      // show confirm message
	      if (confirm(message) == true) {
	      	  var ajax_calls = [];
      		  var delete_route = "{{ url($crud->route) }}/bulk-delete";

	      	  // submit an AJAX delete call
      		  $.ajax({
	              url: delete_route,
	              type: 'DELETE',
				  data: { entries: crud.checkedItems },
	              success: function(result) {
	                  // Show an alert with the result
	                  new PNotify({
	                      title: ("{{ trans('backpack::crud.bulk_delete_sucess_title') }}"),
	                      text: crud.checkedItems.length+"{{ trans('backpack::crud.bulk_delete_sucess_message') }}",
	                      type: "success"
	                  });

	                  crud.checkedItems = [];
			      	  crud.table.ajax.reload();
	              },
	              error: function(result) {
	                  // Show an alert with the result
	                  new PNotify({
	                      title: "{{ trans('backpack::crud.bulk_delete_error_title') }}",
	                      text: "{{ trans('backpack::crud.bulk_delete_error_message') }}",
	                      type: "warning"
	                  });
	              }
	          });
	      }
      }
	}
</script>
@endpush