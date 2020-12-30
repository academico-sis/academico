@extends('backpack::blank')

@section('after_styles')
<style media="screen">
    .backpack-profile-form .required::after {
        content: ' *';
        color: red;
    }
</style>
@endsection

@section('header')
<section class="container-fluid">

    <h2>
        {{ trans('backpack::base.my_account') }}
    </h2>

</section>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        @include('student.account.sidemenu')
    </div>
    <div class="col-md-8">

        <form class="form" action="/edit-account" method="post">

            {!! csrf_field() !!}

            <div class="card padding-10">

                <div class="card-body backpack-profile-form">

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
                            $label = trans('firstname');
                            $field = 'firstname'
                        @endphp
                        <label class="required">{{ $label }}</label>
                        <input required class="form-control" type="text" name="{{ $field }}" value="{{ old($field) ? old($field) : $user->$field }}">
                    </div>

                    <div class="form-group">
                            @php
                                $label = trans('lastname');
                                $field = 'lastname'
                            @endphp
                            <label class="required">{{ $label }}</label>
                            <input required class="form-control" type="text" name="{{ $field }}" value="{{ old($field) ? old($field) : $user->$field }}">
                    </div>

                    <div class="form-group">
                        @php
                            $label = config('backpack.base.authentication_column_name');
                            $field = backpack_authentication_column()
                        @endphp
                        <label class="required">{{ $label }}</label>
                        <input required class="form-control" type="{{ (backpack_authentication_column()=='email')?'email':'text' }}" name="{{ $field }}" value="{{ old($field) ? old($field) : $user->$field }}">
                    </div>

                    <div class="form-group m-b-0">
                        <button type="submit" class="btn btn-success"><span class="ladda-label"><i class="la la-save"></i> {{ trans('backpack::base.save') }}</span></button>
                        <a href="{{ backpack_url() }}" class="btn btn-default"><span class="ladda-label">{{ trans('backpack::base.cancel') }}</span></a>
                    </div>

                </div>
            </div>

        </form>

    </div><!-- /.col -->
</div>
@endsection
