@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang_u('academico.cart_details')
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
                        @lang_u('academico.student_info')
                    </div>
                    <div class="box-tools pull-right">

                            <form action="/carts/{{$student->id}}/checkout" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success"><i class="fa fa-dollar"></i>Continuer avec ces coordonnées</button>
                            </form>


                    </div>
                </div>
                
                <div class="box-body">           
                    <p>@lang_u('academico.name'): {{ $student->firstname }} {{ $student->lastname }}</p>
                    <p>@lang_u('academico.idnumber'): {{ $student->idnumber }}</p>
                    <p>@lang_u('academico.address'): {{ $student->address }}</p>
                    
                    @if (count($student->phone) > 0)
                        <p>@lang_u('academico.phonenumber'):
                            <ul>
                                @foreach($student->phone as $phone)
                                <li>{{ $phone->phone_number }}</li>
                                @endforeach
                            </ul>
                        </p>
                    @endif
                    <p>@lang_u('academico.email'): {{ $student->email }}</p>
                    <p>@lang_u('academico.birthdate'): {{ $student->birthdate }}</p>
                    <p>@lang_u('academico.age'): {{ $student->age }} {{ @lang('academico.yearsold')</p>
                </div>
            </div>
        </div>

        @foreach ($student->additional_data as $additional_data)
        <div class="col-md-4">
                <div class="box">
                    <div class="box-header with-border">
                        <div class="box-title">
                            @lang_u('academico.additional_info')
                            {{ $additional_data->relationship }}
                        </div>
    
                        <div class="box-tools pull-right">

                            <form action="/carts/{{$student->id}}/checkout" method="POST">
                                @csrf
                                <input type="hidden" name="invoice_data" value="{{$additional_data->id}}">
                                <button type="submit" class="btn btn-success"><i class="fa fa-dollar"></i>Continuer avec ces coordonnées</button>
                            </form>


                        </div>
                    </div>
                    
                    <div class="box-body">
                        <p>@lang_u('academico.name'): {{ $additional_data->firstname }} {{ $additional_data->lastname }}</p>
                        <p>@lang_u('academico.idnumber'): {{ $additional_data->idnumber }}</p>
                        <p>@lang_u('academico.address'): {{ $additional_data->address }}</p>
                        @if (count($additional_data->phone) > 0)
                            <p>@lang_u('academico.phonenumber'):
                                <ul>
                                    @foreach($additional_data->phone as $phone)
                                    <li>{{ $phone->phone_number }}</li>
                                    @endforeach
                                </ul>
                            </p>
                        @endif
                        <p>@lang_u('academico.email'): {{ $additional_data->email }}</p>
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