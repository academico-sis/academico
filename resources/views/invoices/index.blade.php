@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang('invoices')
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                        @lang('invoices')
                </div>
                <div class="box-tools pull-right">
                    
                </div>
                
            </div>
            
            <div class="box-body">           
                
                <table class="table table-striped responsive">
                    <thead>
                        <tr>
                            <th>PreInvoice ID</th>
                            <th>@lang('invoice_number')</th>
                            <th>@lang('client')</th>
                            <th>@lang('date')</th>
                            <th>@lang('price')</th>

                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->id }}</td>
                            <td>{{ $invoice->invoice_number }}
                                <a href="/invoices/{{ $invoice->id }}" class="btn btn-xs btn-warning">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            </td>
                            <td>{{ $invoice->id }}</td>
                            <td>{{ $invoice->created_at }}</td>
                            <td>${{ $invoice->total_price }}</td>
                            <td>

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
