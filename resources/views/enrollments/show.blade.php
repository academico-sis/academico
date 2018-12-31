@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        {{ ucfirst(trans_choice('academico.enrollment_details', 1)) }}
    </h1>
</section>
@endsection


@section('content')

<div class="row">

    <div class="col-md-4">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    {{ ucfirst(trans_choice('academico.student_info', 1)) }}
                </div>
                <div class="box-tools pull-right">
                    <button class="btn btn-warning"><i class="fa fa-pencil"></i></button>
                </div>
            </div>
            
            <div class="box-body">
                <p>{{ ucfirst(trans_choice('academico.name', 1)) }}: {{ $enrollment->student_data->self_data->firstname }} {{ $enrollment->student_data->self_data->lastname }}</p>
                <p>{{ ucfirst(trans_choice('academico.idnumber', 1)) }}: {{ $enrollment->student_data->self_data->idnumber }}</p>
                <p>{{ ucfirst(trans_choice('academico.address', 1)) }}: {{ $enrollment->student_data->self_data->address }}</p>
                
                @if (count($enrollment->student_data->phone) > 0)
                    <p>{{ ucfirst(trans_choice('academico.phonenumber', 1)) }}:
                        <ul>
                            @foreach($enrollment->student_data->phone as $phone)
                            <li>{{ $phone->phone_number }}</li>
                            @endforeach
                        </ul>
                    </p>
                @endif
                <p>{{ ucfirst(trans_choice('academico.email', 1)) }}: {{ $enrollment->student_data->email }}</p>
                <p>{{ ucfirst(trans_choice('academico.birthdate', 1)) }}: {{ $enrollment->student_data->birthdate }}</p>
                <p>{{ ucfirst(trans_choice('academico.age', 1)) }}: {{ $enrollment->student_data->age }} {{ trans_choice('academico.yearsold', $enrollment->student_data->age) }}</p>
            </div>
        </div>
    </div>



    <div class="col-md-8">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    {{ ucfirst(trans_choice('academico.products', 1)) }}
                </div>
                <div class="box-tools pull-right">
                    <button class="btn btn-success"><i class="fa fa-plus"></i></button>
                    <button class="btn btn-warning"><i class="fa fa-percent"></i></button>
                    <button class="btn btn-info"><i class="fa fa-dollar"></i></button>

                </div>
            </div>
            
            <div class="box-body">           
               <table>
                   <thead>
                       <th>Qté</th>
                       <th>Produit</th>
                       <th>Prix</th>
                       <th>Total</th>
                       <th>Actions</th>
                   </thead>
                   <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td></td>
                                <td>{{ $product->product_type }}</td>
                                <td>{{ $product->price }}</td>
                                <td></td>
                                <td></td>

                            </tr>
                       @endforeach

                   </tbody>
               </table>
            </div>
        </div>
    </div>

</div>






<div class="row">

    <div class="col-md-4">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    {{ ucfirst(trans_choice('academico.enrollment_status', 1)) }}
                </div>
                <div class="box-tools pull-right">
                </div>
            </div>
            
            <div class="box-body">
                <div class="label label-primary">
                    {{ $enrollment->enrollment_status['name'] }}
                </div>

                Invoice(s)
                @foreach ($enrollment->pre_invoice as $pre_invoice)
                Fiche n°{{ $pre_invoice->id }}
                Facture n°{{ $pre_invoice->invoice_number }}
                <br>
                @endforeach

                {{-- if the enrollment is not yet invoiced --}}
                @if($enrollment->enrollment_status['id'] == 1)
                
                <button class="btn btn-primary">
                    Facturer
                </button>

                <button class="btn btn-danger">
                    Annuler
                </button>

                @endif


            </div>
        </div>
    </div>



</div>
@endsection
