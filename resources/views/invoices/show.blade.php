@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang('Invoices')
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    <div class="col-md-6">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang('Invoices')
                </div>
                <div class="box-tools pull-right">
                </div>
                
            </div>
            
            <div class="box-body">           
                <p>@lang('Pre-invoice ID') : {{ $preInvoice->id }}</p>
                <p>@lang('Date') : {{ $preInvoice->created_at }}</p>
                <p>@lang('Client name') : {{ $preInvoice->client_name }}</p>
                <p>@lang('Client email') : {{ $preInvoice->client_email }}</p>
                <p>@lang('Client address') : {{ $preInvoice->client_address }}</p>
                <p>@lang('Client ID Number') : {{ $preInvoice->client_idnumber }}</p>
                <p>@lang('Client Phone Number') : todo</p>

            </div>
        </div>
    </div>

    <div class="col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">
                        @lang('Numéro de facture')
                    </div>
                    <div class="box-tools pull-right">
                        <button class="btn btn-warning" data-toggle="modal" data-target="#editInvoiceNumberModal">
                            <i class="fa fa-pencil"></i>
                        </button>
                    </div>
                    
                </div>
                
                <div class="box-body">           
                    <h3>{{ $preInvoice->invoice_number }}</h3>
                </div>
            </div>
        </div>
</div>

<div class="row">

<div class="col-md-8">
    <div class="box">
        <div class="box-header with-border">
            <div class="box-title">
                @lang('products')
            </div>
            <div class="box-tools pull-right">
            </div>
        </div>
        
        <div class="box-body">
            
            <table class="table">
                <thead>
                    <th>@lang('Qty')</th>
                    <th>@lang('Product')</th>
                    <th>@lang('Price')</th>
                    <th>@lang('Total')</th>
                </thead>
                <tbody>
                    @foreach($products as $product)
                        <tr>
                            <td>{{ $product->quantity }}</td>
                            <td>{{ $product->product_name }}</td>
                            <td>${{ $product->price }}</td>
                            <td>${{ $product->price * $product->quantity }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td></td>
                        <td>@lang('TOTAL')</td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
                            
            
        </div>
    </div>
</div>

</div>


      <!-- Modal -->
      <div class="modal fade" id="editInvoiceNumberModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title" id="myModalLabel">@lang('Add invoice number')</h4>
            </div>
            <div class="modal-body">
            <form method="POST" action="/invoices/{{ $preInvoice->id }}">
                @method('PATCH')
                @csrf
                <label for="invoice_number">@lang('Nouveau numéro de facture')</label>
                <input type="text" name="invoice_number">
                <button type="submit" class="btn btn-primary">@lang('Save changes')</button>

            </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">@lang('Close')</button>
            </div>
          </div>
        </div>
      </div>
@endsection

