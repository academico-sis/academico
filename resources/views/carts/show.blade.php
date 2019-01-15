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
    

{{-- todo deduplicate this from the student details view --}}

    <div class="col-md-4">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">
                        @lang('Student Info')
                    </div>
                    <div class="box-tools pull-right">

                            <form action="/carts/{{$student->id}}/checkout" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success"><i class="fa fa-dollar"></i>@lang('Continue with this contact information')</button>
                            </form>


                    </div>
                </div>
                
                <div class="box-body">           
                    <p>@lang('name'): {{ $student->firstname }} {{ $student->lastname }}</p>
                    <p>@lang('idnumber'): {{ $student->idnumber }}</p>
                    <p>@lang('address'): {{ $student->address }}</p>
                    
                    @if (count($student->phone) > 0)
                        <p>@lang('Phone Number'):
                            <ul>
                                @foreach($student->phone as $phone)
                                <li>{{ $phone->phone_number }}</li>
                                @endforeach
                            </ul>
                        </p>
                    @endif
                    <p>@lang('email'): {{ $student->email }}</p>
                    <p>@lang('birthdate'): {{ $student->birthdate }}</p>
                    <p>@lang('age'): {{ $student->age }} @lang('yearsold')</p>
                </div>
            </div>
        </div>

        @foreach ($student->additional_data as $additional_data)
        <div class="col-md-4">
                <div class="box">
                    <div class="box-header with-border">
                        <div class="box-title">
                            @lang('Additional Contact')
                            {{ $additional_data->relationship }}
                        </div>
    
                        <div class="box-tools pull-right">

                            <form action="/carts/{{$student->id}}/checkout" method="POST">
                                @csrf
                                <input type="hidden" name="invoice_data" value="{{$additional_data->id}}">
                                <button type="submit" class="btn btn-success"><i class="fa fa-dollar"></i>@lang('Continue with this contact information')</button>
                            </form>


                        </div>
                    </div>
                    
                    <div class="box-body">
                        <p>@lang('name'): {{ $additional_data->firstname }} {{ $additional_data->lastname }}</p>
                        <p>@lang('idnumber'): {{ $additional_data->idnumber }}</p>
                        <p>@lang('address'): {{ $additional_data->address }}</p>
                        @if (count($additional_data->phone) > 0)
                            <p>@lang('phonenumber'):
                                <ul>
                                    @foreach($additional_data->phone as $phone)
                                    <li>{{ $phone->phone_number }}</li>
                                    @endforeach
                                </ul>
                            </p>
                        @endif
                        <p>@lang('email'): {{ $additional_data->email }}</p>
                    </div>
                </div>
            </div>
        @endforeach


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