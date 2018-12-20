@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
            {{ $student->name }}
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
                </div>
            </div>
            
            <div class="box-body">           
                <p>{{ ucfirst(trans_choice('academico.name', 1)) }}: {{ $student->name }}</p>
                <p>{{ ucfirst(trans_choice('academico.idnumber', 1)) }}: {{ $student->idnumber }}</p>
                <p>{{ ucfirst(trans_choice('academico.address', 1)) }}: </p> {{-- todo --}}
                <p>{{ ucfirst(trans_choice('academico.phonenumer', 1)) }}: </p> {{-- todo --}}
                <p>{{ ucfirst(trans_choice('academico.email', 1)) }}: {{ $student->email }}</p>
                <p>{{ ucfirst(trans_choice('academico.birthdate', 1)) }}: {{ $student->birthdate }}</p>
                <p>{{ ucfirst(trans_choice('academico.age', 1)) }}: {{ $student->age }} {{ trans_choice('academico.yearsold', $student->age) }}</p>
            </div>
        </div>
    </div>

    @isset($student->invoicable)
    <div class="col-md-4">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">
                        {{ ucfirst(trans_choice('academico.invoice_info', 1)) }}
                    </div>

                    <div class="box-tools pull-right">
                    </div>
                </div>
                
                <div class="box-body">           
                    <p>{{ ucfirst(trans_choice('academico.name', 1)) }}: {{ $student->invoicable->name }}</p>
                    <p>{{ ucfirst(trans_choice('academico.idnumber', 1)) }}: {{ $student->invoicable->idnumber }}</p>
                    <p>{{ ucfirst(trans_choice('academico.address', 1)) }}: </p> {{-- todo --}}
                    <p>{{ ucfirst(trans_choice('academico.phonenumer', 1)) }}: </p> {{-- todo --}}
                    <p>{{ ucfirst(trans_choice('academico.email', 1)) }}: {{ $student->invoicable->email }}</p>
                </div>
            </div>
        </div>
    @endisset


    @if (count($student->administrative_comments) > 0)
        <div class="col-md-4">
                <div class="box">
                    <div class="box-header with-border">
                        <div class="box-title">
                            {{ ucfirst(trans_choice('academico.student_adm_comments', 1)) }}
                        </div>
                        <div class="box-tools pull-right">
                        </div>
                    </div>
                    
                    <div class="box-body">           
                        @foreach($student->administrative_comments as $comment)
                            <p>{{ $comment->body }} ({{ $comment->date }})</p>
                        @endforeach
                    </div>
                </div>
            </div>
    @endif


</div>


<div class="row">

@if (count($student->pedagogical_comments) > 0)
        <div class="col-md-4">
                <div class="box">
                    <div class="box-header with-border">
                        <div class="box-title">
                            {{ ucfirst(trans_choice('academico.student_ped_comments', 1)) }}
                        </div>
                        <div class="box-tools pull-right">
                        </div>
                    </div>
                    
                    <div class="box-body">           
                        @foreach($student->administrative_comments as $comment)
                            <p>{{ $comment->body }} ({{ $comment->date }})</p>
                        @endforeach
                    </div>
                </div>
            </div>
    @endif

</div>


@endsection
