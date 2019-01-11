@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang_u('academico.invoices')
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    <div class="col-md-6">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                        @lang_u('academico.invoices')
                </div>
                <div class="box-tools pull-right">
                    
                </div>
                
            </div>
            
            <div class="box-body">           
                <p>Numéro de pré-facture : {{ $preInvoice->id }}</p>
                <p>Date : {{ $preInvoice->created_at }}</p>
                <p>Nom du client : {{ $preInvoice->client_name }}</p>
                <p>Email du client : {{ $preInvoice->client_email }}</p>
                <p>Adresse du client : {{ $preInvoice->client_address }}</p>
                <p>Numéro d'identité du client : {{ $preInvoice->client_idnumber }}</p>
                <p>Numéro de téléphone du client : todo</p>

            </div>
        </div>
    </div>

    <div class="col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">
                        Numéro de facture
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
                @lang_u('academico.products')
            </div>
            <div class="box-tools pull-right">
            </div>
        </div>
        
        <div class="box-body">
            
            <table class="table">
                <thead>
                    <th>Qté</th>
                    <th>Produit</th>
                    <th>Prix</th>
                    <th>Total</th>
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
                        <td>TOTAL</td>
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
              <h4 class="modal-title" id="myModalLabel">Ajouter un numéro de facture</h4>
            </div>
            <div class="modal-body">
            <form method="POST" action="/invoices/{{ $preInvoice->id }}">
                @method('PATCH')
                @csrf
                <label for="invoice_number">Nouveau numéro de facture</label>
                <input type="text" name="invoice_number">
                <button type="submit" class="btn btn-primary">Save changes</button>

            </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
@endsection

