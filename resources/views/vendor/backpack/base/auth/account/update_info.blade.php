@extends('backpack::layout')

@section('after_styles')
<style media="screen">
    .backpack-profile-form .required::after {
        content: ' *';
        color: red;
    }
</style>
@endsection

@section('header')
<section class="content-header">

    <h1>
        {{ trans('backpack::base.my_account') }}
    </h1>

    <ol class="breadcrumb">

        <li>
            <a href="{{ backpack_url() }}">{{ config('backpack.base.project_name') }}</a>
        </li>

        <li>
            <a href="{{ route('backpack.account.info') }}">{{ trans('backpack::base.my_account') }}</a>
        </li>

        <li class="active">
            {{ trans('backpack::base.update_account_info') }}
        </li>

    </ol>

</section>
@endsection

@if ($user->isStudent())

@section('content')
<div class="row">
    <div class="col-md-3">
        @include('backpack::auth.account.sidemenu')
    </div>
    <div class="col-md-5">

        <form class="form" action="{{ route('backpack.account.info') }}" method="post">

            {!! csrf_field() !!}

            <div class="box padding-10">

                <div class="box-body backpack-profile-form">

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->count())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group">
                        @php
                            $field = 'firstname';
                        @endphp
                        <label class="required">@lang('Firstname')</label>
                        <input required class="form-control" type="text" name="{{ $field }}" value="{{ old($field) ? old($field) : $user->$field }}">
                    </div>

                    <div class="form-group">
                            @php
                                $field = 'lastname';
                            @endphp
                            <label class="required">@lang('Lastname')</label>
                            <input required class="form-control" type="text" name="{{ $field }}" value="{{ old($field) ? old($field) : $user->$field }}">
                    </div>

                    <div class="form-group">
                        @php
                            $label = config('backpack.base.authentication_column_name');
                            $field = backpack_authentication_column();
                        @endphp
                        <label class="required">{{ $label }}</label>
                        <input required class="form-control" type="{{ backpack_authentication_column()=='email'?'email':'text' }}" name="{{ $field }}" value="{{ old($field) ? old($field) : $user->$field }}">
                    </div>

                    <div class="form-group">
                        @php
                            $field = 'idnumber';
                        @endphp
                        <label class="required">@lang('ID Number')</label>
                        <input required class="form-control" type="text" name="{{ $field }}" value="{{ old($field) ? old($field) : $user->$field }}">
                </div>

                <div class="form-group">
                    @php
                        $field = 'address';
                    @endphp
                    <label class="required">@lang('Address')</label>
                    <input required class="form-control" type="text" name="{{ $field }}" value="{{ old($field) ? old($field) : $user->$field }}">
            </div>


            <div class="form-group">
                @php
                    $field = 'birthdate';
                @endphp
                <label class="required">@lang('Birthdate')</label>
                <input required class="form-control" type="date" name="{{ $field }}" value="{{ old($field) ? old($field) : $user->$field }}">
        </div>

        

                    <div class="form-group m-b-0">
                        <button type="submit" class="btn btn-success"><span class="ladda-label"><i class="fa fa-save"></i> {{ trans('backpack::base.save') }}</span></button>
                        <a href="{{ backpack_url() }}" class="btn btn-default"><span class="ladda-label">{{ trans('backpack::base.cancel') }}</span></a>
                    </div>

                </div>
            </div>

        </form>

    </div>

    <div class="col-md-4">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">
                        @lang('New Additional Contact')
                    </div>

                    <div class="box-tools pull-right">
                        <a class="btn btn-xs btn-primary" data-toggle="modal" data-target="#userDataModal">
                                <i class="fa fa-plus"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>

    @foreach ($user->student->contacts as $contact)
    <div class="col-md-4">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">
                        @lang('Additional Contact')
                        @if(isset($contact->relationship))
                        ({{ $contact->relationship->name }})
                        @endif
                    </div>

                    <div class="box-tools pull-right">
                            <a class="btn btn-xs btn-warning" href="/contact/{{$contact->id}}/edit">
                                <i class="fa fa-edit"></i>
                            </a>

                    </div>
                </div>
                
                <div class="box-body">
                    @include('students.additional_info')
                </div>
            </div>
        </div>
    @endforeach

</div>


    <!-- Add additional user data Modal-->
    <div class="modal fade" id="userDataModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">@lang('Add a new contact')</h4>
            </div>
            <div class="modal-body">
              
                <form action="{{ route('addContact') }}" method="post">
                @csrf
                <input type="hidden" name="student_id" value="{{ $user->student->id }}">
                
                <div class="form-group">
                    <label for="firstname">@lang('Firstname')</label>
                    <input type="text" id="firstname" name="firstname">
                </div>

                <div class="form-group">
                    <label for="lastname">@lang('Name')</label>
                    <input type="text" id="lastname" name="lastname">
                </div>

                <div class="form-group">
                    <label for="email">@lang('Email')</label>
                    <input type="text" id="email" name="email">
            </div>

            <div class="form-group">
                <label for="address">@lang('Address')</label>
                <input type="text" id="address" name="address">
            </div>

            <div class="form-group">
                <label for="phone_number">@lang('Phone Number')</label>
                <input type="text" id="phone_number" name="phone_number">
            </div>

            <div class="form-group">
                <label for="idnumber">@lang('ID Number')</label>
                <input type="text" id="idnumber" name="idnumber">
            </div>


            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">@lang('Close')</button>
              <button type="submit" class="btn btn-success">@lang('Save new Contact')</button>
            </div>
        </form>
          </div>
        </div>
      </div>
    </div>
@endsection

@endif
