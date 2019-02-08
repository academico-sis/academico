@if ($crud->hasAccess('create') && $crud->bulk_actions && isset(request()->course))
  <a href="javascript:void(0)" onclick="bulkAttachEntries(this)" class="btn btn-success">
    <i class="fa fa-clone"></i> @lang('Attach to course')
  </a>
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

        var message = "Are you sure you want to attach these skills to the course?";

        // show confirm message
        if (confirm(message) == true) {
            var ajax_calls = [];
            var attach_route = "{{ url($crud->route) }}/bulk-attach";
            var course = "{{request()->course}}";

        // submit an AJAX delete call
        $.ajax({
          url: attach_route,
          type: 'POST',
          data: {
            entries: crud.checkedItems,
            course: course,
           },
          success: function(result) {
            // Show an alert with the result
            new PNotify({
                title: "Skills set saved",
                text: "Skills have been attached to course",
                type: "success"
            });

            crud.checkedItems = [];
            crud.table.ajax.reload();
          },
          error: function(result) {
            // Show an alert with the result
            new PNotify({
                title: "Attaching failed",
                text: "One or more entries could not be attached. Please try again.",
                type: "warning"
            });
          }
        });
        }
      }
  }
</script>
@endpush