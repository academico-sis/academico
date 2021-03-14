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
<div class="row" id="app">

    <div class="col-md-4">
        @include('student.account.sidemenu')
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-body text-center">
                <h4>@lang('Please check your personal phone number(s)')</h4>
                <h4>@lang('This is important, so that we can reach you in case of an emergency')</h4>

                <phone-number-update-component :student="{{ $user->student->id }}"></phone-number-update-component>

            </div>
        </div>

        <form class="form" action="/edit-phone" method="post">

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

                    <div class="form-group m-b-0">
                        <button type="submit" class="btn btn-success"><span class="ladda-label"><i class="la la-save"></i> {{ trans('backpack::base.save') }}</span></button>
                    </div>

                </div>
            </div>

        </form>
    </div>

</div>
@endsection

@section('after_scripts')
        <script src="{{ mix('/js/app.js') }}"></script>
    <script src="{{ mix('/js/manifest.js') }}"></script>
    <script src="{{ mix('/js/vendor.js') }}"></script>
@endsection