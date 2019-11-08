@if ($crud->hasAccess('clone') && $entry->parent_course_id == null)

	<a href="javascript:void(0)" onclick="cloneEntry(this)" data-route="{{ url($crud->route.'/'.$entry->getKey().'/clone') }}" class="btn btn-sm btn-link" data-button-type="clone"><i class="fa fa-clone"></i></a>
@endif

<script>
	if (typeof cloneEntry != 'function') {
	  $("[data-button-type=clone]").unbind('click');

	  function cloneEntry(button) {
	      // ask for confirmation before deleting an item
	      // e.preventDefault();
	      var button = $(button);
	      var route = button.attr('data-route');

          $.ajax({
              url: route,
              type: 'POST',
              success: function(result) {
                  // Show an alert with the result
                  new PNotify({
                      title: "Entry cloned",
                      text: "A new entry has been added, with the same information as this one.",
                      type: "success"
                  });

                  // Hide the modal, if any
                  $('.modal').modal('hide');

                  // todo open new course edit page
                  crud.table.ajax.reload();
              },
              error: function(result) {
                  // Show an alert with the result
                  new PNotify({
                      title: "Cloning failed",
                      text: "The new entry could not be created. Please try again.",
                      type: "warning"
                  });
              }
          });
      }
	}

	// make it so that the function above is run after each DataTable draw event
	// crud.addFunctionToDataTablesDrawEventQueue('cloneEntry');
</script>