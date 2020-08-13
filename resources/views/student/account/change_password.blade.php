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
<section class="content-header">

    <h1>
        {{ trans('backpack::base.my_account') }}
    </h1>

</section>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        @include('student.account.sidemenu')
    </div>
    <div class="col md-8">

        {{-- CHANGE PASSWORD FORM --}}
                <form class="form" action="/change-password" method="post">
    
                    {!! csrf_field() !!}
    
                    <div class="card padding-10">
    
                        <div class="card-header">
                            {{ trans('backpack::base.change_password') }}
                        </div>
    
                        <div class="card-body backpack-profile-form bold-labels">
                            <div class="form-group">
                                @php
                                    $label = trans('backpack::base.new_password');
                                    $field = 'password';
                                @endphp
                                <label class="required">{{ $label }}</label>
                                <input autocomplete="new-password" required class="form-control" type="password" name="{{ $field }}" id="{{ $field }}" value="">
                            </div>

                            <div class="form-group">
                                @php
                                    $label = trans('backpack::base.confirm_password');
                                    $field = 'password_confirmation';
                                @endphp
                                <label class="required">{{ $label }}</label>
                                <input autocomplete="new-password" required class="form-control" type="password" name="{{ $field }}" id="{{ $field }}" value="">
                            </div>
                        </div>
    
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success"><i class="la la-save"></i> {{ trans('backpack::base.change_password') }}</button>
                        </div>
    
                    </div>
    
                </form>
            </div>
</div>
@endsection
