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
