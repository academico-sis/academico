@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        {{ ucfirst(trans_choice('academico.cart_details', 1)) }}
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    
        <div class="col-md-4">
                <div class="box">
                    <div class="box-header with-border">
                        <div class="box-title">
                            {{ ucfirst(trans_choice('academico.student', 1)) }}
                        </div>
                        <div class="box-tools pull-right">
                            <button class="btn btn-success"><i class="fa fa-dollar"></i>Continuer avec ces coordonnées</button>

                        </div>
                    </div>
                    
                    <div class="box-body">
                    </div>
                </div>
            </div>
        </div>

    <div class="row" id="app">
        <cart-component
            :products="{{ json_encode($products) }}"
        >
        </cart-component>   
    </div>

@endsection


@section('after_scripts')
    <script src="/js/app.js"></script>
@endsection