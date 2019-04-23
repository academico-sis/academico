@extends('backpack::layout')

@section('header')
    <section class="content-header">
      <h1 class="text-center">
        @lang('Please update your data')
      </h1>

      <h6 class="text-center">
        <span>Personal Data</span> > <span>Phone numbers</span> > <span class="text-bold">Profession</span> > <span>Photo</span> > <span>Additional Contacts</span>
      </h6>

    </section>
@endsection


@section('content')
    <div class="row">

        <div class="col-lg-6 col-lg-offset-3">
            <div class="box">
                <div class="box-body text-center">

                    <h4>@lang("Please fill in your profession and your institution (school, workplace). You may leave these fields blank if they do not apply to your situation")</h4>


                </div>
            </div>
        </div>

            <div class="col-lg-6 col-lg-offset-3 text-center">
                <button type="submit" class="btn btn-lg btn-success"><span class="ladda-label"><i class="fa fa-save"></i> @lang('Next')</span></button>
            </div>
        </div>

    </div>
@endsection
