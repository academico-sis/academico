@if ($crud->hasAccess('create') && isset(request()->course))
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
          new Noty({
                text: "{{ trans('backpack::crud.bulk_no_entries_selected_message') }}",
                type: "warning"
            }).show();

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
            new Noty({
                text: "Skills have been attached to course",
                type: "success"
            }).show();
            
            window.location.href = '/course/' + result + '/skill'
          },
          error: function(result) {
            // Show an alert with the result
            new Noty({
                text: "One or more entries could not be attached. Please try again.",
                type: "warning"
            }).show();
          }
        });
        }
      }
  }
</script>
@endpush