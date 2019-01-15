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
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                        @lang('Invoices')
                </div>
                <div class="box-tools pull-right">
                    
                </div>
                
            </div>
            
            <div class="box-body">           
                
                <table class="table table-striped responsive">
                    <thead>
                        <tr>
                            <th>@lang('Pre-invoice ID')</th>
                            <th>@lang('Invoice Number')</th>
                            <th>@lang('Client')</th>
                            <th>@lang('Date')</th>
                            <th>@lang('Price')</th>
                            <th>@lang('Actions')</th>
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
