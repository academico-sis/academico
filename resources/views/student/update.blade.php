@extends('backpack::layout')

@section('header')
    <section class="content-header">
      <h1 class="text-center">
        @lang('Please update your data')
      </h1>

      <h6 class="text-center">
            <span class="text-bold">Personal Data</span> > <span>Phone numbers</span> > <span>Profession</span> > <span>Photo</span> > <span>Additional Contacts</span>
    </h6>
    </section>
@endsection


@section('content')
    <form action="/update" method="post">
    @method('patch')
    @csrf

    <div class="row">

        <div class="col-lg-6 col-lg-offset-3">
            <div class="box">
                <div class="box-body">
                    <div class="form-group">
                        <label for="firstname">@lang('First Name')</label>
                        <input class="form-control input-lg" id="firstname" name="firstname" type="text" value="{{ $user->firstname }}">
                    </div>

                    <div class="form-group">
                        <label for="lastname">@lang('Last Name')</label>
                        <input class="form-control input-lg" id="lastname" name="lastname" type="text" value="{{ $user->lastname }}">
                    </div>

                    <div class="form-group">
                        <label for="email">@lang('Email')</label>
                        <input class="form-control input-lg" id="email" name="email" type="text" value="{{ $user->email }}">
                    </div>

                </div>
            </div>
            </div>

            <div class="col-lg-6 col-lg-offset-3 text-center">
                <button type="submit" class="btn btn-lg btn-success"><span class="ladda-label"><i class="fa fa-save"></i> @lang('Confirm')</span></button>
            </div>
        </div>
    </form>
@endsection
