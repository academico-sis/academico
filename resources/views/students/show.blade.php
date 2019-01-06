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
                <p>{{ ucfirst(trans_choice('academico.name', 1)) }}: {{ $student->firstname }} {{ $student->lastname }}</p>
                <p>{{ ucfirst(trans_choice('academico.idnumber', 1)) }}: {{ $student->idnumber }}</p>
                <p>{{ ucfirst(trans_choice('academico.address', 1)) }}: {{ $student->address }}</p>
                
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
                <p>{{ ucfirst(trans_choice('academico.age', 1)) }}: {{ $student->student_age }} {{ trans_choice('academico.yearsold', $student->student_age) }}</p>
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

    @if (count($student->enrollments) > 0)
        <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <div class="box-title">
                            {{ ucfirst(trans_choice('academico.enrollments', 2)) }}
                        </div>
                        <div class="box-tools pull-right">
                        </div>
                    </div>
                    
                    <div class="box-body">
                        <table class="table table-striped">
                            <thead>
                                <th>Fecha/hora</th>
                                <th>No Matricula</th>
                                <th>Curso</th> {{-- todo pop up with additional info --}}
                                <th>Periodo</th>
                                <th>Estado</th> {{-- todo click with invoice info --}}
                                <th>Resultado</th>
                            </thead>

                            <tbody>
                                @foreach($student->enrollments as $enrollment)
                                    <tr>
                                        <td>{{ $enrollment->date }}</td>
                                        <td>
                                            <a href="/enrollments/{{ $enrollment->id }}">
                                            {{ $enrollment->id }}
                                            </a>
                                        </td>
                                        <td>{{ $enrollment->course_data->name }}</td>
                                        <td>{{ $enrollment->course_data->period->name }}</td>
                                        <td>
                                            {{ $enrollment->enrollment_status->name }}
                                            @if($enrollment->enrollment_status->id == 1)
                                                <button class="btn btn-xs btn-primary"><i class="fa fa-dollar"></i></button>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $enrollment->result['result_name']['name'] }}
                                            <a href="/results/{{ $enrollment->id }}" class="btn btn-xs btn-info">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    @endif


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
