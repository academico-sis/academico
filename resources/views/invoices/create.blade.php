@extends('backpack::blank')

@section('header')
<section class="container-fluid">
    <h2>
        @lang('New pre-invoice')
    </h2>
</section>
@endsection


@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
            
            <div class="card-body">           

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