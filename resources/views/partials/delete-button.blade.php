
<a href="javascript:void(0)" onclick="deleteEntry(this)" data-route="{{ $route }}" redirect-to="{{ $redirectRoute }}" class="btn btn-sm btn-link" data-button-type="delete"><i class="la la-trash"></i></a>

{{-- Button Javascript --}}
{{-- - used right away in AJAX operations (ex: List) --}}
{{-- - pushed to the end of the page, after jQuery is loaded, for non-AJAX operations (ex: Show) --}}
@push('after_scripts')
<script>

    if (typeof deleteEntry != 'function') {
        $("[data-button-type=delete]").unbind('click');

        function deleteEntry(button) {
            var route = $(button).attr('data-route');
            var redirectLocation = $(button).attr('redirect-to')
            swal({
                title: "{!! trans('backpack::base.warning') !!}",
                text: "{!! trans('backpack::crud.delete_confirm') !!}",
                icon: "warning",
                buttons: ["{!! trans('backpack::crud.cancel') !!}", "{!! trans('backpack::crud.delete') !!}"],
                dangerMode: true,
            }).then((value) => {
                if (value) {
                    $.ajax({
                        url: route,
                        type: 'DELETE',
                        success: function(result) {
                            // Show a success notification bubble
                            new Noty({
                                type: "success",
                                text: "{!! '<strong>'.trans('backpack::crud.delete_confirmation_title').'</strong><br>'.trans('backpack::crud.delete_confirmation_message') !!}"
                            }).show();

                            window.location.href = redirectLocation;
                        },
                        error: function(result) {
                            // Show an alert with the result
                            swal({
                                title: "{!! trans('backpack::crud.delete_confirmation_not_title') !!}",
                                text: "{!! trans('backpack::crud.delete_confirmation_not_message') !!}",
                                icon: "error",
                                timer: 4000,
                                buttons: false,
                            });
                        }
                    });
                }
            });

        }
    }
</script>
@endpush
