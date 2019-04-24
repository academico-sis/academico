@extends('backpack::layout')

@section('header')
    <section class="content-header">
      <h1 class="text-center">
        @lang('Please update your data')
      </h1>

      <h6 class="text-center">
        <span>Personal Data</span> > <span>Phone numbers</span> > <span>Profession</span> > <span class="text-bold">Photo</span> > <span>Additional Contacts</span>
      </h6>

    </section>
@endsection


@section('content')
<form action="/update/5" method="post" enctype="multipart/form-data">

    @method('patch')
    @csrf
    
    <div class="row">

        <div class="col-lg-6 col-lg-offset-3">
            <div class="box">
                <div class="box-body text-center">

                    <h4>@lang('Please upload an ID picture. This step is recommended but not mandatory')</h4>

                        <input type="file" name="fileToUpload" id="fileToUpload">

                </div>
            </div>
        </div>

            <div class="col-lg-6 col-lg-offset-3 text-center">
                <button type="submit" class="btn btn-lg btn-success"><span class="ladda-label"><i class="fa fa-save"></i> @lang('Next')</span></button>
            </div>
        </div>

    </div>
</form>

@endsection
