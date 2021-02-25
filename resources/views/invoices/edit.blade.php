@extends('backpack::blank')

@section('header')
<section class="container-fluid">
    <h2>@lang('Edit Invoice Number')</h2>
</section>
@endsection


@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card">

            <div class="card-body">
                <form action="{{ route('store-invoice-number', ['invoice' => $invoice]) }}" method="post">
                    @method('PATCH')
                    @csrf

                    <label for="receipt_number">Numero de factura</label>
                    <input type="text" name="receipt_number" id="receipt_number" />

                    <button class="btn btn-success" type="submit">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
