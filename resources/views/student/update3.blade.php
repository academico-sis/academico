@extends('backpack::layout')

@section('header')
    <section class="content-header">
      <h1 class="text-center">
        @lang('Please update your data')
      </h1>

      <h6 class="text-center">
        <span>Personal Data</span> > <span class="text-bold">Phone numbers</span> > <span>Profession</span> > <span>Photo</span> > <span>Additional Contacts</span>
      </h6>

    </section>
@endsection


@section('content')
    <div class="row">

        <div class="col-lg-6 col-lg-offset-3">
            <div class="box">
                <div class="box-body text-center">

                    <h4>@lang('Please check your personal phone number(s)')</h4>
                    <h4>@lang('This is important, so that we can reach you in case of an emergency')</h4>

                    @forelse ($user->student->phone as $phoneable)
                        <ul>
                            <li class="input-lg">{{ $phoneable->phone_number }}</li>
                        </ul>    
                    @empty
                        @lang('No phone numbers registered')
                    @endforelse

                    @lang('Add new')

                </div>
            </div>
        </div>

            <div class="col-lg-6 col-lg-offset-3 text-center">
                <button type="submit" class="btn btn-lg btn-success"><span class="ladda-label"><i class="fa fa-save"></i> @lang('Next')</span></button>
            </div>
        </div>

    </div>
@endsection
