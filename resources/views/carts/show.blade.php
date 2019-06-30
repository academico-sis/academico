@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang('Cart Details')
    </h1>
</section>
@endsection


@section('content')


<div class="row" id="app">
    <div class="col-lg-8">
        
        <cart-component
            :enrollmentslist="{{ json_encode($enrollments) }}"
            :bookslist="{{ json_encode($books) }}"
            :feeslist="{{ json_encode($fees) }}"
        >
        </cart-component>
    </div>
</div>

@endsection


@section('after_scripts')
    <script src="/js/app.js"></script>
@endsection