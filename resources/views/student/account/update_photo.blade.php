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

    <ol class="breadcrumb">

        <li class="breadcrumb-item">
            <a href="{{ backpack_url() }}">{{ config('backpack.base.project_name') }}</a>
        </li>

        <li class="breadcrumb-item">
            <a href="{{ route('backpack.account.edit_info') }}">{{ trans('backpack::base.my_account') }}</a>
        </li>

        <li class="breadcrumb-item active">
            {{ trans('backpack::base.update_account_info') }}
        </li>

    </ol>

</section>
@endsection

@section('content')
<div class="row">
    <div class="col-md-3">
        @include('student.account.sidemenu')
    </div>
    <div class="col-md-6">

        <form action="/edit-photo" method="post" enctype="multipart/form-data">

            @csrf

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

                    <div class="card">
                        <div class="card-body">

                            <h4>@lang('Please chose an image on your computer to update your profile picture')</h4>
        
                                <input type="file" name="fileToUpload" id="fileToUpload">
        
                        </div>
                    </div>

                    <div class="form-group m-b-0">
                        <button type="submit" class="btn btn-success"><span class="ladda-label"><i class="la la-save"></i> {{ trans('backpack::base.save') }}</span></button>

                        @lang('or') <button type="submit" class="btn btn-primary"><span class="ladda-label">@lang('Continue without uploading a profile picture')</span></button>
                    </div>

                </div>
            </div>

        </form>

    </div>
</div>
@endsection
