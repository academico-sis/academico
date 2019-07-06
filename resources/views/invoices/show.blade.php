
<div class="row">
    <div class="col-md-4">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang('Invoices')
                </div>
                <div class="box-tools pull-right">
                </div>
                
            </div>
            
            <div class="box-body">        
                <p>@lang('Invoice ID') : {{ $enrollment->pre_invoice()->first()->invoice_number }}</p>   
                <p>@lang('Pre-invoice ID') : {{ $enrollment->pre_invoice()->first()->id }}</p>
                <p>@lang('Date') : {{ $enrollment->pre_invoice()->first()->created_at }}</p>
                <p>@lang('Client name') : {{ $enrollment->pre_invoice()->first()->client_name }}</p>
                <p>@lang('Client email') : {{ $enrollment->pre_invoice()->first()->client_email }}</p>
                <p>@lang('Client address') : {{ $enrollment->pre_invoice()->first()->client_address }}</p>
                <p>@lang('Client ID Number') : {{ $enrollment->pre_invoice()->first()->client_idnumber }}</p>
                <p>@lang('Client Phone Number') : todo</p>

            </div>
        </div>
    </div>

<!-- todo remove this, since there are no longer preinvoice comments -->
@if($enrollment->pre_invoice()->first()->comments->count() > 0)
        <div class="col-md-4">
                <div class="box">
                    <div class="box-header with-border">
                        <div class="box-title">
                            @lang('Comment')
                        </div>
                        <div class="box-tools pull-right">
                        </div>
                        
                    </div>
                    
                    <div class="box-body">           
                        <ul>
                            @foreach ($enrollment->pre_invoice()->first()->comments as $comment)
                                <li>{{ $comment->body }} ({{ $comment->date }})</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
@endif


<div class="col-md-8">
    <div class="box">
        <div class="box-header with-border">
            <div class="box-title">
                @lang('Products')
            </div>
            <div class="box-tools pull-right">
            </div>
        </div>
        
        <div class="box-body">
            
            <table class="table">
                <thead>
                    <th>@lang('Product')</th>
                    <th>@lang('Price')</th>
                </thead>
                <tbody>
                    @foreach($enrollment->pre_invoice()->first()->pre_invoice_details as $product)
                        <tr>
                            <td>{{ $product->product_name }}</td>
                            <td>${{ $product->price }}</td>
                        </tr>
                    @endforeach
                    <tr style="font-weight: bold">
                        <td>@lang('TOTAL')</td>
                        <td>${{ $enrollment->pre_invoice->first()->total_price }}</td>
                    </tr>
                </tbody>
            </table>
                            
            
        </div>
    </div>
</div>

</div>

