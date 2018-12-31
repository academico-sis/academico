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
    
    

    <div class="col-md-4">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">
                        {{ ucfirst(trans_choice('academico.course_info', 1)) }}
                    </div>
                    <div class="box-tools pull-right">
                    </div>
                </div>
                
                <div class="box-body">
                        <p>{{ $enrollment->date }}</p>
                        <p>{{ $enrollment->id }}</a>
                        </p>
                        @if ($enrollment->children_count > 0)
                            <p>{{ $enrollment->course_data->name }}</p>
                            <p>Children enrollments:</p>
                            <ul>
                            @foreach ($enrollment->children as $children)
                                <li><a href="/enrollments/{{ $children->id }}">{{ $children->course_data->name }}</a>
                                    ({{ $children->course_data->period->name }})</li>
                            @endforeach
                            </ul>
                            @elseif ($enrollment->parent_id !== null)
                            this enrollment belongs to <a href="/enrollments/{{ $enrollment->parent_id }}">{{ $enrollment->parent_id }}</a>
                        @endif
                        <p>{{ $enrollment->course_data->period->name }}</p>
                        
                        <p>
                            {{ $enrollment->result['result_name']['name'] }}
                            <a href="/results/{{ $enrollment->id }}" class="btn btn-xs btn-info">
                                <i class="fa fa-eye"></i>
                            </a>
                        </p>
                </div>
            </div>
        </div>


    <div class="col-md-4">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    {{ ucfirst(trans_choice('academico.comments', 1)) }}
                </div>
                <div class="box-tools pull-right">
                    <button class="btn btn-primary"><i class="fa fa-plus"></i></button>
                </div>
            </div>
            
            <div class="box-body">
                
            </div>
        </div>
    </div>
    


    
</div>
<div class="row">
    
<div class="col-md-4">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    {{ ucfirst(trans_choice('academico.status', 1)) }}
                </div>
                <div class="box-tools pull-right">
                </div>
            </div>
            
            <div class="box-body">
                @if($enrollment->enrollment_status['id'] == 2)
                
                    <div class="label label-primary">
                        {{ $enrollment->enrollment_status['name'] }}
                    </div>
                    
                    Invoice(s)
                    @foreach ($enrollment->pre_invoice as $pre_invoice)
                    Fiche n°{{ $pre_invoice->id }}
                    Facture n°{{ $pre_invoice->invoice_number }}
                    <br>
                    @endforeach

                @elseif($enrollment->enrollment_status['id'] == 1)
                <p>Matricula pendiente</p>
                
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
    
    @if($enrollment->enrollment_status['id'] == 2)

    <div class="col-md-8">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    {{ ucfirst(trans_choice('academico.products', 1)) }}
                </div>
                <div class="box-tools pull-right">
                </div>
            </div>
            
            <div class="box-body">
                
                <table class="table">
                    <thead>
                        <th>Qté</th>
                        <th>Produit</th>
                        <th>Prix</th>
                        <th>Total</th>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            @foreach ($product->pre_invoice_details as $item)
                            <tr>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->product_name }}</td>
                                <td>${{ $item->price }}</td>
                                <td>${{ $item->price * $item->quantity }}</td>
                            </tr>
                            @endforeach
                        @endforeach
                        <tr>
                            <td></td>
                            <td>TOTAL</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                                
                
            </div>
        </div>
    </div>
    @endif

</div>

@endsection
