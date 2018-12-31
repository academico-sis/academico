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
    

{{-- todo deduplicate this from the student details view --}}

    <div class="col-md-4">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">
                        {{ ucfirst(trans_choice('academico.student_info', 1)) }}
                    </div>
                    <div class="box-tools pull-right">

                            <form action="/carts/{{$student->id}}/checkout" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success"><i class="fa fa-dollar"></i>Continuer avec ces coordonnées</button>
                            </form>


                    </div>
                </div>
                
                <div class="box-body">           
                    <p>{{ ucfirst(trans_choice('academico.name', 1)) }}: {{ $student->self_data->firstname }} {{ $student->self_data->lastname }}</p>
                    <p>{{ ucfirst(trans_choice('academico.idnumber', 1)) }}: {{ $student->self_data->idnumber }}</p>
                    <p>{{ ucfirst(trans_choice('academico.address', 1)) }}: {{ $student->self_data->address }}</p>
                    
                    @if (count($student->phone) > 0)
                        <p>{{ ucfirst(trans_choice('academico.phonenumber', 1)) }}:
                            <ul>
                                @foreach($student->phone as $phone)
                                <li>{{ $phone->phone_number }}</li>
                                @endforeach
                            </ul>
                        </p>
                    @endif
                    <p>{{ ucfirst(trans_choice('academico.email', 1)) }}: {{ $student->email }}</p>
                    <p>{{ ucfirst(trans_choice('academico.birthdate', 1)) }}: {{ $student->birthdate }}</p>
                    <p>{{ ucfirst(trans_choice('academico.age', 1)) }}: {{ $student->age }} {{ trans_choice('academico.yearsold', $student->age) }}</p>
                </div>
            </div>
        </div>

        @foreach ($student->additional_data as $additional_data)
        <div class="col-md-4">
                <div class="box">
                    <div class="box-header with-border">
                        <div class="box-title">
                            {{ ucfirst(trans_choice('academico.additional_info', 1)) }}
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
                        <p>{{ ucfirst(trans_choice('academico.name', 1)) }}: {{ $additional_data->firstname }} {{ $additional_data->lastname }}</p>
                        <p>{{ ucfirst(trans_choice('academico.idnumber', 1)) }}: {{ $additional_data->idnumber }}</p>
                        <p>{{ ucfirst(trans_choice('academico.address', 1)) }}: {{ $additional_data->address }}</p>
                        @if (count($additional_data->phone) > 0)
                            <p>{{ ucfirst(trans_choice('academico.phonenumber', 1)) }}:
                                <ul>
                                    @foreach($additional_data->phone as $phone)
                                    <li>{{ $phone->phone_number }}</li>
                                    @endforeach
                                </ul>
                            </p>
                        @endif
                        <p>{{ ucfirst(trans_choice('academico.email', 1)) }}: {{ $additional_data->email }}</p>
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