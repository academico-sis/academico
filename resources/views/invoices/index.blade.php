@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        {{ ucfirst(trans_choice('academico.invoices', 2)) }}
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                        {{ ucfirst(trans_choice('academico.invoices', 2)) }}
                </div>
                <div class="box-tools pull-right">
                    
                </div>
                
            </div>
            
            <div class="box-body">           
                
                <table class="table table-striped responsive">
                    <thead>
                        <tr>
                            <th>PreInvoice ID</th>
                            <th>{{ ucfirst(trans_choice('academico.invoice_number', 1)) }}</th>
                            <th>{{ ucfirst(trans_choice('academico.client', 1)) }}</th>
                            <th>{{ ucfirst(trans_choice('academico.date', 1)) }}</th>
                            <th>{{ ucfirst(trans_choice('academico.price', 1)) }}</th>

                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->id }}</td>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->id }}</td>
                            <td>{{ $invoice->created_at }}</td>
                            <td>{{ $invoice->id }}</td>
                            <td></td>
                        </tr>
                        @endforeach
                    </tbody>
                    
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
