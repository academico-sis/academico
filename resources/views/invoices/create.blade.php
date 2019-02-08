@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang('New pre-invoice')
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-tools pull-right">
                </div>
                
            </div>
            
            <div class="box-body">           

                <form action="{{ route('quickInvoice') }}" method="post">
                    
                    @csrf
                    
                    <input type="hidden" name="enrollment_id" value="{{ $enrollment->id }}">

                    <div class="form-group">
                        <label for="invoice_number">@lang('Invoice Number')</label>
                        <input type="text" name="invoice_number" id="invoice_number">
                    </div>

                    <div class="form-group">
                        <label for="comment">@lang('Comment')</label>
                        <textarea name="comment" id="comment" cols="50" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <button class="btn btn-primary" type="submit">@lang('Save')</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>
@endsection