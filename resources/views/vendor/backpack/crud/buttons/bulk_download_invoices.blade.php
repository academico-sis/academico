@if ($crud->hasAccess('create') && $crud->get('list.bulkActions'))
	<a href="javascript:void(0)" onclick="bulkDownloadEntries(this)" class="btn btn-sm btn-secondary bulk-button"><i class="la la-copy"></i> {{ trans('Download') }}</a>
@endif

@push('after_scripts')
<script>
	if (typeof bulkDownloadEntries != 'function') {
	  function bulkDownloadEntries(button) {

	      if (typeof crud.checkedItems === 'undefined' || crud.checkedItems.length == 0)
	      {
  	        new Noty({
	          type: "warning",
	          text: "<strong>{!! trans('backpack::crud.bulk_no_entries_selected_title') !!}</strong><br>{!! trans('backpack::crud.bulk_no_entries_selected_message') !!}"
	        }).show();

	      	return;
	      }

	      var message = "{!! trans('bulkDownloadConfirm') !!}";

	      // show confirm message
	      swal({
			  title: "{!! trans('backpack::base.warning') !!}",
			  text: message,
			  icon: "warning",
			  buttons: {
			  	cancel: {
				  text: "{!! trans('backpack::crud.cancel') !!}",
				  value: null,
				  visible: true,
				  className: "bg-secondary",
				  closeModal: true,
				},
			  	delete: {
				  text: "{{ trans('Download') }}",
				  value: true,
				  visible: true,
				  className: "bg-primary",
				}
			  },
			}).then((value) => {
				if (value) {
		      		var route = "{{ url($crud->route) }}/bulk-download";

					$.ajax({
						url: route,
						type: 'POST',
						data: { entries: crud.checkedItems },
						success: function() {
		    	          new Noty({
				            type: "success",
				            text: "{!! trans('bulkDownloadRequested') !!}"
				          }).show();

						  crud.checkedItems = [];
						  crud.table.draw(false);
						},
						error: function(result) {
		    	          new Noty({
				            type: "danger",
				            text: "<strong>{!! trans('Server Error') !!}</strong>",
				          }).show();
						}
					});
				}
			});
      }
	}
</script>
@endpush
