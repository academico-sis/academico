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

<form action="/update/2" method="post">
    @method('patch')
    @csrf

    <div class="row">

        <div class="col-lg-6 col-lg-offset-3">
            <div class="box">
                <div class="box-body">

                    <div class="form-group">
                        <label for="birthdate">@lang('Birthate')</label>
                        <input class="form-control input-lg" id="birthdate" name="birthdate" type="date" value="{{ $user->student->birthdate }}">
                    </div>

                    <div class="form-group">
                        <label for="address">@lang('Address')</label>
                        <input class="form-control input-lg" id="address" name="address" type="text" value="{{ $user->student->address }}">
                    </div>

                    <div class="form-group">
                        <label for="idnumber">@lang('ID Number')</label>
                        <input class="form-control input-lg" id="idnumber" name="idnumber" type="text" value="{{ $user->student->idnumber }}">
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
