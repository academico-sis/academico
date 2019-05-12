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

@section('content')
<div class="row" id="app">

    <div class="col-md-3">
        @include('backpack::auth.account.sidemenu')
    </div>

    <div class="col-md-6">
        <div class="box">
            <div class="box-body text-center">
                <h4>@lang('Please check your personal phone number(s)')</h4>
                <h4>@lang('This is important, so that we can reach you in case of an emergency')</h4>

                <phone-number-update-component :student="{{ $user->student->id }}"></phone-number-update-component>

            </div>
        </div>
    </div>

    <div class="col-md-6 text-center">
        <form action="/update/3" method="post">
            @method('patch')
            @csrf
                <button class="btn btn-lg btn-success" type='submit'><span class="ladda-label">@lang('Next')</span></button>
        </form>
    </div>

</div>
@endsection

@section('after_scripts')
    <script src="/js/app.js"></script>
@endsection