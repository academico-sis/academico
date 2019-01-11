@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        {{ $student->name }}
    </h1>
</section>
@endsection


@section('content')
<div id="app">

<div class="row">

    <div class="col-md-4">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang('student_info')
                </div>
                <div class="box-tools pull-right">
                    <a class="btn btn-xs btn-warning" href="/student/{{$student->id}}/edit">
                        <i class="fa fa-edit"></i>
                    </a>

                    <a class="btn btn-xs btn-primary" data-toggle="modal" data-target="#userDataModal">
                        <i class="fa fa-plus"></i>
                    </a>

                </div>
            </div>
            
            <div class="box-body">           
                <p>@lang('name'): {{ $student->firstname }} {{ $student->lastname }}</p>
                <p>@lang('idnumber'): {{ $student->idnumber }}</p>
                <p>@lang('address'): {{ $student->address }}</p>
                
                @if (count($student->phone) > 0)
                    <p>@lang('phonenumber'):
                        <ul>
                            @foreach($student->phone as $phone)
                            <li>{{ $phone->phone_number }}</li>
                            @endforeach
                        </ul>
                    </p>
                @endif
                <p>@lang('email'): {{ $student->email }}</p>
                <p>@lang('birthdate'): {{ $student->birthdate }}</p>
                <p>@lang('age'): {{ $student->student_age }} @lang('yearsold')</p>
            </div>
        </div>
    </div>

    @foreach ($student->additional_data as $additional_data)
    <div class="col-md-4">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">
                        @lang('additional_info')
                        @if(isset($additional_data->relationship))
                        ({{ $additional_data->relationship->name }})
                        @endif
                    </div>

                    <div class="box-tools pull-right">
                        <a class="btn btn-xs btn-warning" href="/userdata/{{$additional_data->id}}/edit">
                            <i class="fa fa-edit"></i>
                        </a>
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


        <div class="col-md-4">
            <student-comments
                :comments="{{ json_encode($administrative_comments) }}"
                :student="{{ json_encode($student) }}">
            </student-comments> 
        </div>



</div>


<div class="row">

    @if (count($student->enrollments) > 0)
        <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <div class="box-title">
                            @lang('enrollments')
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
                                            <a href="/enrollment/{{ $enrollment->id }}"> {{-- todo clean url --}}
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


    <div class="col-md-4">
            <student-comments
            :comments="{{ json_encode($educational_comments) }}"
            :student="{{ json_encode($student) }}">
        </student-comments> 
    </div>

</div>
</div>

    <!-- Add additional user data Modal-->
    <div class="modal fade" id="userDataModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Add a new contact</h4>
            </div>
            <div class="modal-body">
              
                <form action="/userdata" method="post">
                @csrf
                <input type="hidden" name="user_id" value="{{ $student->id }}">
                
                <div class="form-group">
                    <label for="firstname">prénom</label>
                    <input type="text" id="firstname" name="firstname">
                </div>

                <div class="form-group">
                    <label for="lastname">Nom</label>
                    <input type="text" id="lastname" name="lastname">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" id="email" name="email">
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address">
            </div>

            <div class="form-group">
                <label for="idnumber">ID Number</label>
                <input type="text" id="idnumber" name="idnumber">
            </div>


            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-success">Save new Contact</button>
            </div>
        </form>
          </div>
        </div>
      </div>
    </div>

@endsection

@section('after_scripts')
    <script src="/js/app.js"></script>

    
@endsection
