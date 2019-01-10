@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang_u('academico.enrollment_details')
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    
    <div class="col-md-4">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang_u('academico.student_info')
                </div>
                <div class="box-tools pull-right">
                    <button class="btn btn-warning"><i class="fa fa-pencil"></i></button>
                </div>
            </div>
            
            <div class="box-body">
                <p>@lang_u('academico.name'): {{ $enrollment->student_data->firstname }} {{ $enrollment->student_data->lastname }}</p>
                <p>@lang_u('academico.idnumber'): {{ $enrollment->student_data->idnumber }}</p>
                <p>@lang_u('academico.address'): {{ $enrollment->student_data->address }}</p>
                
                @if (count($enrollment->student_data->phone) > 0)
                <p>@lang_u('academico.phonenumber'):
                    <ul>
                        @foreach($enrollment->student_data->phone as $phone)
                        <li>{{ $phone->phone_number }}</li>
                        @endforeach
                    </ul>
                </p>
                @endif
                <p>@lang_u('academico.email'): {{ $enrollment->student_data->email }}</p>
                <p>@lang_u('academico.birthdate'): {{ $enrollment->student_data->birthdate }}</p>
                <p>@lang_u('academico.age'): {{ $enrollment->student_data->age }} @lang('academico.yearsold')</p>
            </div>
        </div>
    </div>
    
    

    <div class="col-md-4">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">
                        @lang_u('academico.course_info')
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
                    @lang_u('academico.comments')
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
                    @lang_u('academico.status')
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
                    <ul>
                        @foreach ($enrollment->pre_invoice as $pre_invoice)
                        <li>
                            <a href="/invoices/{{ $pre_invoice->id }}">Numéro de facture: {{ $pre_invoice->invoice_number ?? "inconnu" }}</a>
                    </li>
                        @endforeach
                    </ul>
                @elseif($enrollment->enrollment_status['id'] == 1)
                <p>Matricula pendiente</p>
                
                <a href="/enrollments/{{ $enrollment->id }}/bill" class="btn btn-primary">
                    Facturer
                </a>
                
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
                    @lang_u('academico.products')
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
