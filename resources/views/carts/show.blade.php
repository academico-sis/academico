@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang('Cart Details')
    </h1>
</section>
@endsection


@section('content')

<div class="row">

    <div class="col-md-4">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">
                        @lang('Student Info')
                    </div>
                    <div class="box-tools pull-right">

                            <form action="/cart/{{$student->id}}/checkout" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success"><i class="fa fa-dollar"></i>@lang('Continue with this contact information')</button>
                            </form>


                    </div>
                </div>
                
                <div class="box-body">
                    @include('students.student_info')
                </div>
            </div>
        </div>

        @foreach ($student->contacts as $contact)
        <div class="col-md-4">
                <div class="box">
                    <div class="box-header with-border">
                        <div class="box-title">
                            @lang('Additional Contact')
                            {{ $contact->relationship }}
                        </div>
    
                        <div class="box-tools pull-right">

                            <form action="/cart/{{$student->id}}/checkout" method="POST">
                                @csrf
                                <input type="hidden" name="invoice_data" value="{{$contact->id}}">
                                <button type="submit" class="btn btn-success"><i class="fa fa-dollar"></i>@lang('Continue with this contact information')</button>
                            </form>


                        </div>
                    </div>
                    
                    <div class="box-body">
                        @include('students.additional_info')
                    </div>
                </div>
            </div>
        @endforeach

</div>

<div class="row" id="app">
    <div class="col-lg-8">
        <cart-component
            :products="{{ json_encode($products) }}">
        </cart-component>
    </div>
</div>

@endsection


@section('after_scripts')
    <script src="/js/app.js"></script>
@endsection