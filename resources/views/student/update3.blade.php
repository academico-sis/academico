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
    <div class="row" id="app">

        <div class="col-lg-6 col-lg-offset-3">
            <div class="box">
                <div class="box-body text-center">
                    <h4>@lang('Please check your personal phone number(s)')</h4>
                    <h4>@lang('This is important, so that we can reach you in case of an emergency')</h4>

                    <phone-number-update-component :student="{{$user->student->id }}"></phone-number-update-component>

                </div>
            </div>
        </div>

            <div class="col-lg-6 col-lg-offset-3 text-center">
            <form action="/update/3" method="post">
                @method('patch')
                @csrf
                    <button class="btn btn-lg btn-success" type='submit'><span class="ladda-label">@lang('Next')</span></button>
            </form>
            </div>
        </div>

    </div>
@endsection

@section('after_scripts')
    <script src="/js/app.js"></script>   
@endsection
