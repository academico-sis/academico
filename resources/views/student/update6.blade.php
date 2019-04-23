@extends('backpack::layout')

@section('header')
    <section class="content-header">
      <h1 class="text-center">
        @lang('Please update your data')
      </h1>

      <h6 class="text-center">
        <span>Personal Data</span> > <span>Phone numbers</span> > <span>Profession</span> > <span>Photo</span> > <span class="text-bold">Additional Contacts</span>
      </h6>

    </section>
@endsection


@section('content')
    <div class="row">

        <div class="col-lg-6 col-lg-offset-3">
            <div class="box">
                <div class="box-body text-center">

                    <h4>@lang('Please check the additional contact data associated to your account')</h4>


                </div>
            </div>
        </div>

            <div class="col-lg-6 col-lg-offset-3 text-center">
                <button type="submit" class="btn btn-lg btn-success"><span class="ladda-label"><i class="fa fa-save"></i> @lang('Next')</span></button>
            </div>
        </div>

    </div>
@endsection
