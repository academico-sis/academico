
<div class="row">

<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            @lang('Products')
        </div>

        <div class="card-body">

            <table class="table">
                <thead>
                    <th>@lang('Product')</th>
                    <th>@lang('Price')</th>
                    <th>@lang('Invoice ID')</th>
                    <th>@lang('Comment')</th>
                    <th></th>
                </thead>
                <tbody>
                    @foreach($enrollment->pre_invoice()->first()->pre_invoice_details as $product)
                        <tr>
                            <td>{{ $payment->date }}</td>
                            <td>${{ $payment->value }}</td>
                            <td>{{ $payment->invoice_id }}</td>
                            <td>{{ $payment->comment }}</td>
                            <td>
                                <a class="btn btn-sm btn-warning" href="/payment/{{$payment->id}}/edit">
                                    <i class="la la-pencil"></i>
                                </a>

                                <button class="btn btn-sm btn-danger" onclick="deletePayment({{ $payment->id }})">
                                    <i class="la la-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    <tr style="font-weight: bold">
                        <td>@lang('Balance')</td>
                        <td>${{ $enrollment->balance }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>


        </div>
    </div>
</div>

</div>

@push('after_scripts') @if (request()->ajax()) @endpush @endif
<script>

	  function deletePayment(payment) {
		// ask for confirmation before deleting an item

		swal({
		  title: "{!! trans('backpack::base.warning') !!}",
		  text: "{!! trans('backpack::crud.delete_confirm') !!}",
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
			  text: "{!! trans('backpack::crud.delete') !!}",
			  value: true,
			  visible: true,
			  className: "bg-danger",
			}
		  },
		}).then(value => {
			if (value) {
				$.ajax({
			      url: `/payment/${payment}`,
			      type: 'DELETE',
			      success: result => {
			          if (result === 1) {
			          	  // Show a success notification bubble
			              new Noty({
		                    type: "success",
		                    text: "{!! '<strong>'.trans('backpack::crud.delete_confirmation_title').'</strong><br>'.trans('backpack::crud.delete_confirmation_message') !!}"
		                  }).show();

			              // Hide the modal, if any
			              $('.modal').modal('hide');

			              window.location.reload()
			          } else {
			              // if the result is an array, it means
			              // we have notification bubbles to show
			          	  if (result instanceof Object) {
			          	  	// trigger one or more bubble notifications
			          	  	Object.entries(result).forEach((entry, index) => {
			          	  	  var type = entry[0];
			          	  	  entry[1].forEach((message, i) =>
                                  new Noty({
                                      type,
                                      text: message
                                  }).show());
			          	  	});
			          	  } else {// Show an error alert
				              swal({
				              	title: "{!! trans('backpack::crud.delete_confirmation_not_title') !!}",
	                            text: "{!! trans('backpack::crud.delete_confirmation_not_message') !!}",
				              	icon: "error",
				              	timer: 4000,
				              	buttons: false,
				              });
			          	  }
			          }
			      },
			      error: result => {
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

	// make it so that the function above is run after each DataTable draw event
	// crud.addFunctionToDataTablesDrawEventQueue('deleteEntry');
</script>
@if (!request()->ajax()) @endpush @endif
