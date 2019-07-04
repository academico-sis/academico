@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang('Enrollment Details')
    </h1>
</section>
@endsection


@section('content')

<div class="row" id="app">
    
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
                <p>@lang('name'): 
                    <a href="{{ url('student') }}/{{$enrollment->student->id }}">{{ $enrollment->student->firstname }} {{ $enrollment->student->lastname }}</a>
                </p>
                <p>@lang('idnumber'): {{ $enrollment->student->idnumber }}</p>
                <p>@lang('address'): {{ $enrollment->student->address }}</p>
                
                @if (count($enrollment->student->phone) > 0)
                <p>@lang('Phone Number'):
                    <ul>
                        @foreach($enrollment->student->phone as $phone)
                        <li>{{ $phone->phone_number }}</li>
                        @endforeach
                    </ul>
                </p>
                @endif
                <p>@lang('email'): {{ $enrollment->student->email }}</p>
                <p>@lang('birthdate'): {{ $enrollment->student->birthdate }}</p>
                <p>@lang('age'): {{ $enrollment->student_age }} @lang('years old')</p>
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
                        <p>@lang('Course'): {{ $enrollment->course->name }}</p>
                        <p>@lang('Period'): {{ $enrollment->course->period->name }}</p>
                        
                        @if ($enrollment->children_count > 0)
                            <p>@lang('Children enrollments'):</p>
                            <ul>
                            @foreach ($enrollment->children as $children)
                                <li>
                                    <a href="{{ url('enrollment', $children->id) }}">{{ $children->course->name }}</a> ({{ $children->course->period->name }})
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

        <student-comments
            :comments="{{ json_encode($enrollment->comments) }}"
            :id="{{ json_encode($enrollment->id) }}"
            :type="'App\\Models\\Enrollment'"
            route="{{ route('storeComment') }}">
        </student-comments>

    </div>
    

    
<div class="col-md-4">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang('Status')
                </div>
                <div class="box-tools pull-right">
                </div>
            </div>
            
            <div class="box-body">
                @if($enrollment->status_id == 2)
                
                    <div class="label label-primary">
                            {{ $enrollment->enrollmentStatus->name }}
                    </div>
                    

                @elseif($enrollment->status_id == 1)

                <div class="label label-warning">
                    {{ $enrollment->enrollmentStatus->name }}
                </div>
                
                <div>
                    @if ($enrollment->parent_id == null)
                        <a href="/enrollments/{{ $enrollment->id }}/bill" class="btn btn-primary">@lang('Checkout')</a>
                    @endif
                </div>

                @if(backpack_user()->can('enrollments.edit'))
                    {{-- todo translate and improve the confirmation message --}}
                    <button type="submit" class="btn btn-danger" onclick="if(confirm('Voulez-vous vraiment supprimer cette inscription ?')) cancel({{ $enrollment->id }})">
                        @lang('Delete Enrollment')
                    </button>
                @endif
                
            @else
                {{ $enrollment->enrollmentStatus->name }}
            @endif
            </div>
        </div>
    </div>
    

</div>

@if ($enrollment->pre_invoice()->count() > 0)
    {{-- @include('invoices.show') --}}
@endif

@endsection


@section('before_scripts')
<script>
        function cancel(enrollment)
            {
                axios.delete('/enrollment/'+enrollment)
                    .then(function (response) {
                        document.location.reload(true);
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            }
    </script>

@endsection

@section('after_scripts')
    <script src="/js/app.js"></script>
@endsection
