@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang('Enrollment Details')
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
                    <button class="btn btn-warning"><i class="fa fa-pencil"></i></button>
                </div>
            </div>
            
            <div class="box-body">
                <p>@lang('name'): {{ $enrollment->student_data->firstname }} {{ $enrollment->student_data->lastname }}</p>
                <p>@lang('idnumber'): {{ $enrollment->student_data->idnumber }}</p>
                <p>@lang('address'): {{ $enrollment->student_data->address }}</p>
                
                @if (count($enrollment->student_data->phone) > 0)
                <p>@lang('phonenumber'):
                    <ul>
                        @foreach($enrollment->student_data->phone as $phone)
                        <li>{{ $phone->phone_number }}</li>
                        @endforeach
                    </ul>
                </p>
                @endif
                <p>@lang('email'): {{ $enrollment->student_data->email }}</p>
                <p>@lang('birthdate'): {{ $enrollment->student_data->birthdate }}</p>
                <p>@lang('age'): {{ $enrollment->student_data->student_age }} @lang('years old')</p>
            </div>
        </div>
    </div>
    
    

    <div class="col-md-4">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">
                        @lang('Course info')
                    </div>
                    <div class="box-tools pull-right">
                    </div>
                </div>
                
                <div class="box-body">
                        <p>@lang('Enrollment date'): {{ $enrollment->date }}</p>
                        <p>@lang('Enrollment ID'): {{ $enrollment->id }}</p>
                        <p>@lang('Course'): {{ $enrollment->course_data->name }}</p>
                        <p>@lang('Period'): {{ $enrollment->course_data->period->name }}</p>
                        
                        @if ($enrollment->children_count > 0)
                            <p>@lang('Children enrollments'):</p>
                            <ul>
                            @foreach ($enrollment->children as $children)
                                <li>
                                    <a href="{{ url('enrollment', $children->id) }}">{{ $children->course_data->name }}</a> ({{ $children->course_data->period->name }})
                                </li>
                            @endforeach
                            </ul>
                        @elseif ($enrollment->parent_id !== null)
                            @lang('This enrollment belongs to') <a href="{{ url('enrollment', $enrollment->parent_id) }}">{{ $enrollment->parent_id }}</a>
                        @endif
                        
                        @if(isset($enrollment->result))
                            <p>
                                <a href="/result/{{ $enrollment->id }}" class="btn btn-xs btn-info">
                                    <i class="fa fa-eye"></i> {{ $enrollment->result['result_name']['name'] }}
                                </a>
                            </p>
                        @endif
                </div>
            </div>
        </div>


    <div class="col-md-4">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang('comments')
                </div>
                <div class="box-tools pull-right">
                    {{-- <button class="btn btn-primary"><i class="fa fa-plus"></i></button>  todo allow adding comments --}}
                </div>
            </div>
            
            <div class="box-body">
                <ul>
                    @foreach ($comments as $comment)
                        <li>{{ $comment->body }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    


    
</div>
<div class="row">
    
<div class="col-md-4">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang('status')
                </div>
                <div class="box-tools pull-right">
                </div>
            </div>
            
            <div class="box-body">
                @if($enrollment->enrollment_status['id'] == 2)
                
                    <div class="label label-primary">
                        {{ $enrollment->enrollment_status['name'] }}
                    </div>
                    
                    @lang('Invoice(s)')
                    <ul>
                        @foreach ($enrollment->pre_invoice as $pre_invoice)
                        <li>
                            <a href="/invoice/{{ $pre_invoice->id }}">@lang('Numéro de facture'): {{ $pre_invoice->invoice_number ?? "inconnu" }}</a>
                    </li>
                        @endforeach
                    </ul>
                @elseif($enrollment->enrollment_status['id'] == 1)
                <p>@lang('This enrollment has not yet been canceled')</p>
                
                <a href="/enrollments/{{ $enrollment->id }}/bill" class="btn btn-primary">
                    @lang('Checkout')
                </a>
                
                <button class="btn btn-danger">
                    @lang('Delete Enrollment')
                </button>

            @endif
            </div>
        </div>
    </div>
    

</div>

@endsection
