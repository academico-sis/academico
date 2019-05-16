@extends('backpack::layout')

@section('after_styles')
<style media="screen">
    .backpack-profile-form .required::after {
        content: ' *';
        color: red;
    }
</style>
@endsection

@section('header')
<section class="content-header">

    <h1>
        {{ trans('backpack::base.my_account') }}
    </h1>

    <ol class="breadcrumb">

        <li>
            <a href="{{ backpack_url() }}">{{ config('backpack.base.project_name') }}</a>
        </li>

        <li>
            <a href="{{ route('backpack.account.info') }}">{{ trans('backpack::base.my_account') }}</a>
        </li>

        <li class="active">
            {{ trans('backpack::base.update_account_info') }}
        </li>

    </ol>

</section>
@endsection

@section('content')
<div class="row" id="app">
    <div class="col-md-3">
        @include('backpack::auth.account.sidemenu')
    </div>

    <div class="col-md-9">
        <div class="box">
            <div class="box-body text-center">

                <h4>@lang('Please check the additional contact data associated to your account')</h4>

                <h4>@lang('This data is used for invoices')</h4>

                <h4>@lang('Students under 18, please add contact data from your legal representatives')</h4>

                <h4>@lang('You may add more than contact to your account')</h4>

                <a class="btn btn btn-primary" data-toggle="modal" data-target="#userDataModal">
                    <i class="fa fa-plus"></i> @lang('Create another Contact')
                </a>

            </div>
        </div>
    </div>


        @foreach ($user->student->contacts as $contact)

        <div class="col-md-4">
                <div class="box">
                    <div class="box-header with-border">
                        <div class="box-title">
                            @lang('Additional Contact')
                            @if(isset($contact->relationship))
                            ({{ $contact->relationship->name }})
                            @endif
                        </div>

                        <div class="box-tools pull-right">
                                <a class="btn btn btn-warning" href="/contact/{{$contact->id}}/edit">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                
                        </div>
                    </div>
                    
                    <div class="box-body">

                            <p>@lang('Firstname') : 
                            <span class="input-lg" id="firstname">{{ $contact->firstname }}</span></p>

                            <p>@lang('Lastname') :
                            <span class="input-lg" id="lastname">{{ $contact->lastname }}</span></p>
                    

                            <p>@lang('ID Number') :
                            <span class="input-lg" id="idnumber">{{ $contact->idnumber }}</span></p>
                    

                            <p>@lang('Address') :
                            <span class="input-lg" id="address">{{ $contact->address }}</span></p>
                    

                            <p>@lang('Email') :
                            <span class="input-lg" id="email">{{ $contact->email }}</span></p>
                
                        <contact-phone-number-update-component :contact="{{ $contact->id }}"></contact-phone-number-update-component>

                    </div>
                </div>
            </div>
        @endforeach

        <div class="col-md-3">
                <div class="box">
                    <div class="box-body text-center">
                        <h4>@lang('When everything is ready, please confirm that your data is up-to-date')</h4>
        
                        <form action="/edit-contacts" method="post">
                            @method('patch')
                            @csrf
                                <button class="btn btn-lg btn-success" type='submit'><i class="fa fa-check"></i> @lang('Finish update')</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>

    <!-- Add additional user data Modal-->
    <div class="modal fade" id="userDataModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">@lang('Add a new contact')</h4>
            </div>
            <div class="modal-body">
              
                <form action="{{ route('addContact') }}" method="post">
                @csrf
                <input type="hidden" name="student_id" value="{{ $user->student->id }}">
                
                <div class="form-group">
                    <label for="firstname">@lang('Firstname')</label>
                    <input type="text" id="firstname" name="firstname">
                </div>

                <div class="form-group">
                    <label for="lastname">@lang('Name')</label>
                    <input type="text" id="lastname" name="lastname">
                </div>

                <div class="form-group">
                    <label for="email">@lang('Email')</label>
                    <input type="text" id="email" name="email">
            </div>

            <div class="form-group">
                <label for="address">@lang('Address')</label>
                <input type="text" id="address" name="address">
            </div>

            <div class="form-group">
                <label for="phone_number">@lang('Phone Number')</label>
                <input type="text" id="phone_number" name="phone_number">
            </div>

            <div class="form-group">
                <label for="idnumber">@lang('ID Number')</label>
                <input type="text" id="idnumber" name="idnumber">
            </div>


            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">@lang('Close')</button>
              <button type="submit" class="btn btn-success">@lang('Save new Contact')</button>
            </div>
        </form>
          </div>
        </div>
      </div>
    </div>


@endsection

@section('after_scripts')
    <script src="/js/app.js"></script>   
@endsection
